@echo off
echo 🚀 Starting gRPC Chat Microservice...
echo.

REM Copy .env if not exists
if not exist .env (
    echo 📝 Creating .env file...
    copy .env.example .env
    echo ⚠️  Please edit .env file and set your JWT_SECRET before continuing
    echo Press any key to continue after editing .env...
    pause
)

echo 🐳 Starting Docker containers...
docker-compose up -d

echo.
echo ⏳ Waiting for services to be ready...
timeout /t 10 /nobreak > nul

echo.
echo ✅ Services started successfully!
echo.
echo 📡 Service URLs:
echo    - gRPC Server: localhost:50051
echo    - gRPC-Web Proxy: http://localhost:8080
echo    - PostgreSQL: localhost:5433
echo    - Redis: localhost:6380
echo    - Envoy Admin: http://localhost:9901
echo.
echo 📋 Useful commands:
echo    - View logs: docker-compose logs -f
echo    - Stop services: docker-compose down
echo    - Restart: docker-compose restart
echo.

REM Show logs
echo 📜 Showing server logs (Ctrl+C to exit)...
docker-compose logs -f chat-grpc
