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

    #add{
    display: center;
    min-width: 100%;
  
    
    padding: 16rem;
    box-sizing: border-box;

    
    
   

    
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

            <button type="submit" class="logout-btn" style="background-color: #28a745;">Save Movie</button>
            <a href="movies.php" class="logout-btn" style="background-color: gray;">Cancel</a>
        </form>
    </div>

     <script src="../../js/theme.js"></script>
</body>
</html>
