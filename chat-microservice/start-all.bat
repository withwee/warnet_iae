@echo off
echo ========================================
echo   gRPC Chat Microservice Launcher
echo ========================================
echo.

REM Check if Docker is running
docker info >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Docker is not running!
    echo Please start Docker Desktop first.
    pause
    exit /b 1
)

echo [1/4] Stopping existing containers...
docker-compose down

echo.
echo [2/4] Building Docker images...
docker-compose build

echo.
echo [3/4] Starting all services...
docker-compose up -d

echo.
echo [4/4] Waiting for services to be ready...
timeout /t 5 /nobreak >nul

echo.
echo ========================================
echo   Service Status:
echo ========================================
docker-compose ps

echo.
echo ========================================
echo   Available Endpoints:
echo ========================================
echo   - gRPC Server:     localhost:50051
echo   - gRPC-Web Proxy:  http://localhost:8080
echo   - PostgreSQL:      localhost:5433
echo   - Redis:           localhost:6380
echo   - Envoy Admin:     http://localhost:9901
echo ========================================
echo.
echo To view logs, run: docker-compose logs -f
echo To stop services, run: docker-compose down
echo.
pause
