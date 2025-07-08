<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">

    <style>
        /* Default light mode styles */
        body {
            background-color:rgb(226, 223, 210);
            color: #333;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        body.dark-mode {
            background-color: #121212;
            color: #ffffff;
        }

        #for-admin {
            margin-top: 200px;
            text-align: center;
        }

        .logout-btn {
            padding: 8px 15px;
            border: none;
            color: white;
            text-decoration: none;
            font-weight: 600;
            border-radius: 5px;
        }

        #btn-space {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div id="for-admin" class="dashboard-container">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?> (Admin)</h2>
        <p>This is the admin dashboard.</p>

        <div id="btn-space">
            <button id="toggle-theme-btn" style="margin-bottom: 20px; padding: 8px 15px; cursor: pointer; border-radius: 5px; border: none; background-color: #007bff; color: white; font-weight: 600;">
                Toggle Light/Dark Mode
            </button>

            <span>&nbsp;&nbsp;</span>

            <a href="users/profile.php" class="logout-btn" style="background-color: #6c757d;">👤 My Profile</a>
            <span>&nbsp;&nbsp;</span>

            <a href="admin/add_movie.php" class="logout-btn" style="background-color: #007bff;">➕ Add Movie</a>
            <span>&nbsp;&nbsp;</span>

            <a href="admin/movies.php" class="logout-btn" style="background-color: #17a2b8;">🎬 Manage Movies</a>
            <span>&nbsp;&nbsp;</span>

            <a href="../logout.php" class="logout-btn" style="background-color: #dc3545;">🚪 Logout</a>
        </div>
    </div>

    <script src="../js/theme.js"></script>
</body>
</html>
