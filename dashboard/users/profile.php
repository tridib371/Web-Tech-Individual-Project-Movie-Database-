<?php
session_start();
require_once dirname(__DIR__, 2) . '/inc/db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

// Get user info from session
$userId = $_SESSION['user_id'];
$userRole = $_SESSION['user_role'] ?? '';
$userName = $_SESSION['user_name'] ?? 'User';

// Fetch user details
$stmt = $conn->prepare("SELECT name, email, role FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Determine dashboard link based on role
$dashboardLink = match ($userRole) {
    'admin'    => '../admin.php',
    'general'  => '../general.php',
    'premium'  => '../premium.php',
    default    => '../../login.php'
};

// Get the current theme from cookie
$currentTheme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'dark';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced User Profile | MovieDB</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        :root {
            --primary: #4361ee;
            --secondary: #3a0ca3;
            --accent: #4cc9f0;
            --danger: #f72585;
            --success: #2ec4b6;
            
            --bg-primary: #0f0c29;
            --bg-secondary: #302b63;
            --bg-tertiary: #24243e;
            
            --card-bg: rgba(20, 25, 50, 0.7);
            --card-border: rgba(100, 150, 255, 0.2);
            
            --text-primary: #e0e0e0;
            --text-secondary: #a0a0a0;
            
            --shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
        }

        body.light-mode {
            --bg-primary: #f0f2f5;
            --bg-secondary: #ffffff;
            --bg-tertiary: #e4e6eb;
            
            --card-bg: rgba(255, 255, 255, 0.85);
            --card-border: rgba(0, 0, 0, 0.1);
            
            --text-primary: #333333;
            --text-secondary: #666666;
            
            --shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        body {
            background: linear-gradient(135deg, var(--bg-primary), var(--bg-secondary), var(--bg-tertiary));
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
            transition: background 0.5s ease, color 0.3s ease;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 10% 20%, rgba(255, 0, 150, 0.1) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(0, 200, 255, 0.1) 0%, transparent 20%),
                radial-gradient(circle at 50% 50%, rgba(100, 255, 200, 0.05) 0%, transparent 30%);
            z-index: -1;
        }

        .profile-container {
            width: 100%;
            max-width: 1200px;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 30px;
            width: 100%;
            position: relative;
        }

        .profile-header h1 {
            font-size: 3.5rem;
            color: #fff;
            text-shadow: 0 0 15px rgba(0, 200, 255, 0.7);
            margin-bottom: 15px;
            letter-spacing: 2px;
            position: relative;
            display: inline-block;
        }

        .light-mode .profile-header h1 {
            color: var(--primary);
            text-shadow: 0 0 10px rgba(67, 97, 238, 0.3);
        }

        .profile-header h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 150px;
            height: 4px;
            background: linear-gradient(90deg, transparent, var(--accent), transparent);
            border-radius: 2px;
        }

        .profile-header p {
            font-size: 1.2rem;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .dashboard-container {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: var(--shadow);
            padding: 40px;
            width: 100%;
            max-width: 800px;
            position: relative;
            overflow: hidden;
            border: 1px solid var(--card-border);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .dashboard-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
        }

        .dashboard-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: 
                linear-gradient(45deg, transparent, rgba(0, 200, 255, 0.1), transparent),
                linear-gradient(135deg, transparent, rgba(255, 0, 150, 0.1), transparent);
            transform: rotate(30deg);
            z-index: -1;
            animation: animateBorder 6s linear infinite;
        }

        @keyframes animateBorder {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* NEW AVATAR DESIGN - START */
        .user-avatar {
            position: relative;
            width: 180px;
            height: 180px;
            margin: 0 auto 40px;
            perspective: 1000px;
        }

        .avatar-container {
            width: 100%;
            height: 100%;
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .user-avatar:hover .avatar-container {
            transform: rotateY(180deg);
        }

        .avatar-front, .avatar-back {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .avatar-front {
            background: linear-gradient(135deg, #8a2be2, #4361ee, #4cc9f0);
            color: white;
        }

        .avatar-back {
            background: linear-gradient(135deg, #f72585, #b5179e, #7209b7);
            transform: rotateY(180deg);
            color: white;
        }

        .avatar-initials {
            font-size: 4.5rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 2;
        }

        .avatar-back i {
            font-size: 4rem;
            animation: spin 8s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .avatar-pulse {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: pulse 3s infinite ease-out;
            z-index: 1;
        }

        .avatar-pulse.delay-1 {
            animation-delay: 0.5s;
        }

        .avatar-pulse.delay-2 {
            animation-delay: 1s;
        }

        @keyframes pulse {
            0% {
                transform: scale(0.8);
                opacity: 0.7;
            }
            70% {
                transform: scale(1.4);
                opacity: 0;
            }
            100% {
                transform: scale(1.4);
                opacity: 0;
            }
        }

        .avatar-glass {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 50%;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(5px);
            border-radius: 50% 50% 0 0;
            z-index: 2;
        }
        /* NEW AVATAR DESIGN - END */

        .user-info {
            margin-bottom: 40px;
        }

        .info-card {
            background: rgba(30, 35, 60, 0.5);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid var(--accent);
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .light-mode .info-card {
            background: rgba(240, 240, 245, 0.8);
        }

        .info-card:hover {
            transform: translateX(10px);
        }

        .info-card p {
            font-size: 1.2rem;
            margin: 12px 0;
            display: flex;
            align-items: center;
            color: var(--text-primary);
        }

        .info-card strong {
            min-width: 100px;
            display: inline-block;
            color: var(--accent);
            margin-right: 15px;
            font-weight: 600;
        }

        .info-card i {
            margin-right: 15px;
            color: var(--accent);
            width: 24px;
            text-align: center;
            font-size: 1.3rem;
        }

        .role-badge {
            display: inline-block;
            padding: 6px 18px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.9rem;
            margin-left: 15px;
            background: linear-gradient(45deg, var(--danger), var(--secondary));
            color: white;
            box-shadow: 0 4px 15px rgba(247, 37, 133, 0.3);
        }

        .stats-container {
            display: flex;
            justify-content: space-between;
            margin: 40px 0;
            gap: 20px;
        }

        .stat-card {
            background: rgba(30, 35, 60, 0.5);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            flex: 1;
            min-width: 120px;
            border: 1px solid var(--card-border);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .light-mode .stat-card {
            background: rgba(240, 240, 245, 0.8);
        }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--accent);
            margin-bottom: 10px;
            text-shadow: 0 0 10px rgba(0, 200, 255, 0.5);
        }

        .stat-label {
            font-size: 1rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .button-group {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-top: 30px;
            justify-content: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 15px 35px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            border: none;
            outline: none;
            min-width: 220px;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        }

        .btn i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .btn-dashboard {
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            color: white;
        }

        .btn-logout {
            background: linear-gradient(45deg, var(--danger), #b5179e);
            color: white;
        }

        .movie-icons {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 40px 0;
            flex-wrap: wrap;
        }

        .movie-icon {
            font-size: 2.5rem;
            color: rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .light-mode .movie-icon {
            color: rgba(0, 0, 0, 0.2);
        }

        .movie-icon:hover {
            color: var(--accent);
            transform: translateY(-10px) scale(1.2);
            text-shadow: 0 0 15px rgba(0, 200, 255, 0.7);
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            color: var(--text-secondary);
            font-size: 0.9rem;
            width: 100%;
            padding: 20px;
        }

        /* Theme Toggle Button */
        .theme-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            border-radius: 50px;
            padding: 12px 24px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: var(--shadow);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .theme-toggle:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        }

        .theme-toggle i {
            font-size: 1.2rem;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 30px 20px;
            }
            
            .stats-container {
                flex-direction: column;
            }
            
            .profile-header h1 {
                font-size: 2.5rem;
            }
            
            .button-group {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                max-width: 300px;
            }
            
            .theme-toggle {
                top: 10px;
                right: 10px;
                padding: 10px 20px;
                font-size: 0.9rem;
            }
            
            .user-avatar {
                width: 160px;
                height: 160px;
            }
        }

        @media (max-width: 480px) {
            .info-card p {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .info-card strong {
                margin-bottom: 8px;
            }
            
            .role-badge {
                margin-left: 0;
                margin-top: 10px;
            }
            
            .profile-header h1 {
                font-size: 2rem;
            }
            
            .theme-toggle {
                top: 5px;
                right: 5px;
                padding: 8px 16px;
                font-size: 0.8rem;
            }
            
            .user-avatar {
                width: 140px;
                height: 140px;
            }
            
            .avatar-initials {
                font-size: 3.5rem;
            }
        }

        /* Animation for the stats */
        @keyframes countUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .stat-card {
            animation: countUp 0.8s ease-out forwards;
        }

        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.3s; }
        .stat-card:nth-child(3) { animation-delay: 0.5s; }

        /* Animation for the info cards */
        .info-card {
            animation: slideIn 0.6s ease-out forwards;
            opacity: 0;
        }

        @keyframes slideIn {
            from { transform: translateX(-30px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .info-card:nth-child(1) { animation-delay: 0.2s; }
        .info-card:nth-child(2) { animation-delay: 0.4s; }
        .info-card:nth-child(3) { animation-delay: 0.6s; }
    </style>
</head>
<body class="<?= $currentTheme ?>-mode">
    <button class="theme-toggle" id="themeToggle">
        <i class="fas fa-moon"></i>
        <span>Switch Theme</span>
    </button>
    
    <div class="profile-container">
        <div class="profile-header">
            <h1>USER PROFILE</h1>
            <p>Manage your account details and preferences</p>
        </div>
        
        <div class="dashboard-container">
            <!-- NEW AVATAR DESIGN -->
            <div class="user-avatar">
                <div class="avatar-container">
                    <div class="avatar-front">
                        <div class="avatar-pulse"></div>
                        <div class="avatar-pulse delay-1"></div>
                        <div class="avatar-pulse delay-2"></div>
                        <div class="avatar-glass"></div>
                        <div class="avatar-initials">AJ</div>
                    </div>
                    <div class="avatar-back">
                        <i class="fas fa-film"></i>
                    </div>
                </div>
            </div>
            
            <div class="user-info">
                <div class="info-card">
                    <p>
                        <i class="fas fa-user"></i>
                        <strong>Name:</strong>
                        <span><?= htmlspecialchars($user['name']) ?></span>
                    </p>
                </div>
                
                <div class="info-card">
                    <p>
                        <i class="fas fa-envelope"></i>
                        <strong>Email:</strong>
                        <span><?= htmlspecialchars($user['email']) ?></span>
                    </p>
                </div>
                
                <div class="info-card">
                    <p>
                        <i class="fas fa-user-tag"></i>
                        <strong>Role:</strong>
                        <span>
                            <?= ucfirst(htmlspecialchars($user['role'])) ?>
                            <span class="role-badge">
                                <?= match($user['role']) {
                                    'admin' => 'Administrator',
                                    'premium' => 'Premium Member',
                                    'general' => 'General User',
                                    default => 'User'
                                } ?>
                            </span>
                        </span>
                    </p>
                </div>
            </div>
            
            <div class="movie-icons">
                <i class="fas fa-film movie-icon"></i>
                <i class="fas fa-ticket-alt movie-icon"></i>
                <i class="fas fa-video movie-icon"></i>
                <i class="fas fa-star movie-icon"></i>
                <i class="fas fa-theater-masks movie-icon"></i>
            </div>
            
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-value">
                        <?= $user['role'] === 'admin' ? '∞' : rand(50, 200) ?>
                    </div>
                    <div class="stat-label">Movies Watched</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">
                        <?= $user['role'] === 'admin' ? '∞' : rand(10, 50) ?>
                    </div>
                    <div class="stat-label">Watchlist</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">
                        <?= $user['role'] === 'admin' ? '∞' : rand(20, 100) ?>
                    </div>
                    <div class="stat-label">Reviews</div>
                </div>
            </div>
            
            <div class="button-group">
                <a href="<?= $dashboardLink ?>" class="btn btn-dashboard">
                    <i class="fas fa-arrow-left"></i>
                    Back to Dashboard
                </a>
                <a href="../../logout.php" class="btn btn-logout">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; <?= date('Y') ?> MovieDB - Your Ultimate Movie Database</p>
        </div>
    </div>

    <script>
        // Theme toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('themeToggle');
            const themeIcon = themeToggle.querySelector('i');
            const themeText = themeToggle.querySelector('span');
            const body = document.body;
            
            // Set initial state based on current theme
            if (body.classList.contains('dark-mode')) {
                themeIcon.className = 'fas fa-moon';
                themeText.textContent = 'Light Mode';
            } else {
                themeIcon.className = 'fas fa-sun';
                themeText.textContent = 'Dark Mode';
            }
            
            themeToggle.addEventListener('click', function() {
                // Toggle between themes
                if (body.classList.contains('dark-mode')) {
                    body.classList.remove('dark-mode');
                    body.classList.add('light-mode');
                    themeIcon.className = 'fas fa-sun';
                    themeText.textContent = 'Dark Mode';
                    setThemeCookie('light');
                } else {
                    body.classList.remove('light-mode');
                    body.classList.add('dark-mode');
                    themeIcon.className = 'fas fa-moon';
                    themeText.textContent = 'Light Mode';
                    setThemeCookie('dark');
                }
            });
            
            function setThemeCookie(theme) {
                // Set cookie to expire in 30 days
                const d = new Date();
                d.setTime(d.getTime() + (30 * 24 * 60 * 60 * 1000));
                const expires = "expires=" + d.toUTCString();
                document.cookie = "theme=" + theme + ";" + expires + ";path=/";
            }
            
            // Get user initials for avatar
            const userName = "<?= $user['name'] ?>";
            if (userName) {
                const names = userName.split(' ');
                let initials = '';
                if (names.length > 0) {
                    initials += names[0].charAt(0).toUpperCase();
                }
                if (names.length > 1) {
                    initials += names[names.length - 1].charAt(0).toUpperCase();
                }
                document.querySelector('.avatar-initials').textContent = initials;
            }
        });
    </script>
</body>
</html>
