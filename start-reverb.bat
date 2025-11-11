@echo off
echo ========================================
echo Starting WargaNet Real-Time Services
echo ========================================
echo.

echo Starting services in new windows...
echo.

echo [1/4] Starting Laravel Development Server...
start "Laravel Server" cmd /k "php artisan serve"
timeout /t 2 /nobreak >nul

echo [2/4] Starting Queue Worker...
start "Queue Worker" cmd /k "php artisan queue:work"
timeout /t 2 /nobreak >nul

echo [3/4] Starting Reverb WebSocket Server...
start "Reverb Server" cmd /k "php artisan reverb:start"
timeout /t 2 /nobreak >nul

echo [4/4] Starting Vite Dev Server...
start "Vite Dev" cmd /k "npm run dev"
timeout /t 2 /nobreak >nul

echo.
echo ========================================
echo All services started successfully!
echo ========================================
echo.
echo Services running:
echo - Laravel Server: http://localhost:8000
echo - Reverb WebSocket: ws://localhost:8080
echo - Vite Dev Server: http://localhost:5173
echo - Queue Worker: Running in background
echo.
echo Press any key to close this window...
pause >nul
