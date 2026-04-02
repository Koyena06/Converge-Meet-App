<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Converge – Meetup App</title>
    
    <link rel="stylesheet" href="/Converge-Meet-App-main/assets/css/style.css">
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Lora:wght@400;600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>

    <header class="site-header">
        <div class="header-inner">
            
            <p>
            <a href="/Converge-Meet-App-main/assets/image/logo.png">
                <img src="/Converge-Meet-App-main/assets/image/logo.png" alt="Converge Logo" class="logo-img">
            </a>
            <a href="/Converge-Meet-App-main/index.php" class="logo-link">
                <p class="logo-wordmark">Converge </p>
            </a>
            </p>
            <nav class="header-nav">
                <ul>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li>
                            <a href="/Converge-Meet-App-main/user/dashboard.php" 
                               class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>">
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="/Converge-Meet-App-main/friends/friends.php" 
                               class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'friends.php' ? 'active' : '' ?>">
                                Friends
                            </a>
                        </li>
                        <li>
                            <a href="/Converge-Meet-App-main/location/map.php" 
                               class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'map.php' ? 'active' : '' ?>">
                                Map
                            </a>
                        </li>
                        <li>
                            <a href="/Converge-Meet-App-main/events/view_events.php" 
                               class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'view_events.php' ? 'active' : '' ?>">
                                Events
                            </a>
                        </li>
                        <li>
                            <a href="/Converge-Meet-App-main/places/suggest_places.php" 
                               class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'suggest_places.php' ? 'active' : '' ?>">
                                Places
                            </a>
                        </li>
                        <li>
                            <a href="/Converge-Meet-App-main/user/profile.php" class="nav-link">
                                <?= htmlspecialchars($_SESSION['username'] ?? 'Profile'); ?>
                            </a>
                        </li>
                        <li>
                            <a href="/Converge-Meet-App-main/auth/logout.php" class="btn btn-outline">
                                Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <li>
                            <a href="/Converge-Meet-App-main/auth/login.php" class="nav-link">
                                Login
                            </a>
                        </li>
                        <li>
                            <a href="/Converge-Meet-App-main/auth/register.php" class="btn btn-primary">
                                Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>

        </div>
    </header>