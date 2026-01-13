<?php
require_once __DIR__ . '/config/database.php';
// Check admin auth
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin/login.php");
    exit;
}

$configFile = __DIR__ . '/config/launch.json';
$currentStatus = 'coming_soon';
if (file_exists($configFile)) {
    $data = json_decode(file_get_contents($configFile), true);
    $currentStatus = $data['status'] ?? 'coming_soon';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Launch Control Center - KV Wood Works</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #dc2626;
            --success: #22c55e;
            --dark: #1a1a1a;
            --light: #f5f5f5;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: #0f172a;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            overflow: hidden;
        }

        .control-panel {
            text-align: center;
            background: rgba(255, 255, 255, 0.05);
            padding: 40px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            max-width: 500px;
            width: 90%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #fff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p {
            color: #94a3b8;
            margin-bottom: 40px;
        }

        .launch-btn {
            background: linear-gradient(135deg, var(--primary), #b91c1c);
            color: white;
            border: none;
            padding: 20px 40px;
            font-size: 1.5rem;
            font-weight: 700;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 20px 25px -5px rgba(220, 38, 38, 0.4);
            position: relative;
            overflow: hidden;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .launch-btn:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 25px 30px -5px rgba(220, 38, 38, 0.5);
        }

        .launch-btn:active {
            transform: translateY(1px);
        }

        .launch-btn.launched {
            background: var(--success);
            box-shadow: 0 20px 25px -5px rgba(34, 197, 94, 0.4);
            pointer-events: none;
        }

        .status-indicator {
            margin-top: 30px;
            padding: 10px 20px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.1);
            display: inline-block;
            font-size: 0.9rem;
        }

        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }

        .status-dot.coming-soon {
            background: #eab308;
            box-shadow: 0 0 10px #eab308;
        }

        .status-dot.live {
            background: var(--success);
            box-shadow: 0 0 10px var(--success);
        }

        /* Rocket Animation */
        .rocket-container {
            position: fixed;
            bottom: -200px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            transition: bottom 2s cubic-bezier(0.45, 0, 0.55, 1);
            pointer-events: none;
        }

        .rocket-container.fly {
            bottom: 120vh;
        }

        .rocket {
            font-size: 15rem;
            filter: drop-shadow(0 0 20px rgba(255, 165, 0, 0.5));
            transform: rotate(-45deg);
        }

        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            background-color: #f00;
            animation: confetti-fall 3s linear forwards;
            top: -10px;
            border-radius: 50%;
        }

        @keyframes confetti-fall {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
            }

            100% {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }

        .reset-link {
            position: fixed;
            bottom: 20px;
            color: #475569;
            text-decoration: none;
            font-size: 0.8rem;
        }

        .reset-link:hover {
            color: #64748b;
        }
    </style>
</head>

<body>

    <div class="control-panel">
        <div class="icon-header">
            <i class="fas fa-space-shuttle" style="font-size: 4rem; color: white; margin-bottom: 20px;"></i>
        </div>
        <h1>Mission Control</h1>
        <p>KV Wood Works Website Launch System</p>

        <button id="launchBtn" class="launch-btn <?php echo $currentStatus === 'live' ? 'launched' : ''; ?>">
            <?php echo $currentStatus === 'live' ? '<i class="fas fa-check"></i> LIVE' : '<i class="fas fa-rocket"></i> LAUNCH WEBSITE'; ?>
        </button>

        <div class="status-indicator">
            <span class="status-dot <?php echo $currentStatus === 'live' ? 'live' : 'coming-soon'; ?>"></span>
            System Status: <strong id="statusText">
                <?php echo $currentStatus === 'live' ? 'ONLINE' : 'Global Countdown'; ?>
            </strong>
        </div>
    </div>

    <div class="rocket-container" id="rocket">
        <div class="rocket">🚀</div>
    </div>

    <a href="#" onclick="resetStatus()" class="reset-link">Reset to Coming Soon</a>

    <script>
        const launchBtn = document.getElementById('launchBtn');
        const rocket = document.getElementById('rocket');

        launchBtn.addEventListener('click', function () {
            if (this.classList.contains('launched')) return;

            if (confirm('Are you ready to LAUNCH the website to the public?')) {
                launchWebsite();
            }
        });

        function launchWebsite() {
            // Updated button state immediately
            launchBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> IGNITING...';

            fetch('launch_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'launch' })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Trigger Animation
                        rocket.classList.add('fly');
                        createConfetti();

                        setTimeout(() => {
                            launchBtn.classList.add('launched');
                            launchBtn.innerHTML = '<i class="fas fa-check"></i> WEBSITE LIVE';
                            document.querySelector('.status-dot').classList.remove('coming-soon');
                            document.querySelector('.status-dot').classList.add('live');
                            document.getElementById('statusText').innerText = 'ONLINE';

                            // Redirect to live site after animation
                            setTimeout(() => {
                                window.open('index.php', '_blank');
                            }, 2000);
                        }, 500); // Small delay for effect
                    }
                });
        }

        function resetStatus() {
            if (confirm('RESET to Coming Soon mode? (Local dev only)')) {
                fetch('launch_action.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'reset' })
                })
                    .then(() => location.reload());
            }
        }

        function createConfetti() {
            const colors = ['#f43f5e', '#ec4899', '#d946ef', '#a855f7', '#6366f1', '#3b82f6', '#06b6d4', '#14b8a6', '#22c55e', '#eab308', '#f97316'];

            for (let i = 0; i < 100; i++) {
                const confetti = document.createElement('div');
                confetti.classList.add('confetti');
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.animationDuration = (Math.random() * 2 + 2) + 's';
                document.body.appendChild(confetti);

                setTimeout(() => confetti.remove(), 4000);
            }
        }
    </script>
</body>

</html>