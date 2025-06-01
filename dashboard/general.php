<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'general') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>General Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">

    <style>

         #general{
    display: block;
    width: 100%;
  
    
    padding-left: 17rem;
    padding-top: 12rem;

    box-sizing: border-box;
    overflow: hidden;
    
}
    </style>
</head>
<body id="general">
    <div class="dashboard-container">
        <button id="toggle-theme-btn" style="margin-bottom: 20px; padding: 8px 15px; cursor: pointer; border-radius: 5px; border: none; background-color: #007bff; color: white; font-weight: 600;">
    Toggle Light/Dark Mode
</button>

        <h2>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?> (General User)</h2>
        <p>This is the general user dashboard.</p>
        <a href="users/profile.php" class="logout-btn" style="background-color: #6c757d;">👤 My Profile</a>
         <span>&nbsp;&nbsp;</span>

        <a href="user/movies.php" class="logout-btn" style="background-color: #17a2b8;">🎬 Browse Movies</a>
        <span>&nbsp;&nbsp;</span>
        <a href="../logout.php" class="logout-btn">Logout</a>
    </div>







<script src="../js/theme.js"></script>




</body>
</html>
