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
        .movie-card {
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 15px;
            background-color: #f9f9f9;
            display: flex;
            gap: 20px;
            align-items: flex-start;
        }

        .movie-poster {
            width: 120px;
            height: auto;
            border-radius: 8px;
        }

        .movie-info {
            flex: 1;
        }

        .action-buttons {
            margin-top: 15px;
        }

        .action-buttons a {
            margin-right: 10px;
            padding: 6px 12px;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            font-weight: 600;
        }

        .edit-btn {
            background-color: #28a745;
        }

        .delete-btn {
            background-color: #dc3545;
        }

        .movie-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
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
