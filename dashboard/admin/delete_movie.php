<?php
session_start();
require_once dirname(__DIR__, 2) . '/inc/db.php';

// Only admins allowed
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

// Validate movie ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid movie ID.");
}

$movie_id = (int) $_GET['id'];

// Delete movie
$stmt = $conn->prepare("DELETE FROM movies WHERE id = ?");
$stmt->execute([$movie_id]);

// Redirect
header("Location: movies.php");
exit;
