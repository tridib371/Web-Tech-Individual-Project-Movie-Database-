<?php
session_start();
require_once dirname(__DIR__, 2) . '/inc/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

$search = $_GET['search'] ?? '';
if ($search) {
    $stmt = $conn->prepare("SELECT * FROM movies WHERE title LIKE ? ORDER BY created_at DESC");
    $stmt->execute(["%$search%"]);
} else {
    $stmt = $conn->query("SELECT * FROM movies ORDER BY created_at DESC");
}
$movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Movies</title>
    <link rel="stylesheet" href="../../css/styles.css">
    
      <style>
    /* Cosmic color palette */
    .light-mode, .dark-mode {
        --bg-primary: #000010;
        --bg-secondary: #0a0a1f;
        --bg-card: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        --text-primary: #ffffff;
        --text-secondary: #e0e0ff;
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

    body {
        background: var(--bg-primary);
        color: var(--text-primary);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
        overflow-x: hidden;
    }

    /* Animated cosmic background */
    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: 
            radial-gradient(circle at 20% 80%, rgba(255, 46, 99, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(0, 243, 255, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 40% 40%, rgba(157, 78, 221, 0.1) 0%, transparent 50%);
        animation: cosmicFloat 20s infinite linear;
        z-index: -1;
    }

    .movie-card {
        border: none;
        border-radius: 25px;
        padding: 35px;
        background: var(--bg-card);
        display: flex;
        gap: 35px;
        align-items: flex-start;
        position: relative;
        overflow: hidden;
        margin-bottom: 40px;
        box-shadow: 
            0 20px 40px rgba(0, 0, 0, 0.6),
            var(--glow-pink);
        border: 2px solid transparent;
        background-clip: padding-box;
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        animation: cardEntrance 0.8s ease-out;
    }

    /* Multi-color animated border */
    .movie-card::before {
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
        opacity: 0;
        background-size: 400% 400%;
        animation: rainbowBorder 3s linear infinite;
        transition: opacity 0.4s ease;
    }

    .movie-card:hover::before {
        opacity: 1;
    }

    .movie-card:hover {
        transform: translateY(-20px) scale(1.03) rotate(1deg);
        box-shadow: 
            0 30px 60px rgba(0, 0, 0, 0.8),
            0 0 50px var(--neon-pink),
            0 0 80px var(--neon-purple);
    }

    /* Animated poster container */
    .poster-container {
        position: relative;
        perspective: 1000px;
    }

    .movie-poster {
        width: 180px;
        height: 270px;
        border-radius: 20px;
        object-fit: cover;
        box-shadow: 
            0 20px 40px rgba(0, 0, 0, 0.8),
            0 0 30px var(--neon-blue);
        border: 3px solid transparent;
        background: linear-gradient(45deg, var(--neon-pink), var(--neon-blue)) border-box;
        transition: all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        z-index: 2;
        transform-style: preserve-3d;
    }

    .movie-card:hover .movie-poster {
        transform: scale(1.15) rotateY(10deg) rotateX(5deg);
        box-shadow: 
            0 25px 50px rgba(0, 0, 0, 0.9),
            0 0 60px var(--neon-pink),
            0 0 90px var(--neon-purple);
    }

    /* Floating particles around poster */
    .poster-container::after {
        content: '';
        position: absolute;
        top: -10px;
        left: -10px;
        right: -10px;
        bottom: -10px;
        background: 
            radial-gradient(circle at 20% 30%, var(--neon-pink) 2px, transparent 3px),
            radial-gradient(circle at 80% 70%, var(--neon-blue) 2px, transparent 3px),
            radial-gradient(circle at 40% 90%, var(--neon-green) 2px, transparent 3px);
        background-size: 50px 50px;
        border-radius: 25px;
        opacity: 0;
        animation: particleFloat 4s infinite linear;
        transition: opacity 0.4s ease;
    }

    .movie-card:hover .poster-container::after {
        opacity: 0.6;
    }

    .movie-info {
        flex: 1;
        position: relative;
        z-index: 2;
    }

    .movie-info h3 {
        font-size: 2.5em;
        margin: 0 0 25px 0;
        background: linear-gradient(45deg, 
            var(--neon-yellow), 
            var(--neon-pink), 
            var(--neon-blue));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 900;
        text-shadow: 0 0 30px rgba(255, 46, 99, 0.5);
        position: relative;
        display: inline-block;
        animation: titleGlow 2s ease-in-out infinite alternate;
    }

    .info-grid {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 15px 25px;
        margin: 25px 0;
        align-items: start;
    }

    .info-label {
        color: var(--neon-green);
        font-weight: 800;
        font-size: 1.2em;
        text-shadow: var(--glow-blue);
        animation: labelPulse 3s infinite;
    }

    .info-value {
        color: var(--text-primary);
        font-weight: 600;
        font-size: 1.1em;
    }

    .rating-container {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .rating-stars {
        color: var(--neon-yellow);
        font-weight: bold;
        font-size: 1.3em;
        text-shadow: 0 0 15px rgba(255, 215, 0, 0.8);
        animation: starTwinkle 2s infinite;
    }

    .stars-visual {
        display: flex;
        gap: 5px;
    }

    .star {
        color: var(--neon-yellow);
        text-shadow: 0 0 10px currentColor;
        animation: starPulse 1.5s infinite;
    }

    .star:nth-child(2) { animation-delay: 0.2s; }
    .star:nth-child(3) { animation-delay: 0.4s; }
    .star:nth-child(4) { animation-delay: 0.6s; }
    .star:nth-child(5) { animation-delay: 0.8s; }

    .description-box {
        background: rgba(0, 0, 0, 0.5);
        padding: 25px;
        border-radius: 15px;
        border: 2px solid transparent;
        background: linear-gradient(45deg, rgba(26, 26, 46, 0.8), rgba(22, 33, 62, 0.8)) padding-box,
                   linear-gradient(45deg, var(--neon-purple), var(--neon-blue)) border-box;
        margin: 25px 0;
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
    }

    .description-box::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, 
            transparent, 
            rgba(255, 255, 255, 0.1), 
            transparent);
        transition: left 0.8s ease;
    }

    .movie-card:hover .description-box::before {
        left: 100%;
    }

    .action-buttons {
        margin-top: 30px;
        display: flex;
        gap: 20px;
    }

    .action-buttons a {
        padding: 16px 32px;
        border-radius: 50px;
        color: white;
        text-decoration: none;
        font-weight: 800;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        text-transform: uppercase;
        letter-spacing: 2px;
        font-size: 0.9em;
        position: relative;
        overflow: hidden;
        border: 2px solid transparent;
        background: linear-gradient(45deg, var(--neon-pink), var(--neon-purple));
        box-shadow: 0 10px 30px rgba(255, 46, 99, 0.4);
    }

    .edit-btn {
        background: linear-gradient(45deg, var(--neon-green), var(--neon-blue)) !important;
        box-shadow: 0 10px 30px rgba(0, 243, 255, 0.4) !important;
    }

    .delete-btn {
        background: linear-gradient(45deg, var(--neon-orange), var(--neon-pink)) !important;
        box-shadow: 0 10px 30px rgba(255, 107, 53, 0.4) !important;
    }

    .action-buttons a::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, 
            transparent, 
            rgba(255, 255, 255, 0.3), 
            transparent);
        transition: left 0.6s ease;
    }

    .action-buttons a:hover::before {
        left: 100%;
    }

    .action-buttons a:hover {
        transform: translateY(-5px) scale(1.1);
        box-shadow: 
            0 20px 40px rgba(255, 46, 99, 0.6),
            0 0 30px currentColor;
        animation: buttonPop 0.4s ease;
    }

    /* Search form - Ultra modern */
    form[method="GET"] {
        position: relative;
        margin: 60px 0;
        display: flex;
        gap: 25px;
        align-items: center;
        justify-content: center;
    }

    .search-container {
        position: relative;
        display: inline-block;
    }

    form[method="GET"] input {
        padding: 20px 35px;
        width: 450px;
        border-radius: 50px;
        border: 3px solid transparent;
        background: linear-gradient(45deg, #1a1a2e, #16213e) padding-box,
                   linear-gradient(45deg, var(--neon-pink), var(--neon-blue), var(--neon-green)) border-box;
        color: var(--text-primary);
        font-size: 1.2em;
        box-shadow: 
            0 15px 35px rgba(255, 46, 99, 0.3),
            inset 0 2px 10px rgba(255, 255, 255, 0.1);
        transition: all 0.4s ease;
        backdrop-filter: blur(10px);
    }

    form[method="GET"] input:focus {
        outline: none;
        box-shadow: 
            0 20px 45px rgba(255, 46, 99, 0.5),
            0 0 40px var(--neon-blue),
            inset 0 2px 15px rgba(255, 255, 255, 0.2);
        transform: scale(1.05);
    }

    form[method="GET"] button {
        padding: 20px 40px;
        background: linear-gradient(45deg, var(--neon-pink), var(--neon-purple), var(--neon-blue));
        color: white;
        border: none;
        border-radius: 50px;
        font-weight: 900;
        cursor: pointer;
        transition: all 0.4s ease;
        box-shadow: 
            0 15px 35px rgba(255, 46, 99, 0.4),
            0 0 25px var(--neon-purple);
        text-transform: uppercase;
        letter-spacing: 2px;
        font-size: 1.1em;
        position: relative;
        overflow: hidden;
        animation: searchPulse 2s infinite;
    }

    form[method="GET"] button:hover {
        transform: translateY(-5px) scale(1.1);
        box-shadow: 
            0 25px 50px rgba(255, 46, 99, 0.6),
            0 0 50px var(--neon-pink),
            0 0 70px var(--neon-blue);
    }

    /* Animations */
    @keyframes rainbowBorder {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    @keyframes cosmicFloat {
        0% { transform: translate(0, 0) rotate(0deg); }
        25% { transform: translate(-10px, 10px) rotate(1deg); }
        50% { transform: translate(0, 20px) rotate(0deg); }
        75% { transform: translate(10px, 10px) rotate(-1deg); }
        100% { transform: translate(0, 0) rotate(0deg); }
    }

    @keyframes cardEntrance {
        0% { 
            opacity: 0;
            transform: translateY(100px) scale(0.8) rotateX(-45deg);
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

    @keyframes particleFloat {
        0% { transform: translateY(0) rotate(0deg); }
        100% { transform: translateY(-20px) rotate(360deg); }
    }

    @keyframes starPulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.2); }
    }

    @keyframes buttonPop {
        0% { transform: translateY(-5px) scale(1.1); }
        50% { transform: translateY(-7px) scale(1.15); }
        100% { transform: translateY(-5px) scale(1.1); }
    }

    @keyframes searchPulse {
        0%, 100% { box-shadow: 0 15px 35px rgba(255, 46, 99, 0.4), 0 0 25px var(--neon-purple); }
        50% { box-shadow: 0 15px 35px rgba(255, 46, 99, 0.4), 0 0 35px var(--neon-pink), 0 0 45px var(--neon-blue); }
    }

    /* Staggered card animations */
    .movie-card:nth-child(1) { animation-delay: 0.1s; }
    .movie-card:nth-child(2) { animation-delay: 0.2s; }
    .movie-card:nth-child(3) { animation-delay: 0.3s; }
    .movie-card:nth-child(4) { animation-delay: 0.4s; }
    .movie-card:nth-child(5) { animation-delay: 0.5s; }

    /* Text clarity */
    .movie-info * {
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.9);
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
</style>

</head>
<body class="<?= isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light' ?>-mode">
    <div class="dashboard-container">
        <h2>Manage Movies</h2>
        <a href="add_movie.php" class="logout-btn" style="background-color:#28a745;">➕ Add New Movie</a>
        <a href="../../logout.php" class="logout-btn" style="background-color:#dc3545;">Logout</a>

        <button id="toggle-theme-btn" style="margin-top: 20px; padding: 8px 15px; cursor: pointer; border-radius: 5px; border: none; background-color: #007bff; color: white; font-weight: 600;">
            Toggle Light/Dark Mode
        </button>

        <form method="GET" action="movies.php" style="margin: 30px 0;">
            <input type="text" name="search" placeholder="Search by title..." value="<?= htmlspecialchars($search) ?>" style="padding: 8px; width: 250px; border-radius: 5px; border: 1px solid #ccc;">
            <button type="submit" style="padding: 8px 12px; background-color: #007bff; color: white; border: none; border-radius: 5px; font-weight: bold;">
                🔍 Search
            </button>
        </form>

        <div class="movie-container">
            <?php if (count($movies) === 0): ?>
                <p>No movies found.</p>
            <?php else: ?>
                <?php foreach ($movies as $movie): ?>
                    <div class="movie-card">
                        <img src="<?= htmlspecialchars($movie['poster']) ?>" alt="Poster" class="movie-poster">
                        <div class="movie-info">
                            <h3><?= htmlspecialchars($movie['title']) ?> (<?= htmlspecialchars($movie['release_year']) ?>)</h3>
                            <p><strong>Genre:</strong> <?= htmlspecialchars($movie['genre']) ?></p>
                            <p><strong>Rating:</strong> <?= number_format($movie['rating'], 1) ?>/10</p>
                            <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($movie['description'])) ?></p>

                            <div class="action-buttons">
                                <a href="edit_movie.php?id=<?= $movie['id'] ?>" class="edit-btn">✏️ Edit</a>
                                <a href="delete_movie.php?id=<?= $movie['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this movie?');">🗑 Delete</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="../../js/theme.js"></script>
</body>
</html>
