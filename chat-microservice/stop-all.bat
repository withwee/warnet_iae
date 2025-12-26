@echo off
echo ========================================
echo   Stopping gRPC Chat Microservice
echo ========================================
echo.

echo Stopping all containers...
docker-compose down

echo.
echo ========================================
echo   All services stopped!
echo ========================================
echo.
pause
