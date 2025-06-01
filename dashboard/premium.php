<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'premium') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Premium Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">

  <style>
        #premium{
    display: block;
    width: 100%;
  
    
    padding-left: 17rem;
    padding-top: 12rem;

    box-sizing: border-box;
    overflow: hidden;
    
}
  </style>
</head>
<body id="premium">
    <div class="dashboard-container">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?> (Premium User)</h2>
        <p>This is the premium user dashboard.</p>
        <button id="toggle-theme-btn" style="margin-bottom: 20px; padding: 8px 15px; cursor: pointer; border-radius: 5px; border: none; background-color: #007bff; color: white; font-weight: 600;">
    Toggle Light/Dark Mode
</button>
    
         <span>&nbsp;&nbsp;</span>

     <a href="users/profile.php" class="logout-btn" style="background-color: #6c757d;">👤 My Profile</a>
         

         <span>&nbsp;&nbsp;</span>
 <a href="user/movies.php" class="logout-btn" style="background-color: #17a2b8;">🎬 View Movies</a>
          
         <span>&nbsp;&nbsp;</span>
          
        <a href="../logout.php" class="logout-btn">Logout</a>

    </div>


    <script src="../js/theme.js"></script>
</body>
</html>
