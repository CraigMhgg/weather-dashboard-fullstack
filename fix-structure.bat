@echo off
echo Reorganizing frontend directory structure...
echo.

cd /d "%~dp0"

REM Move all files from frontend\weather-dashboard-fullstack to frontend
echo Moving files from frontend\weather-dashboard-fullstack to frontend...
xcopy /E /I /Y "frontend\weather-dashboard-fullstack\*" "frontend\" >nul

REM Remove the now-empty nested directory
echo Removing redundant directory...
rmdir /S /Q "frontend\weather-dashboard-fullstack" 2>nul

echo.
echo ✓ Frontend directory structure fixed!
echo.

REM Clean up backend-temp if Laravel installation is complete
if exist "backend\artisan" (
    if exist "backend-temp" (
        echo Cleaning up backend-temp directory...
        rmdir /S /Q "backend-temp" 2>nul
        echo ✓ Backend-temp removed!
    )
)

echo.
echo Directory structure reorganized successfully!
echo.
pause
