<?php
require_once __DIR__ . '/config/database.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coming Soon - KV Wood Works</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #dc2626;
            --dark: #0f172a;
            --light: #f8fafc;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--dark);
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            text-align: center;
            overflow: hidden;
            position: relative;
        }

        .container {
            position: relative;
            z-index: 10;
            padding: 20px;
        }

        .logo {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
            letter-spacing: -1px;
        }

        .logo span {
            color: var(--primary);
        }

        h1 {
            font-size: 4rem;
            margin: 0;
            line-height: 1.1;
            background: linear-gradient(135deg, #fff 0%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p.subtitle {
            font-size: 1.5rem;
            color: #94a3b8;
            margin-top: 10px;
            margin-bottom: 40px;
            font-weight: 300;
        }

        .message {
            background: rgba(255, 255, 255, 0.05);
            padding: 20px 40px;
            border-radius: 50px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: inline-flex;
            align-items: center;
            gap: 15px;
            backdrop-filter: blur(10px);
        }

        .dot {
            width: 10px;
            height: 10px;
            background: var(--primary);
            border-radius: 50%;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.5);
                opacity: 0.5;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .bg-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                radial-gradient(circle at 20% 30%, rgba(220, 38, 38, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
            z-index: 1;
        }

        .admin-login {
            position: fixed;
            bottom: 20px;
            right: 20px;
            opacity: 0.3;
            color: white;
            text-decoration: none;
            font-size: 0.8rem;
            z-index: 20;
            transition: opacity 0.3s;
        }

        .admin-login:hover {
            opacity: 1;
        }
    </style>
</head>

<body>

    <div class="bg-pattern"></div>

    <div class="container">
        <div class="logo">KV <span>Wood Works</span></div>
        <h1>Something Amazing <br> Is Coming Soon</h1>
        <p class="subtitle">Premium Home Interiors & Wooden Works</p>

        <div class="message">
            <span class="dot"></span>
            <span>Site Launching shortly...</span>
        </div>
    </div>

    <a href="login.php" class="admin-login"><i class="fas fa-lock"></i> Admin Login</a>

    <script>
        // Use PHP to inject the initial check time to avoid cache issues
        const cacheBuster = '<?php echo time(); ?>';

        // Auto-Refresh Logic
        // Checks status every 2 seconds
        setInterval(function () {
            fetch('check_status.php?t=' + Date.now())
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'live') {
                        // Reload page to show real website
                        window.location.reload();
                    }
                })
                .catch(err => console.log('Waiting for network...'));
        }, 2000);
    </script>
</body>

</html>