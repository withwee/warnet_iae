package main

import (
	"log"
	"net"
	"os"
	"os/signal"
	"syscall"

	"github.com/joho/godotenv"
	"github.com/warnet_iae/chat-microservice/db"
	"github.com/warnet_iae/chat-microservice/redis"
	"github.com/warnet_iae/chat-microservice/server"
	pb "github.com/warnet_iae/chat-microservice/proto"
	"google.golang.org/grpc"
	"google.golang.org/grpc/reflection"
)

func main() {
	// Load environment variables
	if err := godotenv.Load(); err != nil {
		log.Println("⚠️  No .env file found, using system environment variables")
	}

	log.Println("🚀 Starting gRPC Chat Microservice...")

	// Initialize database
	database, err := db.NewDatabase()
	if err != nil {
		log.Fatalf("❌ Failed to initialize database: %v", err)
	}
	defer database.Close()

	// Initialize Redis
	redisClient, err := redis.NewRedisClient()
	if err != nil {
		log.Fatalf("❌ Failed to initialize Redis: %v", err)
	}
	defer redisClient.Close()

	// Get port from environment
	port := os.Getenv("GRPC_PORT")
	if port == "" {
		port = "50051"
	}

	// Create TCP listener
	lis, err := net.Listen("tcp", ":"+port)
	if err != nil {
		log.Fatalf("❌ Failed to listen on port %s: %v", port, err)
	}

	// Create gRPC server
	grpcServer := grpc.NewServer(
		grpc.MaxRecvMsgSize(10 * 1024 * 1024), // 10MB
		grpc.MaxSendMsgSize(10 * 1024 * 1024), // 10MB
	)

	// Register chat service
	chatServer := server.NewChatServer(database, redisClient)
	pb.RegisterChatServiceServer(grpcServer, chatServer)

	// Register reflection service (for tools like grpcurl)
	reflection.Register(grpcServer)

	// Handle graceful shutdown
	go func() {
		sigChan := make(chan os.Signal, 1)
		signal.Notify(sigChan, os.Interrupt, syscall.SIGTERM)
		<-sigChan
		log.Println("\n🛑 Shutting down gracefully...")
		grpcServer.GracefulStop()
	}()

	log.Printf("✅ gRPC Chat Server listening on port %s", port)
	log.Println("📡 Ready to accept connections...")

	// Start serving
	if err := grpcServer.Serve(lis); err != nil {
		log.Fatalf("❌ Failed to serve: %v", err)
	}
}
