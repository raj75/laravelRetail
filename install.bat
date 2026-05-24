@echo off
echo ============================================
echo  LaravelRetail - Installation Script
echo ============================================
cd /d "%~dp0"
set PATH=C:\xampp\php;%PATH%

where php >nul 2>&1
if errorlevel 1 (
    echo ERROR: PHP not found. Add C:\xampp\php to your PATH.
    pause
    exit /b 1
)

where composer >nul 2>&1
if errorlevel 1 (
    echo ERROR: Composer not found. Install from https://getcomposer.org
    pause
    exit /b 1
)

echo [1/6] Installing Composer dependencies...
call composer install --no-interaction
if errorlevel 1 pause & exit /b 1

if not exist .env (
    echo [2/6] Creating .env file...
    copy .env.example .env
) else (
    echo [2/6] .env already exists, skipping copy.
)

echo [3/6] Generating application key...
php artisan key:generate --force

echo [4/6] Create MySQL database "laravel_retail" in phpMyAdmin if not exists.
echo       Then press any key to run migrations...
pause

echo [5/6] Running migrations and seeders...
php artisan migrate --force
php artisan db:seed --force

echo [6/6] Linking storage...
php artisan storage:link 2>nul

echo.
echo ============================================
echo  Installation complete!
echo  URL: http://localhost/laravelRetail/public
echo  Login: admin@laravelretail.com / password
echo ============================================
pause
