<?php

namespace App\Http\Controllers;

use App\Services\ChatGrpcService;
use App\Models\ChatGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    protected ChatGrpcService $grpcService;

    public function __construct(ChatGrpcService $grpcService)
    {
        $this->grpcService = $grpcService;
        Log::info('[ChatController] Using gRPC Service for all operations');
    }

    // Get all groups for current user (via gRPC)
    public function index()
    {
        try {
            $userId = Auth::id();
            
            // Call gRPC service instead of direct database access
            $groups = $this->grpcService->getUserGroups($userId);
            
            // Transform response to match expected format
            $transformedGroups = collect($groups)->map(function ($group) {
                return [
                    'id' => $group['id'],
                    'name' => $group['name'],
                    'description' => $group['description'],
                    'member_count' => $group['member_count'],
                    'created_at' => date('c', $group['created_at']), // ISO 8601 format
                ];
            });

            return response()->json([
                'success' => true,
                'groups' => $transformedGroups,
            ]);
            
        } catch (\Exception $e) {
            Log::error('[ChatController] Error in index', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch groups',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Get all available groups (public groups user can join)
    public function availableGroups()
    {
        $user = Auth::user();
        
        // Get all groups where user is not a member yet
        $groups = ChatGroup::whereDoesntHave('members', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->withCount('members')  // Only count, don't load all members
        ->select('id', 'name', 'description', 'created_at')  // Only select needed columns
        ->latest()
        ->get()
        ->map(function ($group) {
            return [
                'id' => $group->id,
                'name' => $group->name,
                'description' => $group->description,
                'member_count' => $group->members_count,
                'created_at' => $group->created_at->toISOString(),
            ];
        });

        return response()->json([
            'success' => true,
            'groups' => $groups,
        ]);
    }

    // Create new group (via gRPC)
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'member_ids' => 'required|array|min:1',
                'member_ids.*' => 'exists:users,id',
            ]);

            // Call gRPC service to create group
            $group = $this->grpcService->createGroup(
                $validated['name'],
                $validated['description'] ?? '',
                $validated['member_ids']
            );

            // Get members info for response
            $members = User::whereIn('id', array_merge([Auth::id()], $validated['member_ids']))
                ->select('id', 'name', 'email')
                ->get()
                ->map(fn($m) => [
                    'id' => $m->id,
                    'name' => $m->name,
                    'email' => $m->email,
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Group created successfully via gRPC',
                'group' => [
                    'id' => $group['id'],
                    'name' => $group['name'],
                    'description' => $group['description'],
                    'members' => $members,
                    'created_at' => date('c', $group['created_at']),
                ],
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('[ChatController] Error in store', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create group',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Get group details with members
    public function show($id)
    {
        $group = ChatGroup::with('members')->findOrFail($id);
        
        // Check if user is member
        $user = Auth::user();
        if (!$group->members->contains($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not a member of this group',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'group' => [
                'id' => $group->id,
                'name' => $group->name,
                'description' => $group->description,
                'members' => $group->members->map(fn($m) => [
                    'id' => $m->id,
                    'name' => $m->name,
                    'email' => $m->email,
                ]),
                'created_at' => $group->created_at->toISOString(),
            ],
        ]);
    }

    // Join existing group
    public function join($id)
    {
        $group = ChatGroup::findOrFail($id);
        $user = Auth::user();

        // Check if already a member
        if ($group->members->contains($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'You are already a member of this group',
            ], 400);
        }

        // Add user to group
        $group->members()->attach($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Successfully joined the group',
        ]);
    }

    // Leave group (via gRPC)
    public function leave($id)
    {
        try {
            $userId = Auth::id();
            
            // Call gRPC service  
            $result = $this->grpcService->leaveGroup($id, $userId);
            
            return response()->json($result, $result['success'] ? 200 : 400);
            
        } catch (\Exception $e) {
            Log::error('[ChatController] Error in leave', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to leave group',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Get messages for a group (via gRPC)
    public function messages($id)
    {
        try {
            // Check membership via database (for auth)
            $group = ChatGroup::findOrFail($id);
            $user = Auth::user();

            if (!$group->members->contains($user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not a member of this group',
                ], 403);
            }

            // Get messages via gRPC service
            $response = $this->grpcService->getMessageHistory($id, 100);
            
            // Transform messages to match frontend format
            $messages = collect($response['messages'])->map(function ($msg) use ($user) {
                return [
                    'id' => $msg['id'],
                    'user_id' => $msg['user_id'],
                    'user_name' => $msg['user_name'],
                    'message' => $msg['content'],
                    'type' => $msg['type'],
                    'is_own' => $msg['user_id'] === $user->id,
                    'created_at' => date('c', $msg['timestamp']),
                ];
            });

            return response()->json([
                'success' => true,
                'messages' => $messages,
            ]);
            
        } catch (\Exception $e) {
            Log::error('[ChatController] Error in messages', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch messages',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Send message to group (via gRPC)
    public function sendMessage(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'message' => 'required|string',
                'type' => 'nullable|string|in:text,image,file',
            ]);

            // Check membership
            $group = ChatGroup::findOrFail($id);
            $user = Auth::user();

            if (!$group->members->contains($user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not a member of this group',
                ], 403);
            }

            // Send message via gRPC service
            $response = $this->grpcService->sendMessage(
                $id,
                $user->id,
                $validated['message'],
                $validated['type'] ?? 'text'
            );

            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $response['id'],
                    'user_id' => $response['user_id'],
                    'user_name' => $response['user_name'],
                    'message' => $response['content'],
                    'type' => $response['type'],
                    'is_own' => $response['user_id'] === $user->id,
                    'created_at' => date('c', $response['timestamp']),
                ],
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('[ChatController] Error in sendMessage', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // Get all users for group creation
    public function users()
    {
        $currentUser = Auth::user();
        
        // Cache users for 5 minutes
        $users = cache()->remember('chat_users_' . $currentUser->id, 300, function () use ($currentUser) {
            return User::where('id', '!=', $currentUser->id)
                ->select('id', 'name', 'email')
                ->orderBy('name')
                ->get();
        });

        return response()->json([
            'success' => true,
            'users' => $users,
        ]);
    }

    // Update typing status
    public function updateTypingStatus(Request $request, $id)
    {
        $user = Auth::user();
        $group = ChatGroup::findOrFail($id);

        // Check if user is member
        if (!$group->members->contains($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not a member of this group',
            ], 403);
        }

        $isTyping = $request->input('is_typing', false);

        // Store typing status in cache for 5 seconds
        $cacheKey = "typing_group_{$id}_user_{$user->id}";
        
        if ($isTyping) {
            cache()->put($cacheKey, [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'group_id' => $id,
                'timestamp' => now(),
            ], 5); // 5 seconds TTL
        } else {
            cache()->forget($cacheKey);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    // Get typing users for a group
    public function getTypingUsers($id)
    {
        $user = Auth::user();
        $group = ChatGroup::findOrFail($id);

        // Check if user is member
        if (!$group->members->contains($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not a member of this group',
            ], 403);
        }

        // Get all typing users from cache
        $typingUsers = [];
        $members = $group->members;

        foreach ($members as $member) {
            if ($member->id === $user->id) {
                continue; // Skip current user
            }

            $cacheKey = "typing_group_{$id}_user_{$member->id}";
            $typingData = cache()->get($cacheKey);

            if ($typingData) {
                $typingUsers[] = [
                    'user_id' => $typingData['user_id'],
                    'user_name' => $typingData['user_name'],
                ];
            }
        }

        return response()->json([
            'success' => true,
            'typing_users' => $typingUsers,
        ]);
    }
}
