<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'premium') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PREMIUM CINEMA LOUNGE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Base Variables */
        :root {
            --gold: #FFD700;
            --platinum: #E5E4E2;
            --velvet: #1A0A26;
            --spotlight: rgba(255,215,0,0.15);
            --bg-gradient: radial-gradient(circle at center, #000000 0%, #1A0A26 100%);
            --panel-bg: linear-gradient(135deg, rgba(26,10,38,0.7), rgba(0,0,0,0.8));
            --lounge-bg: linear-gradient(135deg, rgba(26,10,38,0.9) 0%, rgba(0,0,0,0.95) 100%);
            --card-bg: linear-gradient(135deg, rgba(26,10,38,0.5), rgba(0,0,0,0.6));
            --btn-gradient: linear-gradient(135deg, rgba(255,215,0,0.1), rgba(255,215,0,0.05));
            --text-primary: #E5E4E2;
            --text-secondary: rgba(229, 228, 226, 0.7);
            --bg-image: url('https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
        }

        /* Light Theme Variables */
        :root[data-theme="light"] {
            --gold: #C5A042; /* More sophisticated gold */
            --platinum: #2D2D2D; /* Dark gray for better contrast */
            --velvet: #FFFFFF;
            --spotlight: rgba(197, 160, 66, 0.15);
            --bg-gradient: radial-gradient(circle at center, #F9F9F9 0%, #E8E8E8 100%);
            --panel-bg: linear-gradient(135deg, rgba(255,255,255,0.9), rgba(240,240,240,0.95));
            --lounge-bg: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(245,245,245,0.98) 100%);
            --card-bg: linear-gradient(135deg, rgba(255,255,255,0.8), rgba(240,240,240,0.9));
            --btn-gradient: linear-gradient(135deg, rgba(197,160,66,0.1), rgba(197,160,66,0.05));
            --text-primary: #2D2D2D;
            --text-secondary: rgba(45, 45, 45, 0.8);
            --bg-image: url('https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');
        }

        /* Base Styles */
        body {
            margin: 0;
            padding: 0;
            background: var(--bg-gradient);
            color: var(--text-primary);
            font-family: 'Montserrat', 'Helvetica Neue', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
            transition: all 0.5s ease;
        }

        .premium-lounge {
            position: relative;
            width: 100%;
            min-height: 100vh;
            background: var(--lounge-bg),
                var(--bg-image) center/cover no-repeat;
            backdrop-filter: blur(2px);
            padding: 4rem 2rem;
            box-sizing: border-box;
            transition: all 0.5s ease;
        }

        .premium-lounge::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect width="100" height="100" fill="none" stroke="%23FFD700" stroke-width="0.5" stroke-dasharray="2 2" opacity="0.3"/></svg>');
            pointer-events: none;
            opacity: 0.15;
        }

        .lounge-container {
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .premium-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3rem;
            border-bottom: 1px solid rgba(255, 215, 0, 0.3);
            padding-bottom: 1.5rem;
        }

        .user-greeting {
            position: relative;
        }

        .user-greeting h1 {
            font-size: 2.8rem;
            margin: 0;
            font-weight: 300;
            letter-spacing: 1px;
            background: linear-gradient(90deg, var(--platinum), var(--gold));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            position: relative;
            display: inline-block;
        }

        .user-greeting h1::after {
            content: 'PREMIUM MEMBER';
            position: absolute;
            bottom: -1.5rem;
            left: 0;
            font-size: 0.8rem;
            letter-spacing: 3px;
            color: var(--gold);
            font-weight: 600;
        }

        .status-badge {
            background: linear-gradient(135deg, rgba(255,215,0,0.2), rgba(255,215,0,0.05));
            border: 1px solid var(--gold);
            border-radius: 30px;
            padding: 0.8rem 1.5rem;
            font-size: 0.9rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
        }

        .status-badge i {
            color: var(--gold);
            margin-right: 0.5rem;
            font-size: 1.2rem;
        }

        .premium-content {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 3rem;
            margin-top: 2rem;
        }

        .control-panel {
            background: var(--panel-bg);
            border: 1px solid rgba(255, 215, 0, 0.1);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            height: fit-content;
        }

        .panel-title {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            color: var(--gold);
            letter-spacing: 1px;
            display: flex;
            align-items: center;
        }

        .panel-title i {
            margin-right: 0.8rem;
        }

        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .premium-btn {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            background: var(--btn-gradient);
            border: 1px solid rgba(255, 215, 0, 0.2);
            color: var(--text-primary);
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .premium-btn:hover {
            background: linear-gradient(135deg, rgba(255,215,0,0.2), rgba(255,215,0,0.1));
            border-color: var(--gold);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px var(--spotlight);
        }

        .premium-btn i {
            color: var(--gold);
            font-size: 1.2rem;
        }

        .premium-btn .btn-text {
            flex-grow: 1;
            margin-left: 1rem;
            text-align: left;
        }

        .premium-btn .badge {
            background: var(--gold);
            color: var(--velvet);
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: bold;
        }

        .feature-showcase {
            background: var(--panel-bg);
            border: 1px solid rgba(255, 215, 0, 0.1);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .feature-card {
            background: var(--card-bg);
            border: 1px solid rgba(255, 215, 0, 0.1);
            border-radius: 10px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255,215,0,0.03), transparent);
            z-index: 0;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            border-color: var(--gold);
            box-shadow: 0 10px 20px var(--spotlight);
        }

        .feature-icon {
            font-size: 2rem;
            color: var(--gold);
            margin-bottom: 1rem;
        }

        .feature-title {
            font-size: 1.1rem;
            margin-bottom: 0.8rem;
            color: var(--text-primary);
            position: relative;
        }

        .feature-desc {
            font-size: 0.9rem;
            line-height: 1.6;
            color: var(--text-secondary);
        }

        .exclusive-banner {
            grid-column: 1 / -1;
            background: linear-gradient(90deg, rgba(26,10,38,0.7), rgba(255,215,0,0.1));
            border: 1px solid var(--gold);
            border-radius: 10px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            margin-top: 1.5rem;
        }

        .exclusive-banner i {
            font-size: 2.5rem;
            color: var(--gold);
            margin-right: 1.5rem;
        }

        .banner-content h3 {
            margin: 0 0 0.5rem 0;
            color: var(--gold);
            font-size: 1.3rem;
        }

        .banner-content p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        /* Glow effects */
        .glow {
            animation: glow 3s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from {
                box-shadow: 0 0 5px rgba(255, 215, 0, 0.5);
            }
            to {
                box-shadow: 0 0 20px rgba(255, 215, 0, 0.8);
            }
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .premium-content {
                grid-template-columns: 1fr;
            }
            
            .premium-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .status-badge {
                margin-top: 1rem;
            }
        }

        @media (max-width: 768px) {
            .user-greeting h1 {
                font-size: 2rem;
            }
            
            .feature-grid {
                grid-template-columns: 1fr;
            }
            
            .premium-lounge {
                padding: 2rem 1rem;
            }
            
            .control-panel, .feature-showcase {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="premium-lounge">
        <div class="lounge-container">
            <div class="premium-header">
                <div class="user-greeting">
                    <h1>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></h1>
                </div>
                <div class="status-badge glow">
                    <i class="fas fa-crown"></i>
                    <span>VIP ACCESS ACTIVATED</span>
                </div>
            </div>

            <div class="premium-content">
                <div class="control-panel">
                    <div class="panel-title">
                        <i class="fas fa-film"></i>
                        <span>CINEMA CONTROLS</span>
                    </div>
                    <div class="btn-group">
                        <a href="user/movies.php" class="premium-btn">
                            <i class="fas fa-ticket-alt"></i>
                            <span class="btn-text">Movie Library</span>
                            <span class="badge">4K HDR</span>
                        </a>
                        <a href="users/profile.php" class="premium-btn">
                            <i class="fas fa-user-astronaut"></i>
                            <span class="btn-text">My Profile</span>
                            <span class="badge">VIP</span>
                        </a>
                        <button id="toggle-theme-btn" class="premium-btn">
                            <i class="fas fa-moon"></i>
                            <span class="btn-text">Theater Mode</span>
                            <span class="badge">DOLBY</span>
                        </button>
                        <a href="../logout.php" class="premium-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            <span class="btn-text">Exit Lounge</span>
                        </a>
                    </div>
                </div>

                <div class="feature-showcase">
                    <div class="panel-title">
                        <i class="fas fa-star"></i>
                        <span>YOUR PREMIUM BENEFITS</span>
                    </div>
                    <div class="feature-grid">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-medal"></i>
                            </div>
                            <h3 class="feature-title">Exclusive Premieres</h3>
                            <p class="feature-desc">Early access to new releases before standard members, including director's cuts.</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-photo-film"></i>
                            </div>
                            <h3 class="feature-title">4K Ultra HD</h3>
                            <p class="feature-desc">Stream in pristine 4K resolution with HDR10+ and Dolby Vision support.</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-headphones"></i>
                            </div>
                            <h3 class="feature-title">Dolby Atmos</h3>
                            <p class="feature-desc">Immersive 3D audio that flows all around you with breathtaking realism.</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-download"></i>
                            </div>
                            <h3 class="feature-title">Offline Cinema</h3>
                            <p class="feature-desc">Download unlimited content to your devices for offline viewing anywhere.</p>
                        </div>
                        <div class="exclusive-banner">
                            <i class="fas fa-key"></i>
                            <div class="banner-content">
                                <h3>EXCLUSIVE CONTENT UNLOCKED</h3>
                                <p>As a premium member, you have access to our private collection of behind-the-scenes footage, director commentaries, and special editions.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Theater Mode Toggle Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggle-theme-btn');
            const themeIcon = toggleBtn.querySelector('i');
            const btnText = toggleBtn.querySelector('.btn-text');
            
            // Check for saved theme preference or use preferred color scheme
            const savedTheme = localStorage.getItem('theme') || 
                               (window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
            
            // Apply the saved theme
            applyTheme(savedTheme);
            
            // Toggle between light and dark theme
            toggleBtn.addEventListener('click', function() {
                const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                
                applyTheme(newTheme);
                localStorage.setItem('theme', newTheme);
            });
            
            function applyTheme(theme) {
                document.documentElement.setAttribute('data-theme', theme);
                
                if (theme === 'light') {
                    themeIcon.classList.remove('fa-moon');
                    themeIcon.classList.add('fa-sun');
                    btnText.textContent = 'Theater Mode';
                } else {
                    themeIcon.classList.remove('fa-sun');
                    themeIcon.classList.add('fa-moon');
                    btnText.textContent = 'Light Mode';
                }
            }
            
            // Add cinematic transition effect
            toggleBtn.addEventListener('click', function() {
                document.body.style.opacity = '0.7';
                setTimeout(() => {
                    document.body.style.opacity = '1';
                }, 300);
            });
        });
    </script>
</body>
</html>