@echo off
echo ========================================
echo  Laravel Backend Debugger Setup
echo ========================================
echo.

REM Get PHP info
echo Checking PHP installation...
php -v | findstr "PHP"
echo.

REM Check if Xdebug is already installed
php -m | findstr "xdebug" >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo [OK] Xdebug is already installed!
    goto :configure
)

echo [!] Xdebug is not installed.
echo.
echo To install Xdebug for PHP 8.5:
echo.
echo 1. Visit: https://xdebug.org/download
echo 2. Download Xdebug for PHP 8.5 (Windows x64)
echo 3. Copy the .dll file to: C:\php-8.5.1\ext\
echo 4. Rename it to: php_xdebug.dll
echo 5. Run this script again
echo.
echo OR
echo.
echo Run this PowerShell command to download automatically:
echo.
echo Invoke-WebRequest -Uri "https://xdebug.org/files/php_xdebug-3.4.0-8.5-vs16-x86_64.dll" -OutFile "C:\php-8.5.1\ext\php_xdebug.dll"
echo.
pause
exit /b 1

:configure
echo.
echo ========================================
echo  Configuring Xdebug for VS Code
echo ========================================
echo.

REM Check if xdebug configuration exists in php.ini
findstr /C:"zend_extension=xdebug" "C:\php-8.5.1\php.ini" >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo Adding Xdebug configuration to php.ini...
    echo. >> "C:\php-8.5.1\php.ini"
    echo [Xdebug] >> "C:\php-8.5.1\php.ini"
    echo zend_extension=xdebug >> "C:\php-8.5.1\php.ini"
    echo xdebug.mode=debug,develop >> "C:\php-8.5.1\php.ini"
    echo xdebug.start_with_request=yes >> "C:\php-8.5.1\php.ini"
    echo xdebug.client_port=9003 >> "C:\php-8.5.1\php.ini"
    echo xdebug.client_host=127.0.0.1 >> "C:\php-8.5.1\php.ini"
    echo xdebug.log=C:\php-8.5.1\xdebug.log >> "C:\php-8.5.1\php.ini"
    echo.
    echo [OK] Xdebug configuration added!
) else (
    echo [OK] Xdebug already configured in php.ini
)

echo.
echo ========================================
echo  Debugger Setup Complete!
echo ========================================
echo.
echo Next steps:
echo 1. Restart VS Code to apply changes
echo 2. Set breakpoints in your PHP code
echo 3. Press F5 or use "Run and Debug" panel
echo 4. Select "Listen for Xdebug (Laravel)"
echo 5. Make a request to your Laravel app
echo.
echo Available debug configurations:
echo  - Listen for Xdebug (Laravel)
echo  - Launch Laravel Artisan
echo  - Launch Laravel Server
echo  - Debug Current PHP Script
echo.
pause
