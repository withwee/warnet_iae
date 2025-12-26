# 🚀 Panduan Setup gRPC Chat Microservice

## Status Saat Ini

✅ **gRPC Microservice SUDAH ASLI (Real gRPC)**
- Microservice Go dengan gRPC sudah siap
- Protocol Buffers sudah didefinisikan
- Docker setup sudah lengkap

⚠️ **Laravel Service Masih Simulasi**
- `ChatGrpcService.php` masih menggunakan Laravel models
- Belum terhubung ke gRPC microservice yang asli

---

## 📋 Prerequisites

Sebelum mulai, pastikan sudah terinstal:

- **Docker Desktop for Windows** (download dari https://www.docker.com/products/docker-desktop)
- **Docker Compose** (biasanya sudah include di Docker Desktop)
- **Go 1.21+** (untuk development) - https://go.dev/dl/

---

## 🔧 Cara Menjalankan dengan Docker

### Step 1: Persiapan Environment

```powershell
# Masuk ke folder microservice
cd d:\laragon\www\warnet_iae\chat-microservice

# Copy file .env (jika belum)
copy .env.example .env

# Edit .env - sesuaikan JWT_SECRET dengan Laravel
notepad .env
```

**Penting**: `JWT_SECRET` di `.env` microservice HARUS SAMA dengan `JWT_SECRET` di Laravel!

### Step 2: Build Docker Images

```powershell
# Build all images
docker-compose build

# Atau build dengan no-cache (jika ada masalah)
docker-compose build --no-cache
```

### Step 3: Jalankan Semua Services

```powershell
# Start semua container (detached mode)
docker-compose up -d

# Atau tanpa detached untuk melihat logs
docker-compose up
```

Services yang akan berjalan:
- **chat-postgres** (port 5433): PostgreSQL database
- **chat-redis** (port 6380): Redis
- **chat-grpc** (port 50051): gRPC Server
- **envoy** (port 8080): gRPC-Web proxy

### Step 4: Verifikasi Services

```powershell
# Cek status containers
docker-compose ps

# Lihat logs gRPC server
docker-compose logs -f chat-grpc

# Lihat logs Envoy proxy
docker-compose logs -f envoy

# Lihat semua logs
docker-compose logs -f
```

### Step 5: Test gRPC Server

**Menggunakan grpcurl** (jika sudah install):
```powershell
# Install grpcurl
choco install grpcurl

# List services
grpcurl -plaintext localhost:50051 list

# List methods
grpcurl -plaintext localhost:50051 list chat.ChatService
```

**Menggunakan browser** (via gRPC-Web):
- buka http://localhost:8080
- buka Envoy admin: http://localhost:9901

---

## 🛑 Stop & Clean Up

```powershell
# Stop semua containers
docker-compose down

# Stop + hapus volumes (HATI-HATI: data akan hilang!)
docker-compose down -v

# Restart services
docker-compose restart

# Restart service tertentu saja
docker-compose restart chat-grpc
```

---

## 🔍 Troubleshooting

### Problem 1: Port sudah digunakan

**Error**: `port is already allocated`

**Solusi**:
```powershell
# Lihat apa yang menggunakan port
netstat -ano | findstr :50051
netstat -ano | findstr :5433
netstat -ano | findstr :6380

# Kill process (ganti PID dengan yang muncul)
taskkill /PID <PID> /F

# Atau ubah port di docker-compose.yml
```

### Problem 2: Database connection error

**Solusi**:
```powershell
# Cek logs database
docker-compose logs chat-postgres

# Cek healthcheck
docker exec -it chat-postgres pg_isready -U postgres

# Masuk ke database
docker exec -it chat-postgres psql -U postgres -d chat_db

# Di dalam psql:
\dt  # list tables
\q   # keluar
```

### Problem 3: gRPC Server tidak start

**Solusi**:
```powershell
# Build ulang image
docker-compose build chat-grpc

# Lihat detailed logs
docker-compose logs chat-grpc

# Atau masuk ke container
docker exec -it chat-grpc-server sh
```

### Problem 4: Go build error

**Error**: `go.sum` tidak ada

**Solusi**:
```powershell
# Masuk ke folder microservice
cd d:\laragon\www\warnet_iae\chat-microservice

# Download dependencies
go mod download

# Atau tidy
go mod tidy

# Generate go.sum
```

---

## 🔄 Development Workflow

### 1. Update Proto File

```powershell
# Edit proto/chat.proto
notepad proto\chat.proto

# Generate Go code
protoc --go_out=. --go_opt=paths=source_relative --go-grpc_out=. --go-grpc_opt=paths=source_relative proto/chat.proto

# Rebuild docker
docker-compose build chat-grpc
docker-compose restart chat-grpc
```

### 2. Update Server Code

```powershell
# Edit server/chat_server.go
notepad server\chat_server.go

# Rebuild & restart
docker-compose build chat-grpc
docker-compose restart chat-grpc
```

### 3. View Real-time Logs

```powershell
# Follow logs untuk debugging
docker-compose logs -f chat-grpc
```

---

## 🌐 Integrasi dengan Laravel

Untuk menghubungkan Laravel dengan gRPC microservice yang asli, ubah `ChatGrpcService.php`:

```php
<?php

namespace App\Services;

use Chat\ChatServiceClient;
use Chat\CreateGroupRequest;
use Grpc\ChannelCredentials;

class ChatGrpcService
{
    private $client;

    public function __construct()
    {
        $hostname = env('GRPC_CHAT_HOST', 'localhost:50051');
        $this->client = new ChatServiceClient(
            $hostname,
            ['credentials' => ChannelCredentials::createInsecure()]
        );
    }

    public function createGroup(string $name, string $description, array $memberIds): array
    {
        $request = new CreateGroupRequest();
        $request->setName($name);
        $request->setDescription($description);
        $request->setMemberIds($memberIds);
        $request->setCreatedBy(auth()->id());
        $request->setJwtToken(auth()->user()->createToken('grpc')->plainTextToken);

        [$response, $status] = $this->client->CreateGroup($request)->wait();

        if ($status->code !== 0) {
            throw new \Exception($status->details);
        }

        return [
            'id' => $response->getId(),
            'name' => $response->getName(),
            'description' => $response->getDescription(),
        ];
    }
}
```

**Laravel membutuhkan**:
```bash
composer require grpc/grpc
composer require google/protobuf
```

---

## 📊 Architecture Diagram

```
┌─────────────────────────────────────────────┐
│         Web Browser / Mobile App            │
└──────────────┬──────────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────────┐
│      Laravel Application (Port 8000)        │
│  - Web Routes / API Routes                  │
│  - ChatGrpcService (gRPC Client)            │
└──────────────┬──────────────────────────────┘
               │
               ▼ gRPC calls
┌─────────────────────────────────────────────┐
│       Envoy Proxy (Port 8080)               │
│  - gRPC-Web to gRPC translation             │
└──────────────┬──────────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────────┐
│    gRPC Chat Server (Port 50051)            │
│  - Group Management                         │
│  - Bidirectional Streaming                  │
│  - Message Broadcasting                     │
└─────┬───────────────────────┬───────────────┘
      │                       │
      ▼                       ▼
┌──────────────┐      ┌───────────────┐
│ PostgreSQL   │      │    Redis      │
│ (Port 5433)  │      │  (Port 6380)  │
│              │      │               │
│ - groups     │      │ - pub/sub     │
│ - messages   │      │ - presence    │
│ - members    │      │ - cache       │
└──────────────┘      └───────────────┘
```

---

## 🎯 Quick Commands Cheatsheet

```powershell
# Start everything
docker-compose up -d

# Stop everything
docker-compose down

# Rebuild specific service
docker-compose build chat-grpc

# Restart specific service
docker-compose restart chat-grpc

# View logs
docker-compose logs -f chat-grpc

# Execute command in container
docker exec -it chat-grpc-server sh

# Access PostgreSQL
docker exec -it chat-postgres psql -U postgres -d chat_db

# Access Redis
docker exec -it chat-redis redis-cli

# List all containers
docker ps -a

# Remove all stopped containers
docker container prune

# Remove all unused images
docker image prune -a
```

---

## 📝 Next Steps

1. ✅ **Jalankan Docker**: `docker-compose up -d`
2. ✅ **Verifikasi**: Cek logs dan test endpoints
3. 🔄 **Update Laravel**: Ganti `ChatGrpcService.php` dengan real gRPC client
4. 🧪 **Testing**: Test dari Laravel ke gRPC microservice
5. 📱 **Frontend**: Integrate dengan frontend menggunakan gRPC-Web

---

## 💡 Tips

- Use `docker-compose logs -f` untuk debugging real-time
- Jangan lupa sync `JWT_SECRET` antara Laravel dan microservice
- Gunakan Envoy admin (port 9901) untuk monitoring
- Backup database sebelum `docker-compose down -v`
- Untuk production, gunakan TLS/SSL dan proper secrets management

---

## 🆘 Need Help?

- Check logs: `docker-compose logs -f`
- Check container status: `docker-compose ps`
- Rebuild from scratch: `docker-compose down -v && docker-compose build --no-cache && docker-compose up`
