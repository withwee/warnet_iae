<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\ChatGroup;
use App\Models\ChatMessage;
use App\Models\User;
use App\Events\MessageSent;

/**
 * gRPC Chat Client Service (Hybrid Implementation)
 * 
 * This service simulates gRPC communication while using Laravel models.
 * Demonstrates gRPC architecture without requiring actual gRPC infrastructure.
 * 
 * For production with real gRPC:
 * - composer require grpc/grpc
 * - composer require google/protobuf
 * - Set up actual gRPC server (Go/Node.js)
 */
class ChatGrpcService
{
    protected string $grpcHost;
    protected string $grpcWebHost;
    protected bool $grpcEnabled;

    public function __construct()
    {
        $this->grpcHost = config('services.grpc_chat.host', 'localhost:50051');
        $this->grpcWebHost = config('services.grpc_chat.web_host', 'http://localhost:8080');
        $this->grpcEnabled = config('services.grpc_chat.enabled', false);
    }

    /**
     * Get JWT token for gRPC authentication
     */
    protected function getJwtToken(): string
    {
        if (auth()->check()) {
            return auth()->user()->createToken('grpc-access')->plainTextToken ?? '';
        }
        
        return '';
    }

    /**
     * Create a new group chat
     * 
     * Simulates: rpc CreateGroup(CreateGroupRequest) returns (Group)
     */
    public function createGroup(string $name, string $description, array $memberIds): array
    {
        try {
            Log::info('[gRPC] CreateGroup Request', [
                'method' => 'ChatService/CreateGroup',
                'name' => $name,
                'member_count' => count($memberIds)
            ]);

            // Simulate gRPC request validation
            if (empty($name)) {
                throw new Exception('Group name is required');
            }

            // Create group using Laravel model (simulating gRPC database call)
            $group = ChatGroup::create([
                'name' => $name,
                'description' => $description,
                'created_by' => auth()->id(),
            ]);

            // Add members (creator + specified members)
            $allMemberIds = array_unique(array_merge([auth()->id()], $memberIds));
            $group->members()->attach($allMemberIds);

            // Simulate gRPC response
            $response = [
                'id' => $group->id,
                'name' => $group->name,
                'description' => $group->description,
                'member_count' => count($allMemberIds),
                'created_by' => auth()->id(),
                'created_at' => $group->created_at->timestamp,
            ];

            Log::info('[gRPC] CreateGroup Response', [
                'group_id' => $group->id,
                'status' => 'success'
            ]);

            return $response;

        } catch (Exception $e) {
            Log::error('[gRPC] CreateGroup Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get user's groups
     * 
     * Simulates: rpc GetUserGroups(GetUserGroupsRequest) returns (GroupsResponse)
     */
    public function getUserGroups(int $userId): array
    {
        try {
            Log::info('[gRPC] GetUserGroups Request', [
                'method' => 'ChatService/GetUserGroups',
                'user_id' => $userId
            ]);

            $groups = ChatGroup::whereHas('members', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->withCount('members')
            ->select('id', 'name', 'description', 'created_at')
            ->latest()
            ->get()
            ->map(function ($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'description' => $group->description,
                    'member_count' => $group->members_count,
                    'created_at' => $group->created_at->timestamp,
                ];
            })
            ->toArray();

            Log::info('[gRPC] GetUserGroups Response', [
                'groups_count' => count($groups),
                'status' => 'success'
            ]);

            return $groups;

        } catch (Exception $e) {
            Log::error('[gRPC] GetUserGroups Error', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Join a group
     * 
     * Simulates: rpc JoinGroup(JoinGroupRequest) returns (JoinGroupResponse)
     */
    public function joinGroup(int $groupId, int $userId): array
    {
        try {
            Log::info('[gRPC] JoinGroup Request', [
                'method' => 'ChatService/JoinGroup',
                'user_id' => $userId,
                'group_id' => $groupId
            ]);

            $group = ChatGroup::findOrFail($groupId);
            
            // Check if already a member
            if ($group->members()->where('user_id', $userId)->exists()) {
                Log::warning('[gRPC] JoinGroup - Already member', [
                    'user_id' => $userId,
                    'group_id' => $groupId
                ]);
                
                return [
                    'success' => false,
                    'message' => 'User is already a member of this group',
                ];
            }

            // Add member
            $group->members()->attach($userId);

            Log::info('[gRPC] JoinGroup Response', [
                'status' => 'success'
            ]);

            return [
                'success' => true,
                'message' => 'Successfully joined the group',
            ];

        } catch (Exception $e) {
            Log::error('[gRPC] JoinGroup Error', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Leave a group
     * 
     * Simulates: rpc LeaveGroup(LeaveGroupRequest) returns (LeaveGroupResponse)
     */
    public function leaveGroup(int $groupId, int $userId): array
    {
        try {
            Log::info('[gRPC] LeaveGroup Request', [
                'method' => 'ChatService/LeaveGroup',
                'user_id' => $userId,
                'group_id' => $groupId
            ]);

            $group = ChatGroup::findOrFail($groupId);
            
            if (!$group->members()->where('user_id', $userId)->exists()) {
                return [
                    'success' => false,
                    'message' => 'User is not a member of this group',
                ];
            }

            $group->members()->detach($userId);

            Log::info('[gRPC] LeaveGroup Response', [
                'status' => 'success'
            ]);

            return [
                'success' => true,
                'message' => 'Successfully left the group',
            ];

        } catch (Exception $e) {
            Log::error('[gRPC] LeaveGroup Error', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get message history
     * 
     * Simulates: rpc GetMessageHistory(GetHistoryRequest) returns (MessageHistoryResponse)
     */
    public function getMessageHistory(int $groupId, int $limit = 50, ?int $beforeId = null): array
    {
        try {
            Log::info('[gRPC] GetMessageHistory Request', [
                'method' => 'ChatService/GetMessageHistory',
                'group_id' => $groupId,
                'limit' => $limit,
                'before_id' => $beforeId
            ]);

            $query = ChatMessage::where('chat_group_id', $groupId)
                ->with('user:id,name')
                ->orderBy('created_at', 'desc')
                ->limit($limit);

            if ($beforeId) {
                $query->where('id', '<', $beforeId);
            }

            $messages = $query->get()
                ->reverse()
                ->values()
                ->map(function ($msg) {
                    return [
                        'id' => $msg->id,
                        'group_id' => $msg->chat_group_id,
                        'user_id' => $msg->user_id,
                        'user_name' => $msg->user->name,
                        'content' => $msg->message,
                        'type' => $msg->type ?? 'text',
                        'timestamp' => $msg->created_at->timestamp,
                        'is_edited' => false,
                    ];
                })
                ->toArray();

            $response = [
                'messages' => $messages,
                'has_more' => count($messages) >= $limit,
            ];

            Log::info('[gRPC] GetMessageHistory Response', [
                'message_count' => count($messages),
                'has_more' => $response['has_more']
            ]);

            return $response;

        } catch (Exception $e) {
            Log::error('[gRPC] GetMessageHistory Error', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'messages' => [],
                'has_more' => false,
            ];
        }
    }

    /**
     * Send message
     * 
     * Simulates: rpc StreamMessages(stream MessageRequest) returns (stream MessageResponse)
     */
    public function sendMessage(int $groupId, int $userId, string $content, string $type = 'text'): array
    {
        try {
            Log::info('[gRPC] SendMessage Request', [
                'method' => 'ChatService/StreamMessages',
                'group_id' => $groupId,
                'user_id' => $userId,
                'type' => $type
            ]);

            $message = ChatMessage::create([
                'chat_group_id' => $groupId,
                'user_id' => $userId,
                'message' => $content,
                'type' => $type,
            ]);

            $message->load('user:id,name');

            // Simulate gRPC streaming broadcast
            event(new MessageSent($message));

            $response = [
                'id' => $message->id,
                'group_id' => $message->chat_group_id,
                'user_id' => $message->user_id,
                'user_name' => $message->user->name,
                'content' => $message->message,
                'type' => $message->type,
                'timestamp' => $message->created_at->timestamp,
                'success' => true,
            ];

            Log::info('[gRPC] SendMessage Response', [
                'message_id' => $message->id,
                'status' => 'broadcasted'
            ]);

            return $response;

        } catch (Exception $e) {
            Log::error('[gRPC] SendMessage Error', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get group members
     * 
     * Simulates: rpc GetGroupMembers(GetGroupMembersRequest) returns (GroupMembersResponse)
     */
    public function getGroupMembers(int $groupId): array
    {
        try {
            Log::info('[gRPC] GetGroupMembers Request', [
                'method' => 'ChatService/GetGroupMembers',
                'group_id' => $groupId
            ]);

            $group = ChatGroup::with('members:id,name,email')->findOrFail($groupId);

            $members = $group->members->map(function ($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'email' => $member->email,
                    'online' => false, // Can be enhanced with presence tracking
                ];
            })->toArray();

            Log::info('[gRPC] GetGroupMembers Response', [
                'member_count' => count($members)
            ]);

            return $members;

        } catch (Exception $e) {
            Log::error('[gRPC] GetGroupMembers Error', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Get gRPC connection info for frontend
     * 
     * Returns connection metadata
     */
    public function getConnectionInfo(): array
    {
        return [
            'grpc_web_url' => $this->grpcWebHost,
            'jwt_token' => $this->getJwtToken(),
            'user_id' => auth()->id(),
            'mode' => $this->grpcEnabled ? 'real-grpc' : 'hybrid',
            'server_status' => 'connected',
        ];
    }

    /**
     * Health check
     */
    public function healthCheck(): array
    {
        return [
            'status' => 'healthy',
            'service' => 'ChatService',
            'mode' => $this->grpcEnabled ? 'real-grpc' : 'hybrid',
            'timestamp' => time(),
        ];
    }
}
