<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineAdmin | Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Light Theme */
            --light-bg: #f8f9fc;
            --light-card-bg: #ffffff;
            --light-text: #2d3748;
            --light-primary: #6a11cb;
            --light-secondary: #2575fc;
            --light-accent: #ff6b6b;
            --light-border: #e2e8f0;
            
            /* Dark Theme */
            --dark-bg: #0f172a;
            --dark-card-bg: rgba(30, 41, 59, 0.7);
            --dark-text: #f1f5f9;
            --dark-primary: #8b5cf6;
            --dark-secondary: #6366f1;
            --dark-accent: #ec4899;
            --dark-border: rgba(255, 255, 255, 0.1);
            
            /* Shared */
            --success: #10b981;
            --info: #0ea5e9;
            --warning: #f59e0b;
            --danger: #ef4444;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--light-secondary) 0%, var(--light-primary) 100%);
            color: var(--light-text);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            transition: var(--transition);
        }

        body.dark-mode {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: var(--dark-text);
        }

        .dashboard-container {
            width: 100%;
            max-width: 800px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: var(--shadow);
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: var(--transition);
        }

        body.dark-mode .dashboard-container {
            background: var(--dark-card-bg);
            border: 1px solid var(--dark-border);
        }

        .dashboard-container::before {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
            z-index: -1;
        }

        body.dark-mode .dashboard-container::before {
            background: radial-gradient(circle, rgba(139, 92, 246, 0.1) 0%, rgba(30, 41, 59, 0) 70%);
        }

        .admin-header {
            margin-bottom: 30px;
            position: relative;
            z-index: 2;
        }

        .admin-header h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 2.5rem;
            background: linear-gradient(90deg, var(--light-primary), var(--light-secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }

        body.dark-mode .admin-header h2 {
            background: linear-gradient(90deg, var(--dark-primary), var(--dark-secondary));
            -webkit-background-clip: text;
            background-clip: text;
        }

        .admin-header p {
            font-size: 1.1rem;
            color: #718096;
            max-width: 500px;
            margin: 0 auto;
        }

        body.dark-mode .admin-header p {
            color: #cbd5e1;
        }

        .admin-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
        }

        .action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 140px;
            height: 140px;
            background: var(--light-card-bg);
            border-radius: 15px;
            padding: 20px;
            text-decoration: none;
            color: var(--light-text);
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--light-border);
            position: relative;
            overflow: hidden;
        }

        body.dark-mode .action-btn {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--dark-border);
            color: var(--dark-text);
        }

        .action-btn::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: var(--light-primary);
            transition: var(--transition);
        }

        .action-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .action-btn:hover::before {
            height: 100%;
            opacity: 0.1;
        }

        .action-btn i {
            font-size: 2.5rem;
            margin-bottom: 15px;
            transition: var(--transition);
            z-index: 1;
        }

        .action-btn span {
            font-weight: 600;
            font-size: 1rem;
            z-index: 1;
        }

        .action-btn.profile { color: var(--success); }
        .action-btn.add-movie { color: var(--light-primary); }
        .action-btn.manage-movies { color: var(--info); }
        .action-btn.logout { color: var(--danger); }

        body.dark-mode .action-btn.add-movie { color: var(--dark-primary); }
        body.dark-mode .action-btn.manage-movies { color: var(--dark-secondary); }

        .action-btn:hover i {
            transform: scale(1.1);
        }

        .theme-toggle {
            position: absolute;
            top: 25px;
            right: 25px;
            width: 50px;
            height: 25px;
            background: var(--light-primary);
            border-radius: 50px;
            display: flex;
            align-items: center;
            padding: 0 5px;
            cursor: pointer;
            transition: var(--transition);
            z-index: 10;
        }

        body.dark-mode .theme-toggle {
            background: var(--dark-secondary);
        }

        .theme-toggle::before {
            content: "";
            position: absolute;
            width: 19px;
            height: 19px;
            border-radius: 50%;
            background: white;
            transition: var(--transition);
            transform: translateX(0);
        }

        body.dark-mode .theme-toggle::before {
            transform: translateX(25px);
        }

        .theme-toggle i {
            font-size: 0.9rem;
            color: white;
            position: absolute;
        }

        .theme-toggle .sun {
            left: 6px;
        }

        .theme-toggle .moon {
            right: 6px;
        }

        .cinematic-border {
            position: absolute;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, transparent, var(--light-primary), transparent);
            left: 0;
        }

        body.dark-mode .cinematic-border {
            background: linear-gradient(90deg, transparent, var(--dark-primary), transparent);
        }

        .cinematic-border.top {
            top: 0;
        }

        .cinematic-border.bottom {
            bottom: 0;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 30px 20px;
            }
            
            .admin-header h2 {
                font-size: 2rem;
            }
            
            .admin-actions {
                gap: 10px;
            }
            
            .action-btn {
                width: calc(50% - 10px);
                height: 120px;
            }
        }

        @media (max-width: 480px) {
            .action-btn {
                width: 100%;
            }
            
            .admin-header h2 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="cinematic-border top"></div>
        <div class="cinematic-border bottom"></div>
        
        <div class="theme-toggle">
            <i class="fas fa-sun sun"></i>
            <i class="fas fa-moon moon"></i>
        </div>
        
        <div class="admin-header">
            <h2>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h2>
            <p>Administrator Dashboard for Movie Database Management</p>
        </div>
        
        <div class="admin-actions">
            <a href="users/profile.php" class="action-btn profile">
                <i class="fas fa-user-circle"></i>
                <span>My Profile</span>
            </a>
            
            <a href="admin/add_movie.php" class="action-btn add-movie">
                <i class="fas fa-plus-circle"></i>
                <span>Add Movie</span>
            </a>
            
            <a href="admin/movies.php" class="action-btn manage-movies">
                <i class="fas fa-film"></i>
                <span>Manage Movies</span>
            </a>
            
            <a href="../logout.php" class="action-btn logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <script>
        // Theme Toggle Functionality
        const themeToggle = document.querySelector('.theme-toggle');
        const body = document.body;
        
        // Check for saved theme preference
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            body.classList.add(savedTheme);
        }
        
        themeToggle.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            const currentTheme = body.classList.contains('dark-mode') ? 'dark-mode' : '';
            localStorage.setItem('theme', currentTheme);
        });
        
        // Add cinematic hover effect to action buttons
        const actionBtns = document.querySelectorAll('.action-btn');
        actionBtns.forEach(btn => {
            btn.addEventListener('mouseenter', () => {
                btn.style.transform = 'translateY(-8px)';
            });
            
            btn.addEventListener('mouseleave', () => {
                btn.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>
