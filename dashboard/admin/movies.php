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
    /* Cosmic Color Theme */
    .light-mode, .dark-mode {
        --bg-primary: #0a0a1f;
        --bg-secondary: #151530;
        --bg-card: linear-gradient(135deg, #1a1a3e, #2d1b69, #4a1e7a);
        --text-primary: #ffffff;
        --text-secondary: #e0e0ff;
        --text-accent: #ff2e63;
        --neon-pink: #ff2e63;
        --neon-blue: #00f3ff;
        --neon-purple: #9d4edd;
        --neon-green: #00ff88;
        --neon-yellow: #ffd700;
        --neon-orange: #ff6b35;
    }

    body {
        background: var(--bg-primary);
        color: var(--text-primary);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
        overflow-x: hidden;
        background-image: 
            radial-gradient(circle at 10% 20%, rgba(255, 46, 99, 0.1) 0%, transparent 20%),
            radial-gradient(circle at 90% 80%, rgba(0, 243, 255, 0.1) 0%, transparent 20%),
            radial-gradient(circle at 50% 50%, rgba(157, 78, 221, 0.05) 0%, transparent 50%);
    }

    /* Animated background particles */
    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: 
            radial-gradient(2px 2px at 20% 30%, rgba(255, 46, 99, 0.6) 50%, transparent 100%),
            radial-gradient(2px 2px at 40% 70%, rgba(0, 243, 255, 0.6) 50%, transparent 100%),
            radial-gradient(2px 2px at 60% 20%, rgba(157, 78, 221, 0.6) 50%, transparent 100%),
            radial-gradient(2px 2px at 80% 50%, rgba(0, 255, 136, 0.6) 50%, transparent 100%),
            radial-gradient(2px 2px at 30% 80%, rgba(255, 215, 0, 0.6) 50%, transparent 100%);
        background-repeat: repeat;
        background-size: 200px 200px;
        animation: particleMove 20s infinite linear;
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
            inset 0 1px 0 rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        transform-style: preserve-3d;
        perspective: 1000px;
    }

    /* Holographic border effect */
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
        animation: holographicBorder 4s linear infinite;
        transition: opacity 0.4s ease;
    }

    .movie-card:hover::before {
        opacity: 1;
    }

    .movie-card:hover {
        transform: translateY(-20px) rotateX(5deg) rotateY(5deg) scale(1.03);
        box-shadow: 
            0 35px 60px rgba(255, 46, 99, 0.3),
            0 0 100px rgba(157, 78, 221, 0.2),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }

    /* Floating poster with reflection */
    .poster-container {
        position: relative;
        transform-style: preserve-3d;
    }

    .movie-poster {
        width: 180px;
        height: 270px;
        border-radius: 18px;
        object-fit: cover;
        box-shadow: 
            0 20px 40px rgba(0, 0, 0, 0.8),
            0 0 50px rgba(255, 46, 99, 0.3);
        border: 3px solid transparent;
        background: linear-gradient(45deg, var(--neon-pink), var(--neon-blue)) border-box;
        transition: all 0.5s ease;
        position: relative;
        z-index: 2;
        filter: brightness(1.1) contrast(1.1);
    }

    .movie-card:hover .movie-poster {
        transform: scale(1.15) rotateY(10deg) rotateX(5deg);
        box-shadow: 
            0 30px 60px rgba(255, 46, 99, 0.5),
            0 0 80px rgba(0, 243, 255, 0.4);
        animation: posterGlow 2s ease-in-out infinite alternate;
    }

    /* Reflection effect */
    .poster-container::after {
        content: '';
        position: absolute;
        bottom: -185px;
        left: 0;
        width: 180px;
        height: 60px;
        background: linear-gradient(transparent, rgba(255, 255, 255, 0.1));
        border-radius: 18px;
        transform: scaleY(-1) rotateX(180deg);
        opacity: 0.3;
        filter: blur(5px);
        transition: all 0.5s ease;
    }

    .movie-card:hover .poster-container::after {
        opacity: 0.6;
        transform: scaleY(-1) rotateX(180deg) translateY(-10px);
    }

    .movie-info {
        flex: 1;
        position: relative;
        z-index: 2;
    }

    .movie-info h3 {
        font-size: 2.4em;
        margin: 0 0 25px 0;
        background: linear-gradient(45deg, var(--neon-yellow), var(--neon-pink), var(--neon-blue));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 900;
        text-shadow: 0 0 40px rgba(255, 46, 99, 0.5);
        position: relative;
        display: inline-block;
        animation: titleGlow 3s ease-in-out infinite alternate;
    }

    .movie-info h3::after {
        content: '';
        position: absolute;
        bottom: -12px;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--neon-pink), var(--neon-blue), var(--neon-green));
        border-radius: 2px;
        animation: lineFlow 2s ease-in-out infinite;
    }

    /* Animated info grid */
    .info-grid {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 15px 25px;
        margin: 25px 0;
        align-items: start;
    }

    .info-label {
        color: var(--neon-blue);
        font-weight: 800;
        font-size: 1.2em;
        text-shadow: 0 0 15px rgba(0, 243, 255, 0.7);
        animation: labelPulse 4s ease-in-out infinite;
        position: relative;
    }

    .info-label::before {
        content: '✦';
        margin-right: 8px;
        color: var(--neon-green);
        animation: spin 3s linear infinite;
    }

    .info-value {
        color: var(--text-primary);
        font-weight: 600;
        font-size: 1.1em;
        text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
    }

    .rating-stars {
        color: var(--neon-yellow);
        font-weight: bold;
        text-shadow: 0 0 15px rgba(255, 215, 0, 0.7);
        animation: starTwinkle 2s ease-in-out infinite;
    }

    /* Animated description */
    .description-box {
        background: rgba(0, 0, 0, 0.5);
        padding: 25px;
        border-radius: 15px;
        border-left: 6px solid var(--neon-purple);
        margin: 25px 0;
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(5px);
        border-right: 1px solid rgba(255, 255, 255, 0.1);
    }

    .description-box::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(157, 78, 221, 0.2), transparent);
        transition: left 0.8s ease;
    }

    .movie-card:hover .description-box::before {
        left: 100%;
    }

    /* Quantum action buttons */
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
        transition: all 0.4s ease;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-size: 0.9em;
        position: relative;
        overflow: hidden;
        border: 2px solid transparent;
        background: linear-gradient(45deg, var(--neon-pink), var(--neon-purple));
        box-shadow: 
            0 10px 30px rgba(255, 46, 99, 0.4),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
        animation: buttonFloat 3s ease-in-out infinite;
    }

    .edit-btn {
        background: linear-gradient(45deg, var(--neon-green), var(--neon-blue));
        box-shadow: 
            0 10px 30px rgba(0, 255, 136, 0.4),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
        animation-delay: 0.5s;
    }

    .delete-btn {
        background: linear-gradient(45deg, var(--neon-orange), var(--neon-pink));
        box-shadow: 
            0 10px 30px rgba(255, 107, 53, 0.4),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
        animation-delay: 1s;
    }

    .action-buttons a::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.6s ease;
    }

    .action-buttons a:hover::before {
        left: 100%;
    }

    .action-buttons a:hover {
        transform: translateY(-5px) scale(1.1);
        box-shadow: 
            0 20px 40px rgba(255, 46, 99, 0.6),
            0 0 30px rgba(255, 255, 255, 0.3),
            inset 0 1px 0 rgba(255, 255, 255, 0.3);
        animation: none;
    }

    /* Cosmic search bar */
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
        border: 2px solid transparent;
        background: linear-gradient(45deg, #1a1a3e, #2d1b69) padding-box,
                   linear-gradient(45deg, var(--neon-pink), var(--neon-blue), var(--neon-green)) border-box;
        color: var(--text-primary);
        font-size: 1.2em;
        box-shadow: 
            0 15px 35px rgba(255, 46, 99, 0.3),
            inset 0 2px 0 rgba(255, 255, 255, 0.1);
        transition: all 0.4s ease;
        backdrop-filter: blur(10px);
    }

    form[method="GET"] input:focus {
        outline: none;
        box-shadow: 
            0 20px 45px rgba(255, 46, 99, 0.5),
            0 0 60px rgba(0, 243, 255, 0.3),
            inset 0 2px 0 rgba(255, 255, 255, 0.2);
        transform: scale(1.05);
        animation: searchPulse 2s ease-in-out infinite;
    }

    form[method="GET"] input::placeholder {
        color: var(--text-secondary);
        text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
    }

    form[method="GET"] button {
        padding: 20px 40px;
        background: linear-gradient(45deg, var(--neon-pink), var(--neon-purple), var(--neon-blue));
        color: white;
        border: none;
        border-radius: 50px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.4s ease;
        box-shadow: 
            0 15px 35px rgba(255, 46, 99, 0.4),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
        text-transform: uppercase;
        letter-spacing: 2px;
        font-size: 1.1em;
        position: relative;
        overflow: hidden;
        background-size: 200% 200%;
        animation: gradientShift 3s ease infinite;
    }

    form[method="GET"] button:hover {
        transform: translateY(-5px) scale(1.1);
        box-shadow: 
            0 25px 50px rgba(255, 46, 99, 0.6),
            0 0 40px rgba(157, 78, 221, 0.4);
    }

    /* Epic dashboard header */
    .dashboard-container h2 {
        font-size: 3.5em;
        margin-bottom: 50px;
        text-align: center;
        background: linear-gradient(45deg, 
            var(--neon-yellow), 
            var(--neon-pink), 
            var(--neon-blue), 
            var(--neon-green),
            var(--neon-yellow));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 900;
        text-shadow: 0 0 50px rgba(255, 46, 99, 0.5);
        position: relative;
        padding-bottom: 25px;
        animation: titleShine 4s ease-in-out infinite;
    }

    .dashboard-container h2::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 300px;
        height: 6px;
        background: linear-gradient(90deg, 
            var(--neon-pink), 
            var(--neon-blue), 
            var(--neon-green),
            var(--neon-yellow),
            var(--neon-pink));
        border-radius: 3px;
        animation: rainbowFlow 3s linear infinite;
        background-size: 200% 100%;
    }

    /* Animations */
    @keyframes holographicBorder {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    @keyframes particleMove {
        0% { transform: translateY(0px) translateX(0px); }
        100% { transform: translateY(-200px) translateX(100px); }
    }

    @keyframes posterGlow {
        0% { box-shadow: 0 30px 60px rgba(255, 46, 99, 0.5), 0 0 80px rgba(0, 243, 255, 0.4); }
        100% { box-shadow: 0 30px 60px rgba(255, 46, 99, 0.8), 0 0 120px rgba(157, 78, 221, 0.6); }
    }

    @keyframes titleGlow {
        0% { text-shadow: 0 0 40px rgba(255, 46, 99, 0.5); }
        100% { text-shadow: 0 0 60px rgba(0, 243, 255, 0.8), 0 0 80px rgba(157, 78, 221, 0.6); }
    }

    @keyframes lineFlow {
        0% { background-position: -100% 0; }
        100% { background-position: 200% 0; }
    }

    @keyframes buttonFloat {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-8px); }
    }

    @keyframes gradientShift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    @keyframes rainbowFlow {
        0% { background-position: 0% 0; }
        100% { background-position: 200% 0; }
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    @keyframes labelPulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    @keyframes starTwinkle {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.6; }
    }

    @keyframes searchPulse {
        0%, 100% { box-shadow: 0 20px 45px rgba(255, 46, 99, 0.5), 0 0 60px rgba(0, 243, 255, 0.3); }
        50% { box-shadow: 0 20px 45px rgba(255, 46, 99, 0.7), 0 0 80px rgba(157, 78, 221, 0.5); }
    }

    @keyframes titleShine {
        0%, 100% { filter: brightness(1); }
        50% { filter: brightness(1.3); }
    }

    /* Staggered card animations */
    .movie-card {
        animation: cardEntrance 0.8s ease-out forwards;
        opacity: 0;
        transform: translateY(50px) scale(0.9);
    }

    .movie-card:nth-child(1) { animation-delay: 0.1s; }
    .movie-card:nth-child(2) { animation-delay: 0.2s; }
    .movie-card:nth-child(3) { animation-delay: 0.3s; }
    .movie-card:nth-child(4) { animation-delay: 0.4s; }
    .movie-card:nth-child(5) { animation-delay: 0.5s; }
    .movie-card:nth-child(6) { animation-delay: 0.6s; }

    @keyframes cardEntrance {
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* No movies message */
    p {
        text-align: center;
        font-size: 1.8em;
        color: var(--neon-blue);
        padding: 60px;
        background: rgba(0, 0, 0, 0.6);
        border-radius: 25px;
        border: 3px solid var(--neon-blue);
        text-shadow: 0 0 30px rgba(0, 243, 255, 0.7);
        box-shadow: 
            0 20px 40px rgba(0, 243, 255, 0.3),
            inset 0 0 50px rgba(0, 243, 255, 0.1);
        backdrop-filter: blur(10px);
        animation: noMoviesPulse 3s ease-in-out infinite;
    }

    @keyframes noMoviesPulse {
        0%, 100% { 
            box-shadow: 0 20px 40px rgba(0, 243, 255, 0.3), inset 0 0 50px rgba(0, 243, 255, 0.1);
            transform: scale(1);
        }
        50% { 
            box-shadow: 0 25px 50px rgba(0, 243, 255, 0.5), inset 0 0 80px rgba(0, 243, 255, 0.2);
            transform: scale(1.02);
        }
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
