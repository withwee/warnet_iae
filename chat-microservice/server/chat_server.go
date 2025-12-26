package server

import (
	"context"
	"database/sql"
	"fmt"
	"io"
	"log"
	"sync"
	"time"

	"github.com/warnet_iae/chat-microservice/auth"
	"github.com/warnet_iae/chat-microservice/db"
	pb "github.com/warnet_iae/chat-microservice/proto"
	redisClient "github.com/warnet_iae/chat-microservice/redis"
	"google.golang.org/grpc/codes"
	"google.golang.org/grpc/status"
)

type ChatServer struct {
	pb.UnimplementedChatServiceServer
	db          *db.Database
	redis       *redisClient.RedisClient
	streams     map[int64]map[int64]pb.ChatService_StreamMessagesServer // groupID -> userID -> stream
	streamMutex sync.RWMutex
}

func NewChatServer(database *db.Database, redis *redisClient.RedisClient) *ChatServer {
	return &ChatServer{
		db:      database,
		redis:   redis,
		streams: make(map[int64]map[int64]pb.ChatService_StreamMessagesServer),
	}
}

// CreateGroup creates a new chat group
func (s *ChatServer) CreateGroup(ctx context.Context, req *pb.CreateGroupRequest) (*pb.Group, error) {
	// Validate JWT
	claims, err := auth.ValidateJWT(req.JwtToken)
	if err != nil {
		return nil, status.Error(codes.Unauthenticated, "Invalid authentication token")
	}

	// Verify the requester is the creator
	if claims.UserID != req.CreatedBy {
		return nil, status.Error(codes.PermissionDenied, "User ID mismatch")
	}

	// Insert group
	var groupID int64
	var createdAt time.Time
	err = s.db.DB.QueryRow(
		"INSERT INTO groups (name, description, created_by) VALUES ($1, $2, $3) RETURNING id, created_at",
		req.Name, req.Description, req.CreatedBy,
	).Scan(&groupID, &createdAt)
	if err != nil {
		log.Printf("Error creating group: %v", err)
		return nil, status.Error(codes.Internal, "Failed to create group")
	}

	// Add creator as first member
	memberIDs := append([]int64{req.CreatedBy}, req.MemberIds...)
	for _, memberID := range memberIDs {
		_, err := s.db.DB.Exec(
			"INSERT INTO group_members (group_id, user_id) VALUES ($1, $2) ON CONFLICT DO NOTHING",
			groupID, memberID,
		)
		if err != nil {
			log.Printf("Error adding member %d to group: %v", memberID, err)
		}
	}

	log.Printf("✅ Group created: ID=%d, Name=%s, Creator=%d", groupID, req.Name, req.CreatedBy)

	return &pb.Group{
		Id:          groupID,
		Name:        req.Name,
		Description: req.Description,
		MemberIds:   memberIDs,
		CreatedBy:   req.CreatedBy,
		CreatedAt:   createdAt.Unix(),
		UpdatedAt:   createdAt.Unix(),
	}, nil
}

// JoinGroup allows a user to join an existing group
func (s *ChatServer) JoinGroup(ctx context.Context, req *pb.JoinGroupRequest) (*pb.JoinGroupResponse, error) {
	claims, err := auth.ValidateJWT(req.JwtToken)
	if err != nil {
		return nil, status.Error(codes.Unauthenticated, "Invalid authentication token")
	}

	if claims.UserID != req.UserId {
		return nil, status.Error(codes.PermissionDenied, "User ID mismatch")
	}

	// Check if group exists
	var groupName string
	err = s.db.DB.QueryRow("SELECT name FROM groups WHERE id = $1", req.GroupId).Scan(&groupName)
	if err == sql.ErrNoRows {
		return &pb.JoinGroupResponse{
			Success: false,
			Message: "Group not found",
		}, nil
	}
	if err != nil {
		return nil, status.Error(codes.Internal, "Database error")
	}

	// Add user to group
	_, err = s.db.DB.Exec(
		"INSERT INTO group_members (group_id, user_id) VALUES ($1, $2) ON CONFLICT DO NOTHING",
		req.GroupId, req.UserId,
	)
	if err != nil {
		return nil, status.Error(codes.Internal, "Failed to join group")
	}

	log.Printf("✅ User %d joined group %d", req.UserId, req.GroupId)

	return &pb.JoinGroupResponse{
		Success: true,
		Message: fmt.Sprintf("Successfully joined group: %s", groupName),
	}, nil
}

// LeaveGroup removes a user from a group
func (s *ChatServer) LeaveGroup(ctx context.Context, req *pb.LeaveGroupRequest) (*pb.LeaveGroupResponse, error) {
	claims, err := auth.ValidateJWT(req.JwtToken)
	if err != nil {
		return nil, status.Error(codes.Unauthenticated, "Invalid authentication token")
	}

	if claims.UserID != req.UserId {
		return nil, status.Error(codes.PermissionDenied, "User ID mismatch")
	}

	result, err := s.db.DB.Exec(
		"DELETE FROM group_members WHERE group_id = $1 AND user_id = $2",
		req.GroupId, req.UserId,
	)
	if err != nil {
		return nil, status.Error(codes.Internal, "Failed to leave group")
	}

	rowsAffected, _ := result.RowsAffected()
	if rowsAffected == 0 {
		return &pb.LeaveGroupResponse{
			Success: false,
			Message: "You are not a member of this group",
		}, nil
	}

	log.Printf("✅ User %d left group %d", req.UserId, req.GroupId)

	return &pb.LeaveGroupResponse{
		Success: true,
		Message: "Successfully left the group",
	}, nil
}

// GetUserGroups retrieves all groups a user is a member of
func (s *ChatServer) GetUserGroups(ctx context.Context, req *pb.GetUserGroupsRequest) (*pb.UserGroupsResponse, error) {
	claims, err := auth.ValidateJWT(req.JwtToken)
	if err != nil {
		return nil, status.Error(codes.Unauthenticated, "Invalid authentication token")
	}

	if claims.UserID != req.UserId {
		return nil, status.Error(codes.PermissionDenied, "User ID mismatch")
	}

	rows, err := s.db.DB.Query(`
		SELECT g.id, g.name, g.description, g.created_by, g.created_at, g.updated_at
		FROM groups g
		INNER JOIN group_members gm ON g.id = gm.group_id
		WHERE gm.user_id = $1
		ORDER BY g.updated_at DESC
	`, req.UserId)
	if err != nil {
		return nil, status.Error(codes.Internal, "Database error")
	}
	defer rows.Close()

	var groups []*pb.Group
	for rows.Next() {
		var g pb.Group
		var createdAt, updatedAt time.Time
		err := rows.Scan(&g.Id, &g.Name, &g.Description, &g.CreatedBy, &createdAt, &updatedAt)
		if err != nil {
			continue
		}
		g.CreatedAt = createdAt.Unix()
		g.UpdatedAt = updatedAt.Unix()
		groups = append(groups, &g)
	}

	return &pb.UserGroupsResponse{
		Groups: groups,
	}, nil
}

// StreamMessages handles bidirectional streaming for real-time chat
func (s *ChatServer) StreamMessages(stream pb.ChatService_StreamMessagesServer) error {
	ctx := stream.Context()
	var currentUserID int64
	var currentGroupID int64

	defer func() {
		// Clean up stream on disconnect
		if currentUserID > 0 && currentGroupID > 0 {
			s.removeStream(currentGroupID, currentUserID)
			log.Printf("🔌 User %d disconnected from group %d", currentUserID, currentGroupID)
		}
	}()

	for {
		select {
		case <-ctx.Done():
			return ctx.Err()
		default:
		}

		// Receive message from client
		req, err := stream.Recv()
		if err == io.EOF {
			return nil
		}
		if err != nil {
			log.Printf("Stream receive error: %v", err)
			return err
		}

		// Validate JWT
		claims, err := auth.ValidateJWT(req.JwtToken)
		if err != nil {
			return status.Error(codes.Unauthenticated, "Invalid authentication token")
		}

		if claims.UserID != req.UserId {
			return status.Error(codes.PermissionDenied, "User ID mismatch")
		}

		// Register stream on first message
		if currentUserID == 0 {
			currentUserID = req.UserId
			currentGroupID = req.GroupId
			s.addStream(currentGroupID, currentUserID, stream)
			log.Printf("🔗 User %d connected to group %d", currentUserID, currentGroupID)
		}

		// Verify user is member of group
		var isMember bool
		err = s.db.DB.QueryRow(
			"SELECT EXISTS(SELECT 1 FROM group_members WHERE group_id = $1 AND user_id = $2)",
			req.GroupId, req.UserId,
		).Scan(&isMember)
		if err != nil || !isMember {
			return status.Error(codes.PermissionDenied, "You are not a member of this group")
		}

		// Save message to database
		var messageID int64
		var createdAt time.Time
		err = s.db.DB.QueryRow(`
			INSERT INTO messages (group_id, user_id, content, message_type, attachments, reply_to)
			VALUES ($1, $2, $3, $4, $5, $6)
			RETURNING id, created_at
		`, req.GroupId, req.UserId, req.Content, req.Type, req.Attachments, req.ReplyTo).Scan(&messageID, &createdAt)
		if err != nil {
			log.Printf("Error saving message: %v", err)
			return status.Error(codes.Internal, "Failed to save message")
		}

		// Get user info (you might want to cache this)
		userName := fmt.Sprintf("User %d", req.UserId)
		userAvatar := ""

		// Create response message
		msg := &pb.MessageResponse{
			Id:          messageID,
			GroupId:     req.GroupId,
			UserId:      req.UserId,
			UserName:    userName,
			UserAvatar:  userAvatar,
			Content:     req.Content,
			Type:        req.Type,
			Timestamp:   createdAt.Unix(),
			Attachments: req.Attachments,
			ReplyTo:     req.ReplyTo,
			IsEdited:    false,
			ReadBy:      []int64{req.UserId}, // Sender has "read" their own message
		}

		// Broadcast to all connected clients in this group
		s.broadcastMessage(req.GroupId, msg)

		log.Printf("📨 Message %d sent in group %d by user %d", messageID, req.GroupId, req.UserId)
	}
}

// GetMessageHistory retrieves message history for a group
func (s *ChatServer) GetMessageHistory(ctx context.Context, req *pb.GetHistoryRequest) (*pb.MessageHistoryResponse, error) {
	claims, err := auth.ValidateJWT(req.JwtToken)
	if err != nil {
		return nil, status.Error(codes.Unauthenticated, "Invalid authentication token")
	}

	// Verify user is member of group
	var isMember bool
	err = s.db.DB.QueryRow(
		"SELECT EXISTS(SELECT 1 FROM group_members WHERE group_id = $1 AND user_id = $2)",
		req.GroupId, claims.UserID,
	).Scan(&isMember)
	if err != nil || !isMember {
		return nil, status.Error(codes.PermissionDenied, "You are not a member of this group")
	}

	limit := req.Limit
	if limit == 0 || limit > 100 {
		limit = 50
	}

	query := `
		SELECT id, group_id, user_id, content, message_type, attachments, reply_to, is_edited, created_at
		FROM messages
		WHERE group_id = $1
	`
	args := []interface{}{req.GroupId}

	if req.BeforeId > 0 {
		query += " AND id < $2"
		args = append(args, req.BeforeId)
	}

	query += " ORDER BY id DESC LIMIT $" + fmt.Sprintf("%d", len(args)+1)
	args = append(args, limit)

	rows, err := s.db.DB.Query(query, args...)
	if err != nil {
		return nil, status.Error(codes.Internal, "Database error")
	}
	defer rows.Close()

	var messages []*pb.MessageResponse
	var oldestID int64 = 0

	for rows.Next() {
		var msg pb.MessageResponse
		var createdAt time.Time
		var attachments []string

		err := rows.Scan(
			&msg.Id, &msg.GroupId, &msg.UserId, &msg.Content,
			&msg.Type, &attachments, &msg.ReplyTo, &msg.IsEdited, &createdAt,
		)
		if err != nil {
			continue
		}

		msg.Timestamp = createdAt.Unix()
		msg.Attachments = attachments
		msg.UserName = fmt.Sprintf("User %d", msg.UserId)
		messages = append(messages, &msg)

		if oldestID == 0 || msg.Id < oldestID {
			oldestID = msg.Id
		}
	}

	// Reverse to get chronological order
	for i, j := 0, len(messages)-1; i < j; i, j = i+1, j-1 {
		messages[i], messages[j] = messages[j], messages[i]
	}

	hasMore := len(messages) == int(limit)

	return &pb.MessageHistoryResponse{
		Messages:         messages,
		HasMore:          hasMore,
		OldestMessageId:  oldestID,
	}, nil
}

// SendTypingIndicator handles typing indicators
func (s *ChatServer) SendTypingIndicator(ctx context.Context, req *pb.TypingRequest) (*pb.TypingResponse, error) {
	claims, err := auth.ValidateJWT(req.JwtToken)
	if err != nil {
		return nil, status.Error(codes.Unauthenticated, "Invalid authentication token")
	}

	if claims.UserID != req.UserId {
		return nil, status.Error(codes.PermissionDenied, "User ID mismatch")
	}

	// Broadcast typing indicator to other members
	typingMsg := &pb.TypingResponse{
		Success:   true,
		UserId:    req.UserId,
		UserName:  fmt.Sprintf("User %d", req.UserId),
		IsTyping:  req.IsTyping,
	}

	// You can broadcast this via Redis pub/sub or directly to streams
	log.Printf("⌨️  User %d typing in group %d: %v", req.UserId, req.GroupId, req.IsTyping)

	return typingMsg, nil
}

// UpdateUserStatus updates online/offline status
func (s *ChatServer) UpdateUserStatus(ctx context.Context, req *pb.UserStatusRequest) (*pb.UserStatusResponse, error) {
	claims, err := auth.ValidateJWT(req.JwtToken)
	if err != nil {
		return nil, status.Error(codes.Unauthenticated, "Invalid authentication token")
	}

	if claims.UserID != req.UserId {
		return nil, status.Error(codes.PermissionDenied, "User ID mismatch")
	}

	if req.Online {
		err = s.redis.SetUserOnline(ctx, req.UserId)
	} else {
		err = s.redis.SetUserOffline(ctx, req.UserId)
	}

	if err != nil {
		log.Printf("Error updating user status: %v", err)
		return &pb.UserStatusResponse{
			Success: false,
			Message: "Failed to update status",
		}, nil
	}

	statusText := "offline"
	if req.Online {
		statusText = "online"
	}

	log.Printf("👤 User %d is now %s", req.UserId, statusText)

	return &pb.UserStatusResponse{
		Success: true,
		Message: fmt.Sprintf("Status updated to %s", statusText),
	}, nil
}

// GetGroupMembers retrieves all members of a group
func (s *ChatServer) GetGroupMembers(ctx context.Context, req *pb.GetGroupMembersRequest) (*pb.GroupMembersResponse, error) {
	claims, err := auth.ValidateJWT(req.JwtToken)
	if err != nil {
		return nil, status.Error(codes.Unauthenticated, "Invalid authentication token")
	}

	// Verify requester is member of group
	var isMember bool
	err = s.db.DB.QueryRow(
		"SELECT EXISTS(SELECT 1 FROM group_members WHERE group_id = $1 AND user_id = $2)",
		req.GroupId, claims.UserID,
	).Scan(&isMember)
	if err != nil || !isMember {
		return nil, status.Error(codes.PermissionDenied, "You are not a member of this group")
	}

	rows, err := s.db.DB.Query(
		"SELECT user_id FROM group_members WHERE group_id = $1",
		req.GroupId,
	)
	if err != nil {
		return nil, status.Error(codes.Internal, "Database error")
	}
	defer rows.Close()

	var members []*pb.User
	for rows.Next() {
		var userID int64
		if err := rows.Scan(&userID); err != nil {
			continue
		}

		// Check online status from Redis
		online, _ := s.redis.IsUserOnline(ctx, userID)

		members = append(members, &pb.User{
			Id:     userID,
			Name:   fmt.Sprintf("User %d", userID),
			Online: online,
		})
	}

	return &pb.GroupMembersResponse{
		Members: members,
	}, nil
}

// Helper methods for stream management
func (s *ChatServer) addStream(groupID, userID int64, stream pb.ChatService_StreamMessagesServer) {
	s.streamMutex.Lock()
	defer s.streamMutex.Unlock()

	if s.streams[groupID] == nil {
		s.streams[groupID] = make(map[int64]pb.ChatService_StreamMessagesServer)
	}
	s.streams[groupID][userID] = stream
}

func (s *ChatServer) removeStream(groupID, userID int64) {
	s.streamMutex.Lock()
	defer s.streamMutex.Unlock()

	if s.streams[groupID] != nil {
		delete(s.streams[groupID], userID)
		if len(s.streams[groupID]) == 0 {
			delete(s.streams, groupID)
		}
	}
}

func (s *ChatServer) broadcastMessage(groupID int64, msg *pb.MessageResponse) {
	s.streamMutex.RLock()
	defer s.streamMutex.RUnlock()

	if streams, ok := s.streams[groupID]; ok {
		for userID, stream := range streams {
			if err := stream.Send(msg); err != nil {
				log.Printf("Error sending to user %d: %v", userID, err)
			}
		}
	}
}
