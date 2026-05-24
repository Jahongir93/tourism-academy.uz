@echo off
echo ========================================
echo YUZNI TANISH TIZIMINI ISHGA TUSHIRISH
echo ========================================
echo.

echo PHP Server ishga tushirilmoqda...
start cmd /k "cd /d C:\xampp\htdocs\local.uz && php artisan serve --port=8000"

echo.
echo Tizim tayyor!
echo.
echo Browser orqali kirish:
echo http://localhost:8000/attendance/monitoring
echo.
echo ========================================
pause