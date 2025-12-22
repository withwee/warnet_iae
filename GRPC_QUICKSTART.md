# 🚀 gRPC Chat Microservice - Quick Start Guide

## ✅ What Has Been Implemented

Your gRPC Chat Microservice is now ready! Here's what we've created:

### 🏗️ Architecture Components

1. **gRPC Chat Microservice** (Go)
   - ✅ Protocol Buffers definition (`.proto`)
   - ✅ Full gRPC server implementation
   - ✅ PostgreSQL database integration
   - ✅ Redis pub/sub for scalability
   - ✅ JWT authentication (Laravel compatible)
   - ✅ Docker containerization
   - ✅ Envoy proxy for gRPC-Web

2. **Laravel Integration**
   - ✅ ChatGrpcService wrapper
   - ✅ ChatGroupController for REST API
   - ✅ API routes (`/api/chat/*`)
   - ✅ Configuration in `config/services.php`

3. **Frontend (Vue.js)**
   - ✅ Beautiful group chat component
   - ✅ Real-time messaging interface
   - ✅ Group creation and management
   - ✅ Responsive design

---

## 🎯 Quick Start Instructions

### Step 1: Setup gRPC Microservice

```bash
# Navigate to chat microservice directory
cd d:\laragon\www\warnet_iae\chat-microservice

# Copy environment file
copy .env.example .env

# Edit .env file and set JWT_SECRET (IMPORTANT!)
# Make sure it matches your Laravel JWT_SECRET
notepad .env
```

**Required .env configuration:**
```env
GRPC_PORT=50051
DB_HOST=localhost
DB_PORT=5433
DB_DATABASE=chat_db
DB_USERNAME=postgres
DB_PASSWORD=secret
REDIS_HOST=localhost
REDIS_PORT=6380
JWT_SECRET=<YOUR_LARAVEL_JWT_SECRET>  # ⚠️ MUST MATCH LARAVEL!
LARAVEL_API_URL=http://localhost:8000/api
```

### Step 2: Start Services with Docker

```bash
# Start all services (PostgreSQL, Redis, gRPC, Envoy)
docker-compose up -d

# Or use the convenient batch script
start.bat

# View logs
docker-compose logs -f chat-grpc
```

**Services will be available at:**
- 🟢 gRPC Server: `localhost:50051`
- 🌐 gRPC-Web Proxy: `http://localhost:8080`
- 🐘 PostgreSQL: `localhost:5433`
- 🔴 Redis: `localhost:6380`
- 📊 Envoy Admin: `http://localhost:9901`

### Step 3: Configure Laravel

Add to your Laravel `.env` file:

```env
# gRPC Chat Configuration
GRPC_CHAT_HOST=localhost:50051
GRPC_CHAT_WEB_HOST=http://localhost:8080
```

### Step 4: Test the API

```bash
# Make sure Laravel is running
php artisan serve

# Test connection info endpoint
curl http://localhost:8000/api/chat/connection-info \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"

# Create a group
curl -X POST http://localhost:8000/api/chat/groups \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test Group",
    "description": "My first group chat",
    "member_ids": [1, 2, 3]
  }'

# Get user groups
curl http://localhost:8000/api/chat/groups \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Step 5: Use in Frontend

Add the chat component to your Vue.js app:

```vue
<template>
  <div id="app">
    <GroupChat />
  </div>
</template>

<script>
import GroupChat from './components/GroupChat.vue';

export default {
  components: {
    GroupChat
  }
}
</script>
```

Or create a dedicated route:

```javascript
// routes/web.php or in your Vue Router
Route::get('/chat', function () {
    return view('chat');
})->middleware('auth');
```

```blade
{{-- resources/views/chat.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Group Chat</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <group-chat></group-chat>
    </div>
</body>
</html>
```

---

## 📡 Available API Endpoints

All endpoints require authentication (`Authorization: Bearer <token>`)

### Group Management

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/chat/connection-info` | Get gRPC connection info |
| `GET` | `/api/chat/groups` | Get user's groups |
| `POST` | `/api/chat/groups` | Create new group |
| `GET` | `/api/chat/groups/{id}` | Get group details |
| `POST` | `/api/chat/groups/{id}/join` | Join a group |
| `POST` | `/api/chat/groups/{id}/leave` | Leave a group |
| `GET` | `/api/chat/groups/{id}/messages` | Get message history |

### Example Requests

**Create Group:**
```json
POST /api/chat/groups
{
  "name": "Team Chat",
  "description": "Development team discussion",
  "member_ids": [1, 2, 3, 4]
}
```

**Get Messages:**
```
GET /api/chat/groups/1/messages?limit=50&before_id=999
```

---

## 🧪 Testing the gRPC Service

### Option 1: Using grpcurl (Recommended)

Install grpcurl:
```bash
# Windows (using Chocolatey)
choco install grpcurl

# Or download from: https://github.com/fullstorydev/grpcurl/releases
```

Test commands:
```bash
# List available services
grpcurl -plaintext localhost:50051 list

# List methods
grpcurl -plaintext localhost:50051 list chat.ChatService

# Create a group
grpcurl -plaintext -d '{
  "name": "Test Group",
  "description": "Testing",
  "member_ids": [1, 2],
  "created_by": 1,
  "jwt_token": "your-jwt-token-here"
}' localhost:50051 chat.ChatService/CreateGroup
```

### Option 2: Using BloomRPC (GUI Tool)

1. Download BloomRPC: https://github.com/bloomrpc/bloomrpc/releases
2. Open BloomRPC
3. Import `chat-microservice/proto/chat.proto`
4. Connect to `localhost:50051`
5. Test your RPC methods!

---

## 🔧 Development Workflow

### Making Changes to Proto File

1. Edit `proto/chat.proto`
2. Regenerate code:
```bash
cd chat-microservice

# Generate Go code
protoc --go_out=. --go_opt=paths=source_relative \
    --go-grpc_out=. --go-grpc_opt=paths=source_relative \
    proto/chat.proto

# Rebuild and restart
docker-compose up -d --build
```

### Viewing Logs

```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f chat-grpc
docker-compose logs -f envoy
docker-compose logs -f chat-postgres
```

### Database Access

```bash
# Connect to PostgreSQL
docker exec -it chat-postgres psql -U postgres -d chat_db

# Useful SQL commands
\dt                                    # List tables
SELECT * FROM groups;                  # View groups
SELECT * FROM messages ORDER BY created_at DESC LIMIT 10;  # Recent messages
SELECT * FROM group_members;           # View memberships
```

### Redis CLI

```bash
# Connect to Redis
docker exec -it chat-redis redis-cli

# Useful commands
KEYS user:online:*    # List online users
GET user:online:1     # Check if user 1 is online
MONITOR               # Watch all commands in real-time
```

---

## 🎨 Frontend Integration

### For Real gRPC-Web Streaming

To enable real gRPC streaming in the browser, you'll need:

1. Install gRPC-Web client:
```bash
npm install grpc-web google-protobuf
```

2. Generate JavaScript code from .proto:
```bash
protoc -I=. chat.proto \
  --js_out=import_style=commonjs:./resources/js/grpc \
  --grpc-web_out=import_style=commonjs,mode=grpcwebtext:./resources/js/grpc
```

3. Use in Vue component:
```javascript
import { ChatServiceClient } from '../grpc/chat_grpc_web_pb';
import { MessageRequest } from '../grpc/chat_pb';

const client = new ChatServiceClient('http://localhost:8080');
const stream = client.streamMessages();

stream.on('data', (response) => {
  console.log('New message:', response.toObject());
});

// Send message
const request = new MessageRequest();
request.setGroupId(1);
request.setUserId(currentUserId);
request.setContent('Hello gRPC!');
request.setJwtToken(authToken);
stream.write(request);
```

---

## 🐛 Troubleshooting

### Services Won't Start

```bash
# Check Docker status
docker-compose ps

# Check logs for errors
docker-compose logs

# Restart everything
docker-compose down
docker-compose up -d
```

### Can't Connect to gRPC

1. Check if service is running: `docker ps | grep chat-grpc`
2. Check port availability: `netstat -an | findstr 50051`
3. Verify .env configuration
4. Check firewall settings

### JWT Authentication Fails

1. Verify `JWT_SECRET` matches between Laravel and chat-microservice
2. Generate token in Laravel: `php artisan tinker` then `auth()->user()->createToken('test')->plainTextToken`
3. Test token validation

### Database Connection Errors

```bash
# Check PostgreSQL is running
docker exec chat-postgres pg_isready

# View PostgreSQL logs
docker logs chat-postgres

# Recreate database
docker-compose down -v
docker-compose up -d
```

---

## 📊 Performance Optimization

### For Production

1. **Enable TLS/SSL**
   - Generate certificates
   - Update docker-compose.yml
   - Configure Envoy for HTTPS

2. **Horizontal Scaling**
   - Run multiple gRPC server instances
   - Use load balancer (Nginx, HAProxy)
   - Share Redis for pub/sub

3. **Database Optimization**
   - Add indexes (already included)
   - Configure connection pooling
   - Regular backup strategy

4. **Monitoring**
   - Add Prometheus metrics
   - Setup Grafana dashboards
   - Configure alerting

---

## 🚀 Next Steps

### Recommended Enhancements

1. **File Uploads**
   - Implement file attachment support
   - Add image preview in chat
   - Configure storage (S3, MinIO)

2. **Advanced Features**
   - Message reactions (emoji)
   - Message editing and deletion
   - Thread/Reply functionality
   - Voice/Video calls (WebRTC)

3. **Notifications**
   - Desktop notifications
   - Email notifications for mentions
   - Push notifications for mobile

4. **Search & Filters**
   - Full-text search in messages
   - Filter by date, user, type
   - Message bookmarking

5. **Admin Features**
   - Group admin roles
   - User moderation
   - Message reporting
   - Analytics dashboard

---

## 📚 Additional Resources

- [gRPC Official Documentation](https://grpc.io/docs/)
- [Protocol Buffers Guide](https://developers.google.com/protocol-buffers)
- [gRPC-Web Documentation](https://github.com/grpc/grpc-web)
- [Go gRPC Tutorial](https://grpc.io/docs/languages/go/quickstart/)
- [Envoy Proxy Docs](https://www.envoyproxy.io/docs)

---

## 🤝 Support

If you encounter any issues:

1. Check logs: `docker-compose logs -f`
2. Review this documentation
3. Check GitHub issues for similar problems
4. Create a new issue with:
   - Error messages
   - Steps to reproduce
   - Environment details

---

## ✅ Checklist

Before going to production, ensure:

- [ ] JWT_SECRET is properly configured and matches Laravel
- [ ] Database credentials are secure
- [ ] TLS/SSL is enabled for production
- [ ] Environment variables are set correctly
- [ ] Backup strategy is in place
- [ ] Monitoring is configured
- [ ] Load testing completed
- [ ] Security audit performed
- [ ] Documentation is up to date

---

**🎉 You're all set! Happy coding!**
