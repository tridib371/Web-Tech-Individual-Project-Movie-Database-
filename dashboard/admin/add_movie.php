<?php
session_start();
require_once dirname(__DIR__, 2) . '/inc/db.php';

// Check if admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

// Handle form submit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $release_year = $_POST['release_year'];
    $genre = trim($_POST['genre']);
    $poster = trim($_POST['poster']);  // updated variable name to 'poster'

    $stmt = $conn->prepare("INSERT INTO movies (title, description, release_year, genre, poster) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $release_year, $genre, $poster]);

    header("Location: movies.php");
    exit; 
    
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Movie</title>
    <link rel="stylesheet" href="../../css/styles.css">

   
   <style>
    /* FIXED: Buttons container - perfectly centered and side by side */
    .form-buttons {
        display: flex;
        gap: 25px;
        margin-top: 2rem;
        justify-content: center;
        align-items: center;
        flex-wrap: nowrap;
        width: 100%;
    }
.flex {
    display: flex;
    justify-content: center;
    gap: 20px; /* adds spacing between buttons */
}

   

    /* Submit button */
    button[type="submit"] {
        padding: 16px 35px;
        background: linear-gradient(45deg, var(--neon-green), var(--neon-blue));
        color: white;
        border: none;
        border-radius: 50px;
        font-weight: 900;
        font-size: 1em;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        text-transform: uppercase;
        letter-spacing: 2px;
        position: relative;
        overflow: hidden;
        box-shadow: 
            0 10px 30px rgba(0, 243, 255, 0.4),
            0 0 20px var(--neon-green);
        animation: submitPulse 3s infinite;
        width: 180px;
        height: 55px;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }

    button[type="submit"]::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, 
            transparent, 
            rgba(255, 255, 255, 0.4), 
            transparent);
        transition: left 0.6s ease;
    }

    button[type="submit"]:hover::before {
        left: 100%;
    }

    button[type="submit"]:hover {
        transform: translateY(-5px) scale(1.1);
        box-shadow: 
            0 20px 40px rgba(0, 243, 255, 0.6),
            0 0 40px var(--neon-green),
            0 0 60px var(--neon-blue);
    }

    /* Cancel button */
    a.logout-btn[style*="gray"] {
        padding: 16px 35px;
        background: linear-gradient(45deg, #666, #999) !important;
        color: white !important;
        border: none;
        border-radius: 50px;
        font-weight: 900;
        font-size: 1em;
        cursor: pointer;
        transition: all 0.4s ease;
        text-transform: uppercase;
        letter-spacing: 2px;
        text-decoration: none;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        width: 180px;
        height: 55px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
        line-height: 1;
    }

    a.logout-btn[style*="gray"]:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 
            0 15px 35px rgba(255, 255, 255, 0.2),
            0 0 30px rgba(255, 255, 255, 0.3);
        background: linear-gradient(45deg, #888, #aaa) !important;
    }

    /* Keep all other existing styles below exactly as they were */
    /* Cosmic form styling */
    .light-mode, .dark-mode {
        --bg-primary: #000010;
        --bg-secondary: #0a0a1f;
        --form-bg: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        --neon-pink: #ff2e63;
        --neon-blue: #00f3ff;
        --neon-purple: #9d4edd;
        --neon-green: #00ff88;
        --neon-yellow: #ffd700;
        --neon-orange: #ff6b35;
        --glow-pink: 0 0 20px rgba(255, 46, 99, 0.8);
        --glow-blue: 0 0 20px rgba(0, 243, 255, 0.8);
        --glow-purple: 0 0 20px rgba(157, 78, 221, 0.8);
    }

    body#add {
        background: var(--bg-primary);
        color: #ffffff;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        min-height: 100vh;
        margin: 0;
        padding: 0;
        overflow-x: hidden;
        position: relative;
    }

    /* Animated cosmic background */
    body#add::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: 
            radial-gradient(circle at 10% 20%, rgba(255, 46, 99, 0.15) 0%, transparent 40%),
            radial-gradient(circle at 90% 80%, rgba(0, 243, 255, 0.15) 0%, transparent 40%),
            radial-gradient(circle at 50% 50%, rgba(157, 78, 221, 0.1) 0%, transparent 50%);
        animation: cosmicFloat 15s infinite linear;
        z-index: -2;
    }

    /* Floating particles */
    body#add::after {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: 
            radial-gradient(circle at 25% 25%, var(--neon-pink) 1px, transparent 2px),
            radial-gradient(circle at 75% 75%, var(--neon-blue) 1px, transparent 2px),
            radial-gradient(circle at 50% 10%, var(--neon-green) 1px, transparent 2px),
            radial-gradient(circle at 10% 90%, var(--neon-yellow) 1px, transparent 2px),
            radial-gradient(circle at 90% 50%, var(--neon-purple) 1px, transparent 2px);
        background-size: 300px 300px, 400px 400px, 500px 500px, 600px 600px, 700px 700px;
        animation: particleMove 20s infinite linear;
        z-index: -1;
        opacity: 0.3;
    }

    .dashboard-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem;
        position: relative;
        z-index: 1;
    }

    /* Main form container */
    .dashboard-container > form {
        background: var(--form-bg);
        padding: 3rem;
        border-radius: 25px;
        box-shadow: 
            0 20px 40px rgba(0, 0, 0, 0.6),
            var(--glow-pink);
        border: 2px solid transparent;
        background-clip: padding-box;
        position: relative;
        overflow: hidden;
        animation: formEntrance 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    /* Animated border */
    .dashboard-container > form::before {
        content: '';
        position: absolute;
        top: -3px;
        left: -3px;
        right: -3px;
        bottom: -3px;
        background: linear-gradient(45deg, 
            var(--neon-pink), 
            var(--neon-purple), 
            var(--neon-blue), 
            var(--neon-green),
            var(--neon-yellow),
            var(--neon-pink));
        border-radius: 28px;
        z-index: -1;
        opacity: 0.8;
        background-size: 400% 400%;
        animation: rainbowBorder 4s linear infinite;
    }

    /* Form header */
    .dashboard-container h2 {
        font-size: 3em;
        text-align: center;
        margin-bottom: 2rem;
        background: linear-gradient(45deg, 
            var(--neon-yellow), 
            var(--neon-pink), 
            var(--neon-blue));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 900;
        text-shadow: 0 0 30px rgba(255, 46, 99, 0.5);
        animation: titleGlow 2s ease-in-out infinite alternate;
        position: relative;
    }

    .dashboard-container h2::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 200px;
        height: 4px;
        background: linear-gradient(90deg, var(--neon-pink), var(--neon-blue));
        border-radius: 2px;
        animation: widthPulse 3s infinite;
    }

    /* Form labels */
    form label {
        display: block;
        margin-bottom: 8px;
        font-weight: 700;
        font-size: 1.1em;
        color: var(--neon-green);
        text-shadow: var(--glow-blue);
        animation: labelFloat 3s ease-in-out infinite;
        transform-origin: left;
    }

    /* Form inputs */
    form input[type="text"],
    form input[type="number"],
    form textarea {
        width: 100%;
        padding: 15px 20px;
        border: 2px solid transparent;
        border-radius: 15px;
        background: rgba(0, 0, 0, 0.4);
        color: #ffffff;
        font-size: 1.1em;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
        box-shadow: 
            inset 0 2px 10px rgba(255, 255, 255, 0.1),
            0 5px 20px rgba(0, 0, 0, 0.3);
        position: relative;
    }

    form input[type="text"]:focus,
    form input[type="number"]:focus,
    form textarea:focus {
        outline: none;
        border: 2px solid var(--neon-blue);
        box-shadow: 
            inset 0 2px 15px rgba(255, 255, 255, 0.2),
            0 0 30px var(--neon-blue),
            var(--glow-blue);
        transform: scale(1.02);
        animation: inputPulse 2s infinite;
    }

    /* Textarea specific */
    form textarea {
        min-height: 120px;
        resize: vertical;
        font-family: inherit;
    }

    /* Input containers with icons */
    .input-container {
        position: relative;
        margin-bottom: 2rem;
    }

    .input-container::before {
        content: '';
        position: absolute;
        top: 50%;
        right: 20px;
        transform: translateY(-50%);
        width: 24px;
        height: 24px;
        opacity: 0.6;
        transition: all 0.3s ease;
    }

    .input-container:nth-child(1)::before { content: '🎬'; }
    .input-container:nth-child(2)::before { content: '📝'; }
    .input-container:nth-child(3)::before { content: '📅'; }
    .input-container:nth-child(4)::before { content: '🎭'; }
    .input-container:nth-child(5)::before { content: '🖼️'; }

    .input-container:focus-within::before {
        opacity: 1;
        transform: translateY(-50%) scale(1.2);
        animation: iconBounce 0.5s ease;
    }

    /* Theme toggle button */
    #toggle-theme-btn {
        padding: 12px 25px;
        background: linear-gradient(45deg, var(--neon-purple), var(--neon-pink));
        color: white;
        border: none;
        border-radius: 25px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 8px 25px rgba(157, 78, 221, 0.4);
        margin-bottom: 2rem;
    }

    #toggle-theme-btn:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 
            0 12px 30px rgba(157, 78, 221, 0.6),
            0 0 25px var(--neon-purple);
    }

    /* Animations */
    @keyframes cosmicFloat {
        0% { transform: translate(0, 0) scale(1); }
        25% { transform: translate(-10px, 10px) scale(1.02); }
        50% { transform: translate(0, 20px) scale(1); }
        75% { transform: translate(10px, 10px) scale(0.98); }
        100% { transform: translate(0, 0) scale(1); }
    }

    @keyframes particleMove {
        0% { transform: translate(0, 0) rotate(0deg); }
        100% { transform: translate(-100px, 100px) rotate(360deg); }
    }

    @keyframes rainbowBorder {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    @keyframes formEntrance {
        0% { 
            opacity: 0;
            transform: translateY(100px) scale(0.8) rotateX(-15deg);
        }
        100% { 
            opacity: 1;
            transform: translateY(0) scale(1) rotateX(0);
        }
    }

    @keyframes titleGlow {
        0% { text-shadow: 0 0 20px var(--neon-pink); }
        100% { text-shadow: 0 0 40px var(--neon-blue), 0 0 60px var(--neon-purple); }
    }

    @keyframes widthPulse {
        0%, 100% { width: 200px; }
        50% { width: 250px; }
    }

    @keyframes labelFloat {
        0%, 100% { transform: translateX(0); }
        50% { transform: translateX(10px); }
    }

    @keyframes inputPulse {
        0%, 100% { box-shadow: 0 0 30px var(--neon-blue); }
        50% { box-shadow: 0 0 40px var(--neon-blue), 0 0 50px var(--neon-green); }
    }

    @keyframes iconBounce {
        0%, 100% { transform: translateY(-50%) scale(1); }
        50% { transform: translateY(-50%) scale(1.3); }
    }

    @keyframes submitPulse {
        0%, 100% { 
            box-shadow: 0 10px 30px rgba(0, 243, 255, 0.4), 0 0 20px var(--neon-green);
        }
        50% { 
            box-shadow: 0 10px 30px rgba(0, 243, 255, 0.4), 0 0 30px var(--neon-green), 0 0 40px var(--neon-blue);
        }
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .dashboard-container {
            padding: 1rem;
        }
        
        .dashboard-container > form {
            padding: 2rem;
        }
        
        .form-buttons {
            flex-direction: row;
            gap: 15px;
        }
        
        button[type="submit"],
        a.logout-btn[style*="gray"] {
            width: 160px;
            height: 50px;
            font-size: 0.9em;
        }

    }
</style>

</head>
<body id="add" class="<?= isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light' ?>-mode">
    
<div class="dashboard-container">
        <button id="toggle-theme-btn" style="margin-bottom: 20px; padding: 8px 15px; cursor: pointer; border-radius: 5px; border: none; background-color: #007bff; color: white; font-weight: 600;">
    Toggle Light/Dark Mode
</button>
        <h2>Add New Movie</h2>

        <form method="post">
            <label>Title:</label><br>
            <input type="text" name="title" required><br><br>

            <label>Description:</label><br>
            <textarea name="description" rows="4" required></textarea><br><br>

            <label>Release Year:</label><br>
            <input type="number" name="release_year" min="1900" max="2099" required><br><br>

            <label>Genre:</label><br>
            <input type="text" name="genre" required><br><br>

            <label>Poster URL:</label><br>
            <input type="text" name="poster" placeholder="https://example.com/poster.jpg"><br><br>

            <div class='flex'>
                <button type="submit" class="logout-btn" style="background-color: #28a745;">Save Movie</button>
            <a href="movies.php" class="logout-btn" style="background-color: gray;">Cancel</a>
            </div>
        </form>
    </div>

     <script src="../../js/theme.js"></script>
</body>
</html>
