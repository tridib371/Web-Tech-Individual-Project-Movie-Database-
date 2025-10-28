<?php
session_start();
require_once dirname(__DIR__, 2) . '/inc/db.php';

// Ensure only admins can access
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

// Get movie ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid movie ID.");
}

$movie_id = (int) $_GET['id'];

// Fetch movie
$stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->execute([$movie_id]);
$movie = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$movie) {
    die("Movie not found.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $release_year = $_POST['release_year'];
    $genre = trim($_POST['genre']);
    $poster = trim($_POST['poster']);

    $update = $conn->prepare("UPDATE movies SET title = ?, description = ?, release_year = ?, genre = ?, poster = ? WHERE id = ?");
    $update->execute([$title, $description, $release_year, $genre, $poster, $movie_id]);

    header("Location: movies.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Movie</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <style>
        /* Add this to your existing CSS */
        .theme-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        .toggle-btn {
            background: rgba(10, 10, 20, 0.8);
            border: 2px solid #00ffff;
            color: #00ffff;
            padding: 10px 20px;
            font-family: 'Orbitron', monospace;
            font-weight: bold;
            text-transform: uppercase;
            cursor: pointer;
            border-radius: 25px;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.3);
        }

        .toggle-btn:hover {
            background: rgba(0, 255, 255, 0.1);
            box-shadow: 0 0 30px rgba(0, 255, 255, 0.6);
            transform: translateY(-2px);
        }

        .toggle-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .toggle-btn:hover::before {
            left: 100%;
        }

        .toggle-btn.light::after {
            content: "☀️";
            margin-left: 8px;
        }

        .toggle-btn.dark::after {
            content: "🌙";
            margin-left: 8px;
        }

        /* Light mode styles for the toggle button */
        .light-mode .toggle-btn {
            background: rgba(255, 255, 255, 0.8);
            border-color: #333;
            color: #333;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }

        .light-mode .toggle-btn:hover {
            background: rgba(0, 0, 0, 0.1);
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.3);
        }

@import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;400;500;600;700&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Rajdhani', sans-serif;
    background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 50%, #16213e 100%);
    min-height: 100vh;
    overflow-x: hidden;
}

.dashboard-container {
    max-width: 800px;
    margin: 2rem auto;
    padding: 2rem;
    background: rgba(10, 10, 20, 0.8);
    border: 1px solid rgba(0, 255, 255, 0.3);
    border-radius: 12px;
    backdrop-filter: blur(10px);
    position: relative;
    overflow: hidden;
}

/* Animated Background Elements */
.dashboard-container::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(0, 255, 255, 0.1), transparent);
    animation: matrix 20s linear infinite;
    z-index: -1;
}

@keyframes matrix {
    0% { transform: rotate(0deg) translate(-10px, -10px); }
    100% { transform: rotate(360deg) translate(-10px, -10px); }
}

h2 {
    color: #00ffff;
    text-align: center;
    margin-bottom: 2rem;
    font-size: 2.5rem;
    font-family: 'Orbitron', monospace;
    text-shadow: 0 0 20px #00ffff, 0 0 40px #00ffff;
    animation: textGlow 2s ease-in-out infinite alternate;
    position: relative;
}

h2::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 3px;
    background: linear-gradient(90deg, transparent, #00ffff, transparent);
    animation: scanLine 3s linear infinite;
}

@keyframes textGlow {
    0% { text-shadow: 0 0 20px #00ffff, 0 0 40px #00ffff; }
    100% { text-shadow: 0 0 30px #00ffff, 0 0 60px #00ffff, 0 0 80px #00ffff; }
}

@keyframes scanLine {
    0% { opacity: 0; transform: translateX(-50%) scaleX(0); }
    50% { opacity: 1; transform: translateX(-50%) scaleX(1); }
    100% { opacity: 0; transform: translateX(-50%) scaleX(0); }
}

/* Cyber Field Styles */
.cyber-field {
    margin-bottom: 2rem;
    border: 1px solid rgba(0, 255, 255, 0.5);
    border-radius: 8px;
    background: rgba(0, 20, 30, 0.6);
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    transform-style: preserve-3d;
}

.cyber-field:hover {
    border-color: #00ffff;
    box-shadow: 0 0 30px rgba(0, 255, 255, 0.3);
    transform: translateY(-2px) perspective(1000px) rotateX(1deg);
}

.cyber-field::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(0, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
}

.cyber-field:hover::before {
    left: 100%;
}

.field-header {
    display: flex;
    align-items: center;
    padding: 1rem 1.5rem;
    background: linear-gradient(90deg, rgba(0, 255, 255, 0.1), transparent);
    border-bottom: 1px solid rgba(0, 255, 255, 0.3);
    position: relative;
}

.field-icon {
    width: 12px;
    height: 12px;
    background: #00ffff;
    margin-right: 12px;
    border-radius: 50%;
    box-shadow: 0 0 10px #00ffff, 0 0 20px #00ffff;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.7; transform: scale(1.1); }
}

.field-label {
    color: #00ffff;
    font-weight: 600;
    flex-grow: 1;
    text-transform: uppercase;
    letter-spacing: 2px;
    font-size: 0.9rem;
    font-family: 'Orbitron', monospace;
}

.field-required {
    color: #ff0066;
    font-size: 0.8rem;
    font-weight: bold;
    text-shadow: 0 0 10px #ff0066;
}

/* Cyber Input Styles */
.cyber-input {
    width: 100%;
    padding: 1.2rem 1.5rem;
    background: transparent;
    border: none;
    color: #ffffff;
    font-family: 'Rajdhani', sans-serif;
    font-size: 1.1rem;
    font-weight: 500;
    outline: none;
    resize: vertical;
    transition: all 0.3s ease;
    position: relative;
}

.cyber-input:focus {
    background: rgba(0, 255, 255, 0.05);
    text-shadow: 0 0 10px #ffffff;
}

.cyber-input::placeholder {
    color: rgba(255, 255, 255, 0.4);
    font-style: italic;
}

/* Floating Label Effect */
.cyber-field:focus-within .field-label {
    color: #00ff88;
    text-shadow: 0 0 10px #00ff88;
}

/* Cyber Actions */
.cyber-actions {
    display: flex;
    gap: 1.5rem;
    justify-content: center;
    margin-top: 3rem;
    position: relative;
}

.cyber-btn {
    padding: 1rem 2.5rem;
    border: 2px solid;
    background: transparent;
    color: inherit;
    font-family: 'Orbitron', monospace;
    font-weight: 700;
    text-transform: uppercase;
    text-decoration: none;
    text-align: center;
    cursor: pointer;
    transition: all 0.4s ease;
    position: relative;
    overflow: hidden;
    letter-spacing: 2px;
    backdrop-filter: blur(10px);
}

.cyber-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
}

.cyber-btn:hover::before {
    left: 100%;
}

.cyber-btn::after {
    content: '';
    position: absolute;
    inset: 0;
    background: inherit;
    border: inherit;
    filter: blur(10px);
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: -1;
}

.cyber-btn:hover::after {
    opacity: 0.7;
}

.btn-update {
    border-color: #00ff88;
    color: #00ff88;
    box-shadow: 0 0 20px rgba(0, 255, 136, 0.3);
}

.btn-update:hover {
    background: rgba(0, 255, 136, 0.1);
    box-shadow: 0 0 40px rgba(0, 255, 136, 0.6);
    transform: translateY(-3px) scale(1.05);
}

.btn-cancel {
    border-color: #ff0066;
    color: #ff0066;
    box-shadow: 0 0 20px rgba(255, 0, 102, 0.3);
}

.btn-cancel:hover {
    background: rgba(255, 0, 102, 0.1);
    box-shadow: 0 0 40px rgba(255, 0, 102, 0.6);
    transform: translateY(-3px) scale(1.05);
}

/* Particle Animation */
.particles {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: -1;
}

.particle {
    position: absolute;
    width: 2px;
    height: 2px;
    background: #00ffff;
    border-radius: 50%;
    animation: float 6s infinite linear;
}

@keyframes float {
    0% {
        transform: translateY(100vh) translateX(0) rotate(0deg);
        opacity: 0;
    }
    10% {
        opacity: 1;
    }
    90% {
        opacity: 1;
    }
    100% {
        transform: translateY(-100px) translateX(100px) rotate(360deg);
        opacity: 0;
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .dashboard-container {
        margin: 1rem;
        padding: 1.5rem;
    }
    
    .cyber-actions {
        flex-direction: column;
        gap: 1rem;
    }
    
    .cyber-btn {
        width: 100%;
    }
    
    h2 {
        font-size: 2rem;
    }

    .theme-toggle {
        top: 10px;
        right: 10px;
    }

    .toggle-btn {
        padding: 8px 16px;
        font-size: 0.9rem;
    }
}

/* Enhanced Light Mode */
.light-mode {
    background: linear-gradient(135deg, #f0f4ff 0%, #e6f0ff 50%, #d4e4ff 100%);
}

.light-mode .dashboard-container {
    background: rgba(255, 255, 255, 0.95);
    border-color: #4a90e2;
    box-shadow: 0 10px 40px rgba(74, 144, 226, 0.2);
}

.light-mode .dashboard-container::before {
    background: linear-gradient(45deg, transparent, rgba(74, 144, 226, 0.1), transparent);
}

.light-mode h2 {
    color: #2c5aa0;
    text-shadow: 0 0 20px rgba(44, 90, 160, 0.3), 0 0 40px rgba(44, 90, 160, 0.2);
}

.light-mode h2::after {
    background: linear-gradient(90deg, transparent, #4a90e2, transparent);
}

.light-mode .cyber-field {
    background: rgba(255, 255, 255, 0.9);
    border-color: #4a90e2;
    box-shadow: 0 5px 20px rgba(74, 144, 226, 0.1);
}

.light-mode .cyber-field:hover {
    border-color: #2c5aa0;
    box-shadow: 0 0 30px rgba(44, 90, 160, 0.2);
    transform: translateY(-2px) perspective(1000px) rotateX(1deg);
}

.light-mode .cyber-field::before {
    background: linear-gradient(90deg, transparent, rgba(44, 90, 160, 0.1), transparent);
}

.light-mode .field-header {
    background: linear-gradient(90deg, rgba(74, 144, 226, 0.1), transparent);
    border-bottom-color: rgba(74, 144, 226, 0.3);
}

.light-mode .field-icon {
    background: #4a90e2;
    box-shadow: 0 0 10px #4a90e2, 0 0 20px rgba(74, 144, 226, 0.5);
}

.light-mode .field-label {
    color: #2c5aa0;
    text-shadow: 0 0 10px rgba(44, 90, 160, 0.2);
}

.light-mode .field-required {
    color: #e74c3c;
    text-shadow: 0 0 10px rgba(231, 76, 60, 0.3);
}

.light-mode .cyber-input {
    color: #2c3e50;
}

.light-mode .cyber-input:focus {
    background: rgba(74, 144, 226, 0.05);
    text-shadow: 0 0 10px rgba(44, 90, 160, 0.1);
}

.light-mode .cyber-input::placeholder {
    color: rgba(44, 62, 80, 0.5);
}

.light-mode .cyber-field:focus-within .field-label {
    color: #27ae60;
    text-shadow: 0 0 10px rgba(39, 174, 96, 0.3);
}

.light-mode .btn-update {
    border-color: #27ae60;
    color: #27ae60;
    box-shadow: 0 0 20px rgba(39, 174, 96, 0.2);
}

.light-mode .btn-update:hover {
    background: rgba(39, 174, 96, 0.1);
    box-shadow: 0 0 40px rgba(39, 174, 96, 0.4);
}

.light-mode .btn-cancel {
    border-color: #e74c3c;
    color: #e74c3c;
    box-shadow: 0 0 20px rgba(231, 76, 60, 0.2);
}

.light-mode .btn-cancel:hover {
    background: rgba(231, 76, 60, 0.1);
    box-shadow: 0 0 40px rgba(231, 76, 60, 0.4);
}

.light-mode .particle {
    background: #4a90e2;
}

/* Light mode cursor */
.light-mode {
    cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><circle cx="12" cy="12" r="6" fill="%234a90e2" opacity="0.7"/></svg>'), auto;
}

/* Light mode text animations */
.light-mode h2 {
    animation: lightTextGlow 2s ease-in-out infinite alternate;
}

@keyframes lightTextGlow {
    0% { text-shadow: 0 0 20px rgba(44, 90, 160, 0.3), 0 0 40px rgba(44, 90, 160, 0.2); }
    100% { text-shadow: 0 0 30px rgba(44, 90, 160, 0.4), 0 0 60px rgba(44, 90, 160, 0.3); }
}

/* Light mode hologram effect */
.light-mode .dashboard-container {
    animation: lightHologram 10s infinite;
}

@keyframes lightHologram {
    0%, 100% { opacity: 0.95; filter: hue-rotate(0deg) brightness(1); }
    50% { opacity: 1; filter: hue-rotate(10deg) brightness(1.02); }
}

/* Enhanced toggle button for light mode */
.light-mode .toggle-btn {
    background: rgba(255, 255, 255, 0.95);
    border-color: #4a90e2;
    color: #2c5aa0;
    box-shadow: 0 0 20px rgba(74, 144, 226, 0.3);
}

.light-mode .toggle-btn:hover {
    background: rgba(74, 144, 226, 0.1);
    box-shadow: 0 0 30px rgba(74, 144, 226, 0.5);
}

/* Light mode input focus effects */
.light-mode .cyber-input:focus {
    background: rgba(74, 144, 226, 0.08);
    box-shadow: inset 0 0 20px rgba(74, 144, 226, 0.1);
}

/* Loading Animation */
@keyframes hologram {
    0%, 100% { opacity: 0.8; filter: hue-rotate(0deg); }
    50% { opacity: 1; filter: hue-rotate(90deg); }
}

.dashboard-container {
    animation: hologram 10s infinite;
}

/* Cursor Glow Effect */
body {
    cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><circle cx="12" cy="12" r="6" fill="%2300ffff" opacity="0.7"/></svg>'), auto;
}
    </style>
</head>
<body class="<?= isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light' ?>-mode">
    <!-- ADD THE TOGGLE BUTTON HERE -->
    <div class="theme-toggle">
        <button class="toggle-btn <?= isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark' ? 'dark' : 'light' ?>" id="themeToggle">
            <?= isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark' ? 'Light Mode' : 'Dark Mode' ?>
        </button>
    </div>

    <div class="dashboard-container">
        <h2>Edit Movie</h2>

        <form method="post">
            <div class="cyber-field" id="title-field">
                <div class="field-header">
                    <div class="field-icon"></div>
                    <span class="field-label">Movie Title</span>
                    <span class="field-required">*REQUIRED*</span>
                </div>
                <input type="text" name="title" class="cyber-input" value="<?= htmlspecialchars($movie['title']) ?>" required>
            </div>

            <div class="cyber-field" id="description-field">
                <div class="field-header">
                    <div class="field-icon"></div>
                    <span class="field-label">Synopsis</span>
                    <span class="field-required">*REQUIRED*</span>
                </div>
                <textarea name="description" class="cyber-input" rows="4" required><?= htmlspecialchars($movie['description']) ?></textarea>
            </div>

            <div class="cyber-field" id="year-field">
                <div class="field-header">
                    <div class="field-icon"></div>
                    <span class="field-label">Release Year</span>
                    <span class="field-required">*REQUIRED*</span>
                </div>
                <input type="number" name="release_year" class="cyber-input" value="<?= htmlspecialchars($movie['release_year']) ?>" min="1900" max="2099" required>
            </div>

            <div class="cyber-field" id="genre-field">
                <div class="field-header">
                    <div class="field-icon"></div>
                    <span class="field-label">Genre Tags</span>
                    <span class="field-required">*REQUIRED*</span>
                </div>
                <input type="text" name="genre" class="cyber-input" value="<?= htmlspecialchars($movie['genre']) ?>" required>
            </div>

            <div class="cyber-field" id="poster-field">
                <div class="field-header">
                    <div class="field-icon"></div>
                    <span class="field-label">Poster URL</span>
                    <span class="field-required">OPTIONAL</span>
                </div>
                <input type="text" name="poster" class="cyber-input" value="<?= htmlspecialchars($movie['poster']) ?>">
            </div>

            <div class="cyber-actions">
                <button type="submit" class="cyber-btn btn-update">UPDATE</button>
                <a href="movies.php" class="cyber-btn btn-cancel">CANCEL</a>
            </div>
        </form>
    </div>

    <script>
        // Theme Toggle Functionality
        document.getElementById('themeToggle').addEventListener('click', function() {
            const body = document.body;
            const currentTheme = body.classList.contains('dark-mode') ? 'dark' : 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            // Update body class
            body.classList.remove(currentTheme + '-mode');
            body.classList.add(newTheme + '-mode');
            
            // Update button text and class
            this.textContent = newTheme === 'dark' ? 'Light Mode' : 'Dark Mode';
            this.classList.remove(currentTheme);
            this.classList.add(newTheme);
            
            // Save theme preference in cookie
            document.cookie = `theme=${newTheme}; path=/; max-age=31536000`; // 1 year
            
            // Optional: Add transition effect
            body.style.transition = 'all 0.5s ease';
            setTimeout(() => {
                body.style.transition = '';
            }, 500);
        });

        // Add some cool animation when page loads
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('themeToggle');
            toggleBtn.style.transform = 'scale(0)';
            toggleBtn.style.transition = 'transform 0.5s ease';
            
            setTimeout(() => {
                toggleBtn.style.transform = 'scale(1)';
            }, 500);
        });

        // Add floating particles
        document.addEventListener('DOMContentLoaded', function() {
            const particlesContainer = document.createElement('div');
            particlesContainer.className = 'particles';
            document.body.appendChild(particlesContainer);

            for (let i = 0; i < 50; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + 'vw';
                particle.style.animationDelay = Math.random() * 6 + 's';
                particle.style.animationDuration = (3 + Math.random() * 4) + 's';
                particlesContainer.appendChild(particle);
            }

            // Add input focus effects
            const inputs = document.querySelectorAll('.cyber-input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'perspective(1000px) rotateX(2deg) translateY(-5px)';
                    this.parentElement.style.boxShadow = '0 10px 40px rgba(0, 255, 255, 0.5)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = '';
                    this.parentElement.style.boxShadow = '';
                });
            });
        });
    </script>
</body>
</html>