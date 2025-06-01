<?php
session_start();
require_once dirname(__DIR__, 2) . '/inc/db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

// Get user info from session
$userId = $_SESSION['user_id'];
$userRole = $_SESSION['user_role'] ?? '';
$userName = $_SESSION['user_name'] ?? 'User';

// Fetch user details
$stmt = $conn->prepare("SELECT name, email, role FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Determine dashboard link based on role
$dashboardLink = match ($userRole) {
    'admin'    => '../admin.php',
    'general'  => '../general.php',
    'premium'  => '../premium.php',
    default    => '../../login.php'
};
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
    <link rel="stylesheet" href="../../css/styles.css">

    <style>
        #profile1{
    display: block;
    width: 100%;
  
    
    padding-left: 17rem;
    padding-top: 12rem;

    box-sizing: border-box;
    overflow: hidden;
    
}
    </style>
</head>
<body id="profile1" class="<?= isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light' ?>-mode">
    <div class="dashboard-container">
        <h2>Your Profile</h2>

        <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Role:</strong> <?= ucfirst(htmlspecialchars($user['role'])) ?></p>

        <a href="<?= $dashboardLink ?>" class="logout-btn" style="background-color:#007bff;">⬅ Back to Dashboard</a>
        <span>&nbsp;&nbsp;</span>
        <a href="../../logout.php" class="logout-btn" style="background-color:#e03e2f;">Logout</a>
    </div>

    <script src="../../js/theme.js"></script>
</body>
</html>
