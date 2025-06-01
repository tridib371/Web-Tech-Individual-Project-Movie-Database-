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
</head>
<body class="<?= isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light' ?>-mode">
    <div class="dashboard-container">
        <h2>Edit Movie</h2>

        <form method="post">
            <label>Title:</label><br>
            <input type="text" name="title" value="<?= htmlspecialchars($movie['title']) ?>" required><br><br>

            <label>Description:</label><br>
            <textarea name="description" rows="4" required><?= htmlspecialchars($movie['description']) ?></textarea><br><br>

            <label>Release Year:</label><br>
            <input type="number" name="release_year" value="<?= htmlspecialchars($movie['release_year']) ?>" min="1900" max="2099" required><br><br>

            <label>Genre:</label><br>
            <input type="text" name="genre" value="<?= htmlspecialchars($movie['genre']) ?>" required><br><br>

            <label>Poster URL:</label><br>
            <input type="text" name="poster" value="<?= htmlspecialchars($movie['poster']) ?>"><br><br>

            <button type="submit" class="logout-btn" style="background-color: #28a745;">Update Movie</button>
            <a href="movies.php" class="logout-btn" style="background-color: gray;">Cancel</a>
        </form>
    </div>
</body>
</html>
