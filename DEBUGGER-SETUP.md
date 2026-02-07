# Backend Debugger Setup Guide

## ✅ Debugger Configuration Complete!

Your Laravel backend debugger has been configured with the following debug profiles:

### 🎯 Available Debug Configurations

1. **Listen for Xdebug (Laravel)** - Main debugging mode
   - Use this for debugging web requests
   - Start this, then make requests to your Laravel API

2. **Launch Laravel Artisan** - Debug artisan commands
   - Debug specific artisan commands directly

3. **Launch Laravel Server** - Debug with built-in server
   - Starts Laravel server with debugging enabled

4. **Debug Current PHP Script** - Debug any PHP file
   - Debug the currently open PHP file

## 📦 Xdebug Installation Required

Xdebug is not currently installed. Follow these steps:

### Option 1: Manual Download (Recommended)

1. **Download Xdebug:**
   - Visit: https://xdebug.org/download
   - Download: **PHP 8.5 VC16 x86_64 (64 bit)** version
   - Or direct link: https://xdebug.org/files/php_xdebug-3.4.0-8.5-vs16-x86_64.dll

2. **Install the DLL:**

   ```powershell
   # Save the downloaded file to:
   C:\php-8.5.1\ext\php_xdebug.dll
   ```

3. **Configure PHP:**
   Add to `C:\php-8.5.1\php.ini`:

   ```ini
   [Xdebug]
   zend_extension=xdebug
   xdebug.mode=debug,develop
   xdebug.start_with_request=yes
   xdebug.client_port=9003
   xdebug.client_host=127.0.0.1
   xdebug.log=C:\php-8.5.1\xdebug.log
   ```

4. **Verify Installation:**

   ```powershell
   php -v
   # Should show "with Xdebug"

   php -m | Select-String "xdebug"
   # Should show "xdebug"
   ```

### Option 2: Auto-Configure (if DLL exists)

If you already have the Xdebug DLL, run:

```powershell
.\setup-debugger.ps1
```

## 🚀 How to Use the Debugger

### Step 1: Start Debugging

1. Open VS Code
2. Go to "Run and Debug" panel (Ctrl+Shift+D)
3. Select **"Listen for Xdebug (Laravel)"**
4. Click the green play button or press **F5**

### Step 2: Set Breakpoints

1. Open any PHP file (e.g., `backend/app/Http/Controllers/WeatherController.php`)
2. Click in the gutter (left of line numbers) to set a red breakpoint
3. The debugger will pause when that line is executed

### Step 3: Make a Request

1. Start your Laravel server:

   ```bash
   cd backend
   php artisan serve
   ```

2. Make a request to trigger your breakpoint:

   ```bash
   curl "http://localhost:8000/api/weather?city=London"
   ```

3. VS Code will pause at your breakpoint!

### Step 4: Debug Controls

- **Continue (F5)**: Resume execution
- **Step Over (F10)**: Execute current line
- **Step Into (F11)**: Step into functions
- **Step Out (Shift+F11)**: Step out of functions
- **Restart (Ctrl+Shift+F5)**: Restart debugging
- **Stop (Shift+F5)**: Stop debugging

## 🔍 Debugging Features

### Variables Panel

- View all variables in current scope
- Inspect arrays and objects
- Watch specific expressions

### Call Stack

- See the execution path
- Click to jump to different stack frames

### Debug Console

- Execute PHP code in current context
- Evaluate expressions on the fly

### Breakpoints Panel

- Manage all breakpoints
- Enable/disable breakpoints
- Set conditional breakpoints

## 💡 Pro Tips

### Conditional Breakpoints

Right-click on a breakpoint → Edit Breakpoint → Add condition:

```php
$city == 'London'
```

### Logpoints

Add messages to output without stopping:
Right-click → Add Logpoint

### Debug Artisan Commands

Select "Launch Laravel Artisan" and debug any command:

```bash
php artisan migrate
php artisan db:seed
```

## 🛠️ Troubleshooting

### Debugger Not Connecting?

1. **Check Xdebug is loaded:**

   ```bash
   php -v
   ```

   Should show "with Xdebug"

2. **Check port 9003 is free:**

   ```powershell
   netstat -ano | findstr :9003
   ```

3. **Restart VS Code** after installing Xdebug

4. **Check Laravel server is running:**
   ```bash
   php artisan serve
   ```

### Breakpoints Not Hitting?

1. Ensure "Listen for Xdebug" is running (green status bar)
2. Check breakpoint is in actual executed code path
3. Verify `xdebug.mode=debug` in php.ini
4. Check Xdebug log: `C:\php-8.5.1\xdebug.log`

### Performance Issues?

Xdebug can slow down PHP. When not debugging:

- Stop the debug session (Shift+F5)
- Or temporarily disable: `xdebug.mode=off` in php.ini

## 📚 Additional Resources

- [Xdebug Documentation](https://xdebug.org/docs/)
- [VS Code PHP Debugging](https://code.visualstudio.com/docs/languages/php)
- [Laravel Debugging Guide](https://laravel.com/docs/debugging)

## ✨ VS Code Extensions Installed

- ✅ **PHP Debug** (xdebug.php-debug)
- ✅ **PHP Extension Pack** (xdebug.php-pack)
- ✅ **PHP Intelephense** (bmewburn.vscode-intelephense-client)

Your debugger is ready! Install Xdebug and start debugging! 🐛
