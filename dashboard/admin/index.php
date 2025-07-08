<?php
session_start();

// Your existing session checks here (if any)
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../css/styles.css">  <!-- Adjust path if needed -->
</head>
<body>
    <div class="dashboard-container">
        <h2>Admin Dashboard</h2>

        <!-- Theme toggle button -->
        <button id="toggle-theme-btn" 
            style="margin-bottom: 20px; padding: 8px 15px; cursor: pointer; border-radius: 5px; border: none; background-color: #007bff; color: white; font-weight: 600;">
            Toggle Light/Dark Mode
        </button>

        <!-- Your action buttons -->
        <a href="add_movie.php" class="logout-btn" style="background-color: #007bff;">➕ Add Movie</a>
        <a href="movies.php" class="logout-btn" style="background-color: #17a2b8;">🎬 Manage Movies</a>
        <a href="../../logout.php" class="logout-btn" style="background-color: #e03e2f;">Logout</a>
    </div>

    <script src="../../js/theme.js"></script> <!-- Adjust path if needed -->
</body>
</html>
