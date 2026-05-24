@echo off
echo ===================================
echo Starting Soketi WebSocket Server
echo ===================================
echo.
echo Soketi server ishga tushmoqda...
echo URL: http://127.0.0.1:6001
echo Metrics: http://127.0.0.1:9601
echo.

docker-compose -f docker-compose.soketi.yml up -d

echo.
echo Soketi server ishga tushdi!
echo.
echo Loglarni ko'rish uchun: docker-compose -f docker-compose.soketi.yml logs -f
echo To'xtatish uchun: docker-compose -f docker-compose.soketi.yml down
echo.
pause
