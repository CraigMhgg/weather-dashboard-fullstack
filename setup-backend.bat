@echo off
echo Setting up Laravel Backend...
echo.

cd /d "%~dp0"

REM Wait for any running Composer processes to complete
echo Waiting for Composer installation to complete...
timeout /t 5 /nobreak >nul

REM Copy remaining Laravel files if backend-temp exists
if exist "backend-temp" (
    echo Copying Laravel files to backend directory...
    xcopy /E /I /Y "backend-temp\*" "backend\"
    rmdir /S /Q "backend-temp"
    echo Files copied successfully!
)

REM Navigate to backend directory
cd backend

REM Check if .env exists, if not copy from .env.example
if not exist ".env" (
    if exist ".env.example" (
        echo Creating .env file...
        copy ".env.example" ".env"
    )
)

REM Merge custom environment variables
if exist ".env.local" (
    echo Adding custom environment variables...
    type ".env.local" >> ".env"
)

REM Generate application key
echo Generating application key...
php artisan key:generate

echo.
echo ========================================
echo Laravel Backend Setup Complete!
echo ========================================
echo.
echo To start the Laravel server, run:
echo   cd backend
echo   php artisan serve
echo.
echo The API will be available at: http://localhost:8000
echo.
pause
