# gRPC Chat Microservice

A high-performance, scalable group chat microservice built with Go and gRPC.

## 🚀 Features

- ✅ **Real-time bidirectional streaming** for instant messaging
- ✅ **Group chat management** (create, join, leave)
- ✅ **Message persistence** with PostgreSQL
- ✅ **JWT authentication** integration with Laravel
- ✅ **Typing indicators** and user presence
- ✅ **Message history** with pagination
- ✅ **Read receipts** tracking
- ✅ **File attachments** support
- ✅ **Redis pub/sub** for scalability
- ✅ **Docker containerization** for easy deployment
- ✅ **gRPC-Web support** via Envoy proxy

## 📋 Prerequisites

- Docker & Docker Compose
- Go 1.21+ (for local development)
- Protocol Buffers compiler (protoc)

## 🔧 Quick Start

### 1. Setup Environment

```bash
# Copy environment file
cp .env.example .env

# Edit .env and set your JWT_SECRET (same as Laravel)
# Update database credentials if needed
```

### 2. Start Services with Docker

```bash
# Start all services (PostgreSQL, Redis, gRPC server, Envoy)
docker-compose up -d

# View logs
docker-compose logs -f chat-grpc

# Stop services
docker-compose down
```

### 3. Verify Services

```bash
# Check if all containers are running
docker-compose ps

# Test gRPC server (requires grpcurl)
grpcurl -plaintext localhost:50051 list
```

## 📡 Service Endpoints

- **gRPC Server**: `localhost:50051`
- **gRPC-Web (Envoy Proxy)**: `http://localhost:8080`
- **PostgreSQL**: `localhost:5433`
- **Redis**: `localhost:6380`
- **Envoy Admin**: `http://localhost:9901`

## 🏗️ Architecture

```
Web/Mobile Client
       ↓
   Envoy Proxy (gRPC-Web)
       ↓
   gRPC Chat Server
       ↓
   ┌──────────┬─────────┐
   ↓          ↓         ↓
PostgreSQL  Redis   Laravel API
```

## 🔐 Authentication

The service uses JWT tokens from your Laravel application. Make sure to:

1. Set `JWT_SECRET` in `.env` to match your Laravel `JWT_SECRET`
2. Pass the JWT token in every gRPC request
3. The service validates tokens and extracts user information

## 📝 API Documentation

### Group Management

- `CreateGroup` - Create a new group chat
- `JoinGroup` - Join an existing group
- `LeaveGroup` - Leave a group
- `GetUserGroups` - Get all groups for a user
- `GetGroupMembers` - Get members of a group

### Messaging

- `StreamMessages` - Bidirectional streaming for real-time chat
- `GetMessageHistory` - Retrieve message history with pagination
- `SendTypingIndicator` - Send/receive typing indicators

### User Status

- `UpdateUserStatus` - Update online/offline status

## 🧪 Testing with grpcurl

```bash
# List available services
grpcurl -plaintext localhost:50051 list

# List methods in ChatService
grpcurl -plaintext localhost:50051 list chat.ChatService

# Create a group (replace with actual JWT token)
grpcurl -plaintext -d '{
  "name": "Test Group",
  "description": "A test group chat",
  "member_ids": [1, 2, 3],
  "created_by": 1,
  "jwt_token": "your-jwt-token-here"
}' localhost:50051 chat.ChatService/CreateGroup
```

## 🔨 Development

### Generate Protocol Buffers

```bash
# Install protoc plugins
go install google.golang.org/protobuf/cmd/protoc-gen-go@latest
go install google.golang.org/grpc/cmd/protoc-gen-go-grpc@latest

# Generate Go code from .proto files
protoc --go_out=. --go_opt=paths=source_relative \
    --go-grpc_out=. --go-grpc_opt=paths=source_relative \
    proto/chat.proto
```

### Run Locally (without Docker)

```bash
# Install dependencies
go mod download

# Run PostgreSQL and Redis (or use Docker Compose for these only)
docker-compose up -d chat-postgres chat-redis

# Run the server
go run main.go
```

## 📦 Project Structure

```
chat-microservice/
├── proto/              # Protocol Buffer definitions
│   └── chat.proto
├── server/             # gRPC server implementation
│   └── chat_server.go
├── db/                 # Database layer
│   └── database.go
├── redis/              # Redis client
│   └── redis.go
├── auth/               # JWT authentication
│   └── jwt.go
├── main.go             # Application entry point
├── Dockerfile          # Docker build configuration
├── docker-compose.yml  # Multi-container setup
├── envoy.yaml          # Envoy proxy configuration
└── .env.example        # Environment variables template
```

## 🚀 Deployment

### Production Considerations

1. **Use TLS/SSL**: Enable secure connections
2. **Set proper secrets**: Use strong JWT secrets
3. **Configure limits**: Set message size limits
4. **Enable monitoring**: Add Prometheus metrics
5. **Use load balancer**: For horizontal scaling
6. **Backup database**: Regular PostgreSQL backups

### Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `GRPC_PORT` | gRPC server port | `50051` |
| `DB_HOST` | PostgreSQL host | `localhost` |
| `DB_PORT` | PostgreSQL port | `5433` |
| `DB_DATABASE` | Database name | `chat_db` |
| `DB_USERNAME` | Database user | `postgres` |
| `DB_PASSWORD` | Database password | `secret` |
| `REDIS_HOST` | Redis host | `localhost` |
| `REDIS_PORT` | Redis port | `6380` |
| `JWT_SECRET` | JWT secret key | - |
| `LARAVEL_API_URL` | Laravel API URL | `http://localhost:8000/api` |

## 📊 Monitoring

### Health Checks

```bash
# Check gRPC server health
grpcurl -plaintext localhost:50051 grpc.health.v1.Health/Check

# Check Envoy admin interface
curl http://localhost:9901/stats
```

### Logs

```bash
# View all logs
docker-compose logs -f

# View specific service
docker-compose logs -f chat-grpc
docker-compose logs -f envoy
```

## 🐛 Troubleshooting

### Connection Issues

```bash
# Check if services are running
docker-compose ps

# Restart services
docker-compose restart

# Check network connectivity
docker network inspect chat-network
```

### Database Issues

```bash
# Access PostgreSQL
docker exec -it chat-postgres psql -U postgres -d chat_db

# View tables
\dt

# View messages
SELECT * FROM messages ORDER BY created_at DESC LIMIT 10;
```

### Redis Issues

```bash
# Access Redis CLI
docker exec -it chat-redis redis-cli

# Check connected clients
CLIENT LIST

# Monitor commands
MONITOR
```

## 🔄 Integration with Laravel

See the main project documentation for Laravel integration steps.

## 📄 License

MIT License

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.
