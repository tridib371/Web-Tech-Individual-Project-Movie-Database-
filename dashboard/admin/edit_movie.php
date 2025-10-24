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

<style>
    /* Clean Modern Edit Form Theme */
    .light-mode, .dark-mode {
        --bg-primary: #0f141f;
        --bg-secondary: #1a1f2e;
        --form-bg: #1e2535;
        --input-bg: #2a3245;
        --input-border: #3a4255;
        --input-focus: #4f46e5;
        --text-primary: #f8fafc;
        --text-secondary: #cbd5e1;
        --accent-primary: #4f46e5;
        --accent-success: #10b981;
        --accent-danger: #ef4444;
        --shadow: rgba(0, 0, 0, 0.25);
    }

    body {
        background: var(--bg-primary);
        color: var(--text-primary);
        font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        min-height: 100vh;
        margin: 0;
        padding: 20px;
        display: flex;
        align-items: flex-start;
        justify-content: center;
    }

    .dashboard-container {
        background: var(--form-bg);
        padding: 45px;
        border-radius: 20px;
        box-shadow: 0 20px 40px var(--shadow);
        border: 1px solid rgba(255, 255, 255, 0.1);
        width: 100%;
        max-width: 600px;
        margin-top: 40px;
        animation: formSlideIn 0.6s ease-out;
    }

    .dashboard-container h2 {
        text-align: center;
        font-size: 2.5em;
        margin-bottom: 40px;
        color: var(--text-primary);
        font-weight: 700;
        position: relative;
    }

    .dashboard-container h2::after {
        content: '';
        position: absolute;
        bottom: -12px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: var(--accent-primary);
        border-radius: 2px;
        animation: lineExpand 0.8s ease-out;
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 25px;
    }

    label {
        font-size: 1.1em;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 8px;
        display: block;
        animation: fadeInUp 0.6s ease-out;
    }

    input[type="text"],
    input[type="number"],
    textarea {
        width: 100%;
        padding: 14px 18px;
        border: 2px solid var(--input-border);
        border-radius: 10px;
        background: var(--input-bg);
        color: var(--text-primary);
        font-size: 1em;
        font-family: inherit;
        transition: all 0.3s ease;
        box-sizing: border-box;
        animation: fadeInUp 0.6s ease-out;
    }

    input[type="text"]:focus,
    input[type="number"]:focus,
    textarea:focus {
        outline: none;
        border-color: var(--accent-primary);
        background: var(--input-bg);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        transform: translateY(-2px);
    }

    textarea {
        resize: vertical;
        min-height: 120px;
        line-height: 1.5;
    }

    /* Button Styles */
    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 35px;
        animation: fadeInUp 0.8s ease-out;
    }

    button[type="submit"] {
        padding: 12px 30px;
        border: none;
        border-radius: 8px;
        background: var(--accent-success);
        color: white;
        font-weight: 600;
        font-size: 0.95em;
        cursor: pointer;
        transition: all 0.3s ease;
        min-width: 140px;
        height: 46px;
        position: relative;
        overflow: hidden;
    }

    button[type="submit"]::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transition: left 0.5s ease;
    }

    button[type="submit"]:hover::before {
        left: 100%;
    }

    button[type="submit"]:hover {
        background: #0da271;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
    }

    .cancel-btn {
        padding: 12px 30px;
        border: 2px solid var(--input-border);
        border-radius: 8px;
        background: transparent;
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 0.95em;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        text-align: center;
        min-width: 120px;
        height: 46px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .cancel-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.05), transparent);
        transition: left 0.5s ease;
    }

    .cancel-btn:hover::before {
        left: 100%;
    }

    .cancel-btn:hover {
        border-color: var(--text-primary);
        color: var(--text-primary);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(255, 255, 255, 0.1);
    }

    /* Input validation styles */
    input:invalid, textarea:invalid {
        border-color: var(--accent-danger);
    }

    input:valid, textarea:valid {
        border-color: var(--accent-success);
    }

    /* Animations */
    @keyframes formSlideIn {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes lineExpand {
        from {
            width: 0;
        }
        to {
            width: 80px;
        }
    }

    /* Staggered animations */
    form > * {
        animation: fadeInUp 0.6s ease-out;
    }

    form label:nth-child(1) { animation-delay: 0.1s; }
    form input:nth-child(2) { animation-delay: 0.2s; }
    form label:nth-child(3) { animation-delay: 0.3s; }
    form textarea:nth-child(4) { animation-delay: 0.4s; }
    form label:nth-child(5) { animation-delay: 0.5s; }
    form input:nth-child(6) { animation-delay: 0.6s; }
    form label:nth-child(7) { animation-delay: 0.7s; }
    form input:nth-child(8) { animation-delay: 0.8s; }
    form label:nth-child(9) { animation-delay: 0.9s; }
    form input:nth-child(10) { animation-delay: 1s; }
    .form-actions { animation-delay: 1.1s; }

    /* Responsive design */
    @media (max-width: 768px) {
        .dashboard-container {
            padding: 35px 25px;
            margin: 20px 0;
        }
        
        .form-actions {
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }
        
        button[type="submit"],
        .cancel-btn {
            width: 100%;
            max-width: 250px;
        }
    }

    @media (max-width: 480px) {
        body {
            padding: 10px;
        }
        
        .dashboard-container {
            padding: 30px 20px;
        }
        
        .dashboard-container h2 {
            font-size: 2em;
        }
        
        button[type="submit"],
        .cancel-btn {
            padding: 10px 25px;
            height: 44px;
            font-size: 0.9em;
        }
    }
</style>

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
