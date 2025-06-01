<?php
session_start();
require_once dirname(__DIR__, 2) . '/inc/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['general', 'premium'])) {
    header("Location: ../../login.php");
    exit;
}

$isPremium = $_SESSION['user_role'] === 'premium';

// Handle search
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
    <title>Browse Movies</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <style>
        .movie-card {
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 10px;
            margin-top: 40px;
            margin-bottom: 40px;
            background-color: #f9f9f9;
        }

        .movie-card img {
            width: 120px;
            border-radius: 8px;
        }

        .movie-details {
            margin-left: 20px;
        }

        .movie-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
        }

        .movie-card-wrapper {
            display: flex;
            flex-direction: row;
            align-items: center;
            width: 100%;
        }

        #card {
            display: block;
            width: 100%;
            margin: 1rem auto;
            padding: 1rem;
            box-sizing: border-box;
            overflow: auto;
        }
    </style>
</head>
<body class="<?= isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light' ?>-mode">
    <div class="dashboard-container">
        <h2>Movie Library</h2>
        <a href="../../logout.php" class="logout-btn" style="background-color: #e03e2f;">Logout</a>

        <button id="toggle-theme-btn" style="margin-bottom: 20px; padding: 8px 15px; cursor: pointer; border-radius: 5px; border: none; background-color: #007bff; color: white; font-weight: 600;">
            Toggle Light/Dark Mode
        </button>

        <!-- Search Form -->
        <form method="GET" action="movies.php" style="margin-bottom: 20px;">
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
                        <div class="movie-card-wrapper">
                            <?php if ($isPremium): ?>
                                <img src="<?= htmlspecialchars($movie['poster']) ?>" alt="Poster">
                            <?php endif; ?>
                            <div class="movie-details">
                                <h3><?= htmlspecialchars($movie['title']) ?> (<?= htmlspecialchars($movie['release_year']) ?>)</h3>
                                <p><strong>Genre:</strong> <?= htmlspecialchars($movie['genre']) ?></p>
                                <?php if ($isPremium): ?>
                                    <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($movie['description'])) ?></p>
                                    <p><strong>Rating:</strong> <?= number_format($movie['rating'], 1) ?>/10</p>
                                <?php endif; ?>
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
