<?php
$isAdmin = isset($title) && (
    strpos($title, 'Telemetry') !== false || 
    strpos($title, 'Result') !== false || 
    strpos($title, 'Login') !== false || 
    strpos($title, 'Error') !== false
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title) : 'LibreSpeed MVC'; ?></title>
    <?php if ($isAdmin): ?>
        <link rel="stylesheet" href="/assets/css/admin.css">
    <?php else: ?>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
        <style>
            :root {
                --bg-color: #0b0f19;
                --card-bg: rgba(17, 24, 39, 0.7);
                --border-color: rgba(255, 255, 255, 0.08);
                --text-primary: #f3f4f6;
                --text-secondary: #9ca3af;
                --accent-primary: #7c3aed;
                --accent-secondary: #06b6d4;
                --gradient: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%);
            }

            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }

            body {
                font-family: 'Outfit', sans-serif;
                background-color: var(--bg-color);
                color: var(--text-primary);
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                align-items: center;
                overflow-x: hidden;
                background-image: 
                    radial-gradient(at 0% 0%, rgba(124, 58, 237, 0.15) 0px, transparent 50%),
                    radial-gradient(at 100% 100%, rgba(6, 182, 212, 0.15) 0px, transparent 50%);
            }

            header {
                width: 100%;
                max-width: 1200px;
                padding: 2rem 1.5rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-bottom: 1px solid var(--border-color);
            }

            .logo {
                font-size: 1.5rem;
                font-weight: 800;
                background: var(--gradient);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                text-transform: uppercase;
                letter-spacing: 1px;
                text-decoration: none;
            }

            nav a {
                color: var(--text-secondary);
                text-decoration: none;
                margin-left: 1.5rem;
                font-weight: 500;
                transition: color 0.3s;
            }

            nav a:hover {
                color: var(--text-primary);
            }

            main {
                flex: 1;
                width: 100%;
                max-width: 1200px;
                padding: 3rem 1.5rem;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }

            footer {
                width: 100%;
                max-width: 1200px;
                padding: 2rem 1.5rem;
                text-align: center;
                color: var(--text-secondary);
                font-size: 0.875rem;
                border-top: 1px solid var(--border-color);
            }

            .glass-card {
                background: var(--card-bg);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border: 1px solid var(--border-color);
                border-radius: 16px;
                padding: 2.5rem;
                width: 100%;
                max-width: 600px;
                box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
            }
        </style>
    <?php endif; ?>
</head>
<body class="<?php echo $isAdmin ? 'admin-body' : ''; ?>">
    <?php if ($isAdmin): ?>
        <?php
        $isLoggedIn = false;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['logged']) && $_SESSION['logged'] === true) {
            $isLoggedIn = true;
        }
        $isLoginPage = isset($title) && strpos($title, 'Login') !== false;
        ?>
        <?php if (!$isLoginPage): ?>
            <header class="admin-header">
                <div class="header-container">
                    <a href="/" class="logo-link">
                        <span class="logo-symbol"></span>
                        LibreSpeed
                    </a>
                    <nav class="admin-nav">
                        <a href="/" id="nav-home">Home</a>
                        <a href="/stats" class="<?php echo (isset($title) && strpos($title, 'Logs') !== false) ? 'active' : ''; ?>">Stats</a>
                        <?php if ($isLoggedIn): ?>
                            <a href="/logout" class="btn-logout">Logout</a>
                        <?php endif; ?>
                    </nav>
                </div>
            </header>
        <?php endif; ?>

        <main class="admin-main">
            <?php echo $content; ?>
        </main>

        <footer class="admin-footer">
            <div class="footer-container">
                <p>&copy; <?php echo date('Y'); ?> LibreSpeed. Built on Vercel style.</p>
                <p>Vanilla CSS</p>
            </div>
        </footer>
    <?php else: ?>
        <header>
            <a href="/" class="logo" id="header-logo">LibreSpeed MVC</a>
            <nav>
                <a href="/" id="nav-home">Home</a>
                <a href="/stats" id="nav-stats">Stats</a>
            </nav>
        </header>

        <main>
            <?php echo $content; ?>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> LibreSpeed MVC. All Rights Reserved.</p>
        </footer>
    <?php endif; ?>
</body>
</html>
