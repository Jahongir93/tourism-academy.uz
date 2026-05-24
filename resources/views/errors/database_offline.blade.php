<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ma'lumotlar bazasi mavjud emas</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        .icon {
            font-size: 72px;
            margin-bottom: 20px;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .demo-mode {
            background: #f0f9ff;
            border: 1px solid #0284c7;
            border-radius: 5px;
            padding: 15px;
            margin-top: 20px;
        }
        .demo-mode h3 {
            color: #0284c7;
            margin: 0 0 10px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #5a67d8;
        }
        .status {
            display: inline-block;
            padding: 5px 10px;
            background: #fef2f2;
            color: #dc2626;
            border-radius: 3px;
            font-size: 14px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">⚠️</div>
        <h1>Ma'lumotlar bazasi mavjud emas</h1>
        <div class="status">Oflayn rejim</div>

        @if(config('database_fallback.demo_mode'))
        <div class="demo-mode">
            <h3>Demo rejim faol</h3>
            <p>Siz demo rejimda ishlayapsiz. Ba'zi funksiyalar cheklangan bo'lishi mumkin.</p>
        </div>
        @else
        <p>
            Hozirda ma'lumotlar bazasiga ulanib bo'lmadi.
            Tizim fallback rejimda ishlayapti va ma'lumotlar vaqtincha faylda saqlanmoqda.
        </p>
        @endif

        <p>
            <strong>Sabablari:</strong><br>
            • MySQL serveri ishlamayotgan bo'lishi mumkin<br>
            • Ulanish ma'lumotlari noto'g'ri<br>
            • Hosting chegaralanishlari
        </p>

        <a href="{{ route('home') }}" class="btn">Bosh sahifaga qaytish</a>
    </div>
</body>
</html>