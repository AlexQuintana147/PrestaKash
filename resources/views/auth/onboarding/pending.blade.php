<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Completado - PrestaKash</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            padding: 50px 40px;
            text-align: center;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .icon-success {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: scaleIn 0.5s ease-out 0.2s both;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        .icon-success svg {
            width: 40px;
            height: 40px;
            stroke: white;
            stroke-width: 3;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        h1 {
            font-size: 28px;
            color: #1a202c;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .subtitle {
            font-size: 16px;
            color: #718096;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .info-box {
            background: #f7fafc;
            border-left: 4px solid #667eea;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: left;
        }

        .info-box p {
            color: #4a5568;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .info-box p:last-child {
            margin-bottom: 0;
        }

        .info-box strong {
            color: #2d3748;
        }

        .countdown {
            font-size: 48px;
            font-weight: 700;
            color: #667eea;
            margin: 20px 0;
            font-variant-numeric: tabular-nums;
        }

        .countdown-label {
            font-size: 14px;
            color: #718096;
            margin-bottom: 30px;
        }

        .btn {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .btn:active {
            transform: translateY(0);
        }

        @media (max-width: 640px) {
            .container {
                padding: 40px 25px;
            }

            h1 {
                font-size: 24px;
            }

            .countdown {
                font-size: 40px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-success">
            <svg viewBox="0 0 24 24">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>

        <h1>¡Registro Completado!</h1>
        <p class="subtitle">Tu información ha sido recibida correctamente</p>

        <div class="info-box">
            <p><strong>📧 Notificación por correo:</strong> Te enviaremos un email a <strong>{{ auth()->user()->email }}</strong> cuando tu perfil haya sido validado.</p>
            <p><strong>⏱️ Tiempo de validación:</strong> Nuestro equipo revisará tu información en menos de 24 horas.</p>
            <p><strong>✅ Próximos pasos:</strong> Una vez aprobado, podrás acceder a todas las funcionalidades de PrestaKash.</p>
        </div>

        <div class="countdown" id="countdown">10</div>
        <p class="countdown-label">Redirigiendo al inicio automáticamente...</p>

        <form action="{{ route('logout') }}" method="POST" id="logoutForm">
            @csrf
            <button type="submit" class="btn">Volver al Inicio</button>
        </form>
    </div>

    <script>
        let seconds = 10;
        const countdownEl = document.getElementById('countdown');
        const logoutForm = document.getElementById('logoutForm');

        const interval = setInterval(() => {
            seconds--;
            countdownEl.textContent = seconds;

            if (seconds <= 0) {
                clearInterval(interval);
                logoutForm.submit();
            }
        }, 1000);
    </script>
</body>
</html>
