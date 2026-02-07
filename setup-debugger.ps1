# Laravel Backend Debugger Setup Script

Write-Host "========================================" -ForegroundColor Cyan
Write-Host " Laravel Backend Debugger Setup" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Get PHP info
Write-Host "Checking PHP installation..." -ForegroundColor Yellow
php -v
Write-Host ""

# Check if Xdebug is already installed
$xdebugInstalled = php -m | Select-String "xdebug"

if ($xdebugInstalled) {
    Write-Host "[OK] Xdebug is already installed!" -ForegroundColor Green
} else {
    Write-Host "[!] Xdebug is not installed." -ForegroundColor Red
    Write-Host ""
    Write-Host "Installing Xdebug for PHP 8.5..." -ForegroundColor Yellow
    
    $xdebugUrl = "https://xdebug.org/files/php_xdebug-3.4.0-8.5-vs16-x86_64.dll"
    $xdebugPath = "C:\php-8.5.1\ext\php_xdebug.dll"
    
    try {
        Write-Host "Downloading Xdebug..."
        Invoke-WebRequest -Uri $xdebugUrl -OutFile $xdebugPath
        Write-Host "[OK] Xdebug downloaded successfully!" -ForegroundColor Green
    } catch {
        Write-Host "[ERROR] Failed to download Xdebug: $_" -ForegroundColor Red
        Write-Host ""
        Write-Host "Please download manually from: https://xdebug.org/download" -ForegroundColor Yellow
        pause
        exit 1
    }
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host " Configuring Xdebug for VS Code" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Check if xdebug configuration exists in php.ini
$phpIniPath = "C:\php-8.5.1\php.ini"
$xdebugConfig = Get-Content $phpIniPath | Select-String "zend_extension=xdebug"

if (-not $xdebugConfig) {
    Write-Host "Adding Xdebug configuration to php.ini..." -ForegroundColor Yellow
    
    $xdebugSettings = @"

[Xdebug]
zend_extension=xdebug
xdebug.mode=debug,develop
xdebug.start_with_request=yes
xdebug.client_port=9003
xdebug.client_host=127.0.0.1
xdebug.log=C:\php-8.5.1\xdebug.log
xdebug.log_level=7
"@
    
    Add-Content -Path $phpIniPath -Value $xdebugSettings
    Write-Host "[OK] Xdebug configuration added!" -ForegroundColor Green
} else {
    Write-Host "[OK] Xdebug already configured in php.ini" -ForegroundColor Green
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host " Verifying Installation" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Verify Xdebug is loaded
$xdebugLoaded = php -m | Select-String "xdebug"
if ($xdebugLoaded) {
    Write-Host "[OK] Xdebug is loaded and ready!" -ForegroundColor Green
    php -v | Select-String "Xdebug"
} else {
    Write-Host "[WARNING] Xdebug not loaded. You may need to restart your terminal." -ForegroundColor Yellow
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host " Debugger Setup Complete!" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Green
Write-Host "1. Restart VS Code to apply changes"
Write-Host "2. Set breakpoints in your PHP code"
Write-Host "3. Press F5 or use 'Run and Debug' panel"
Write-Host "4. Select 'Listen for Xdebug (Laravel)'"
Write-Host "5. Make a request to your Laravel app"
Write-Host ""
Write-Host "Available debug configurations:" -ForegroundColor Cyan
Write-Host " - Listen for Xdebug (Laravel)" -ForegroundColor White
Write-Host " - Launch Laravel Artisan" -ForegroundColor White
Write-Host " - Launch Laravel Server" -ForegroundColor White
Write-Host " - Debug Current PHP Script" -ForegroundColor White
Write-Host ""
pause
