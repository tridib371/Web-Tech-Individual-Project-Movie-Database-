<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'general') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>General Dashboard</title>
    <style>
        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            color: #f0f0f0;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Film Grain Effect */
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
            opacity: 0.1;
            pointer-events: none;
            z-index: -1;
        }
        
        /* Dashboard Container */
        #general {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 2rem;
        }
        
        .dashboard-container {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 3rem;
            max-width: 800px;
            width: 100%;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
            text-align: center;
        }
        
        /* Cinematic Header Effect */
        .dashboard-container::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #ff6b6b, #ffa502, #e74c3c);
            box-shadow: 0 0 15px rgba(231, 76, 60, 0.7);
        }
        
        /* Theme Toggle Button */
        #toggle-theme-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: linear-gradient(135deg, #ff6b6b, #e74c3c);
            color: white;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.4);
            transition: all 0.3s ease;
            z-index: 10;
        }
        
        #toggle-theme-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.6);
        }
        
        /* Welcome Section */
        h2 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            color: #ffd700;
            text-shadow: 0 0 10px rgba(255, 215, 0, 0.3);
            position: relative;
            display: inline-block;
        }
        
        h2::after {
            content: "";
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #ff6b6b, #ffa502);
            border-radius: 2px;
        }
        
        p {
            font-size: 1.2rem;
            margin-bottom: 2.5rem;
            color: #cbd5e1;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }
        
        /* Action Buttons */
        .btn-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 2rem;
        }
        
        .action-btn {
            padding: 15px 30px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 220px;
            position: relative;
            overflow: hidden;
            z-index: 1;
            border: none;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        .action-btn::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            z-index: -1;
            transition: transform 0.5s ease;
            transform: scaleX(0);
            transform-origin: right;
        }
        
        .action-btn:hover::before {
            transform: scaleX(1);
            transform-origin: left;
        }
        
        .action-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
        }
        
        .action-btn i {
            margin-right: 10px;
            font-size: 1.4rem;
        }
        
        /* Profile Button */
        .profile-btn {
            background: linear-gradient(135deg, #4e54c8, #8f94fb);
            color: white;
        }
        
        /* Movies Button */
        .movies-btn {
            background: linear-gradient(135deg, #ff6b6b, #ff8e53);
            color: white;
        }
        
        /* Logout Button */
        .logout-btn {
            background: linear-gradient(135deg, #2c3e50, #4a6491);
            color: white;
        }
        
        /* Film Reel Decorations */
        .film-reel {
            position: absolute;
            width: 80px;
            height: 80px;
            opacity: 0.2;
            z-index: -1;
        }
        
        .reel-1 {
            top: 20px;
            left: 20px;
            animation: spin 20s linear infinite;
        }
        
        .reel-2 {
            bottom: 20px;
            right: 20px;
            animation: spin 25s linear infinite reverse;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 2rem 1.5rem;
            }
            
            h2 {
                font-size: 2rem;
            }
            
            .btn-container {
                flex-direction: column;
                align-items: center;
            }
            
            .action-btn {
                width: 100%;
                max-width: 300px;
            }
            
            .film-reel {
                width: 50px;
                height: 50px;
            }
        }
        
        /* Light Theme Styles */
        body.light-theme {
            background: linear-gradient(135deg, #e0e7ff 0%, #d1e0ff 50%, #c2d5ff 100%);
            color: #1e293b;
        }
        
        body.light-theme .dashboard-container {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        body.light-theme h2 {
            color: #4f46e5;
            text-shadow: none;
        }
        
        body.light-theme p {
            color: #4b5563;
        }
        
        body.light-theme .action-btn {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body id="general">
    <!-- Film Reel Decorations -->
    <div class="film-reel reel-1">
        <svg viewBox="0 0 100 100">
            <circle cx="50" cy="50" r="45" fill="#333" />
            <circle cx="50" cy="50" r="15" fill="#1a1a2e" />
            <rect x="47" y="5" width="6" height="90" rx="3" fill="#ffd700" />
            <rect x="5" y="47" width="90" height="6" rx="3" fill="#ffd700" />
            <rect x="20" y="20" width="6" height="6" rx="3" fill="#ffd700" transform="rotate(45 50 50)" />
            <rect x="74" y="20" width="6" height="6" rx="3" fill="#ffd700" transform="rotate(45 50 50)" />
            <rect x="20" y="74" width="6" height="6" rx="3" fill="#ffd700" transform="rotate(45 50 50)" />
            <rect x="74" y="74" width="6" height="6" rx="3" fill="#ffd700" transform="rotate(45 50 50)" />
        </svg>
    </div>
    
    <div class="film-reel reel-2">
        <svg viewBox="0 0 100 100">
            <circle cx="50" cy="50" r="45" fill="#333" />
            <circle cx="50" cy="50" r="15" fill="#1a1a2e" />
            <rect x="47" y="5" width="6" height="90" rx="3" fill="#ffd700" />
            <rect x="5" y="47" width="90" height="6" rx="3" fill="#ffd700" />
            <rect x="20" y="20" width="6" height="6" rx="3" fill="#ffd700" transform="rotate(45 50 50)" />
            <rect x="74" y="20" width="6" height="6" rx="3" fill="#ffd700" transform="rotate(45 50 50)" />
            <rect x="20" y="74" width="6" height="6" rx="3" fill="#ffd700" transform="rotate(45 50 50)" />
            <rect x="74" y="74" width="6" height="6" rx="3" fill="#ffd700" transform="rotate(45 50 50)" />
        </svg>
    </div>
    
    <div class="dashboard-container">
        <button id="toggle-theme-btn">Toggle Light/Dark Mode</button>
        
        <h2>Welcome, Movie Lover.👻</h2>
        <p>Explore our vast collection of movies, update your profile, and discover new favorites. Your cinematic journey starts here!</p>
        
        <div class="btn-container">
            <a href="users/profile.php" class="action-btn profile-btn">👤 My Profile</a>
            <a href="user/movies.php" class="action-btn movies-btn">🎬 Browse Movies</a>
            <a href="../logout.php" class="action-btn logout-btn">Logout</a>
        </div>
    </div>

    <script>
        // Theme Toggle Functionality
        document.getElementById('toggle-theme-btn').addEventListener('click', function() {
            document.body.classList.toggle('light-theme');
            
            // Save theme preference to localStorage
            const isLightTheme = document.body.classList.contains('light-theme');
            localStorage.setItem('theme', isLightTheme ? 'light' : 'dark');
        });
        
        // Check for saved theme preference
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'light') {
                document.body.classList.add('light-theme');
            }
        });
    </script>
</body>
</html>
