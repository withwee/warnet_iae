<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ChatGrpcService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatGroupController extends Controller
{
    protected ChatGrpcService $chatService;

    public function __construct(ChatGrpcService $chatService)
    {
        // Ensure user is authenticated
        $this->middleware('auth:api');
        $this->chatService = $chatService;
    }

    /**
     * Get all groups for authenticated user
     * 
     * GET /api/chat/groups
     */
    public function index(Request $request)
    {
        try {
            $groups = $this->chatService->getUserGroups(auth()->id());

            return response()->json([
                'success' => true,
                'data' => $groups,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch groups',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new group
     * 
     * POST /api/chat/groups
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'member_ids' => 'required|array|min:1',
            'member_ids.*' => 'integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $group = $this->chatService->createGroup(
                $request->input('name'),
                $request->input('description', ''),
                $request->input('member_ids')
            );

            return response()->json([
                'success' => true,
                'message' => 'Group created successfully',
                'data' => $group,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create group',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get group details with members
     * 
     * GET /api/chat/groups/{groupId}
     */
    public function show(Request $request, int $groupId)
    {
        try {
            $members = $this->chatService->getGroupMembers($groupId);

            return response()->json([
                'success' => true,
                'data' => [
                    'group_id' => $groupId,
                    'members' => $members,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch group details',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Join a group
     * 
     * POST /api/chat/groups/{groupId}/join
     */
    public function join(Request $request, int $groupId)
    {
        try {
            $result = $this->chatService->joinGroup($groupId, auth()->id());

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
            ], $result['success'] ? 200 : 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to join group',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Leave a group
     * 
     * POST /api/chat/groups/{groupId}/leave
     */
    public function leave(Request $request, int $groupId)
    {
        try {
            $result = $this->chatService->leaveGroup($groupId, auth()->id());

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
            ], $result['success'] ? 200 : 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to leave group',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get message history for a group
     * 
     * GET /api/chat/groups/{groupId}/messages
     */
    public function messages(Request $request, int $groupId)
    {
        $limit = $request->input('limit', 50);
        $beforeId = $request->input('before_id', null);

        try {
            $result = $this->chatService->getMessageHistory($groupId, $limit, $beforeId);

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch messages',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get gRPC connection info for frontend
     * 
     * GET /api/chat/connection-info
     */
    public function connectionInfo(Request $request)
    {
        try {
            $info = $this->chatService->getConnectionInfo();

            return response()->json([
                'success' => true,
                'data' => $info,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get connection info',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
