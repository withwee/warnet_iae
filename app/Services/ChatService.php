<?php

namespace App\Services;

use App\Models\ChatGroup;
use App\Models\ChatMessage;
use App\Events\MessageSent;
use Illuminate\Support\Facades\DB;

class ChatService
{
    /**
     * Get user's groups with optimized query
     */
    public function getUserGroups(int $userId)
    {
        return ChatGroup::whereHas('members', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->withCount('members')
        ->select('id', 'name', 'description', 'created_at')
        ->latest()
        ->get();
    }

    /**
     * Get available groups for user to join
     */
    public function getAvailableGroups(int $userId)
    {
        return ChatGroup::whereDoesntHave('members', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->withCount('members')
        ->select('id', 'name', 'description', 'created_at')
        ->latest()
        ->get();
    }

    /**
     * Create new group with members
     */
    public function createGroup(array $data, int $creatorId): ChatGroup
    {
        return DB::transaction(function () use ($data, $creatorId) {
            // Create group
            $group = ChatGroup::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'created_by' => $creatorId,
            ]);

            // Add members (including creator)
            $memberIds = array_unique(array_merge([$creatorId], $data['member_ids']));
            $group->members()->attach($memberIds);

            return $group->load('members');
        });
    }

    /**
     * Add user to group
     */
    public function joinGroup(int $groupId, int $userId): bool
    {
        $group = ChatGroup::findOrFail($groupId);
        
        // Check if already member
        if ($group->members()->where('user_id', $userId)->exists()) {
            return false;
        }

        $group->members()->attach($userId);
        return true;
    }

    /**
     * Remove user from group
     */
    public function leaveGroup(int $groupId, int $userId): bool
    {
        $group = ChatGroup::findOrFail($groupId);
        
        // Check if member
        if (!$group->members()->where('user_id', $userId)->exists()) {
            return false;
        }

        $group->members()->detach($userId);
        return true;
    }

    /**
     * Check if user is member of group
     */
    public function isMember(int $groupId, int $userId): bool
    {
        return ChatGroup::whereHas('members', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->where('id', $groupId)->exists();
    }

    /**
     * Get messages for group
     */
    public function getGroupMessages(int $groupId, int $userId)
    {
        // Verify membership
        if (!$this->isMember($groupId, $userId)) {
            throw new \Exception('User is not a member of this group');
        }

        return ChatMessage::where('chat_group_id', $groupId)
            ->with('user:id,name')
            ->select('id', 'user_id', 'message', 'type', 'created_at')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Send message to group
     */
    public function sendMessage(array $data, int $groupId, int $userId): ChatMessage
    {
        // Verify membership
        if (!$this->isMember($groupId, $userId)) {
            throw new \Exception('User is not a member of this group');
        }

        // Create message
        $message = ChatMessage::create([
            'chat_group_id' => $groupId,
            'user_id' => $userId,
            'message' => $data['message'],
            'type' => $data['type'] ?? 'text',
        ]);

        // Load user relationship
        $message->load('user:id,name');

        // Broadcast to group
        broadcast(new MessageSent($message))->toOthers();

        return $message;
    }

    /**
     * Get group details with members
     */
    public function getGroupDetails(int $groupId)
    {
        return ChatGroup::with('members:id,name,email')
            ->findOrFail($groupId);
    }
}
