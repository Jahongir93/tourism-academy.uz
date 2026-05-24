<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vakansiya arizangiz bo'yicha javob</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #0066CC, #0052a3);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .message-box {
            background: #f8f9fa;
            border-left: 4px solid #0066CC;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
        .vacancy-info {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .vacancy-info h3 {
            margin: 0 0 10px 0;
            color: #0066CC;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
        .footer a {
            color: #0066CC;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Tourism Academy Samarkand</h1>
        </div>

        <div class="content">
            <p class="greeting">Hurmatli {{ $application->full_name }},</p>

            <div class="vacancy-info">
                <h3>{{ $application->vacancy->title }}</h3>
                <p style="margin: 0; color: #666;">
                    Siz ushbu vakansiya uchun ariza topshirgan edingiz.
                </p>
            </div>

            <p>Sizning arizangiz bo'yicha bizdan javob:</p>

            <div class="message-box">
                {!! nl2br(e($message)) !!}
            </div>

            <p>
                Agar sizda savollar bo'lsa, bizga quyidagi manzil orqali murojaat qilishingiz mumkin:<br>
                <strong>Email:</strong> hr@tourismacademy.uz<br>
                <strong>Telefon:</strong> +998 90 123-45-67
            </p>

            <p>Hurmat bilan,<br><strong>Tourism Academy HR Bo'limi</strong></p>
        </div>

        <div class="footer">
            <p>
                Bu xabar avtomatik ravishda yuborilgan.<br>
                <a href="{{ config('app.url') }}">Tourism Academy Samarkand</a>
            </p>
        </div>
    </div>
</body>
</html>
