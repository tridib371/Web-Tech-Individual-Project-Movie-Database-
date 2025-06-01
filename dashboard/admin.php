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
        #for-admin{
            margin-top:200px;
            
        }
        
    </style>
</head>
<body>
    <div id="for-admin"; class="dashboard-container">
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
        <a href="../logout.php" class="logout-btn">Logout</a>
      </div>

    </div>
    <script src="../js/theme.js"></script>
</body>
</html>
