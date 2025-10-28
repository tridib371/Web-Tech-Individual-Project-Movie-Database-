<?php
session_start();
require_once dirname(__DIR__, 2) . '/inc/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['general', 'premium'])) {
    header("Location: ../../login.php");
    exit;
}


$isPremium = $_SESSION['user_role'] === 'premium';


$search = $_GET['search'] ?? '';
if ($search) {
    $stmt = $conn->prepare("SELECT * FROM movies WHERE title LIKE ? ORDER BY created_at DESC");
    $stmt->execute(["%$search%"]);
} else {
    $stmt = $conn->query("SELECT * FROM movies ORDER BY created_at DESC");
}
$movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Browse Movies</title>
    <link rel="stylesheet" href="../../css/styles.css">
   

      <style>
.dashboard-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 30px;
    background: var(--container-bg, #fff);
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-top: 20px;
    margin-bottom: 20px;
}

h2 {
    text-align: center;
    color: var(--text-primary, #2c3e50);
    margin-bottom: 40px;
    font-size: 2.2rem;
    font-weight: 700;
    position: relative;
}

h2::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 3px;
    background: linear-gradient(90deg, #3498db, #2ecc71);
    border-radius: 2px;
}

.logout-btn {
    position: absolute;
    top: 30px;
    right: 30px;
    padding: 12px 24px;
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(231, 76, 60, 0.3);
}

.logout-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(231, 76, 60, 0.4);
}

.movie-container {
    display: flex;
    flex-direction: column;
    gap: 25px;
    margin-top: 40px;
}

.movie-card {
    background: var(--card-bg, #fff);
    border: 1px solid var(--border-color, #e1e8ed);
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.movie-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(180deg, #3498db, #2ecc71);
}

.movie-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
    border-color: var(--accent-color, #3498db);
}

.movie-card-wrapper {
    display: flex;
    gap: 25px;
    align-items: flex-start;
}

.movie-card img {
    width: 140px;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid var(--border-color, #e1e8ed);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.movie-card:hover img {
    transform: scale(1.03);
}

.movie-details {
    flex: 1;
}

.movie-details h3 {
    color: var(--text-primary, #2c3e50);
    margin: 0 0 12px 0;
    font-size: 1.4rem;
    font-weight: 700;
}

.movie-details h3::after {
    content: '';
    display: block;
    width: 40px;
    height: 2px;
    background: var(--accent-color, #3498db);
    margin-top: 8px;
    border-radius: 1px;
}

.movie-details p {
    color: var(--text-secondary, #7f8c8d);
    margin: 8px 0;
    line-height: 1.5;
    font-size: 1rem;
}

.movie-details strong {
    color: var(--text-primary, #2c3e50);
    font-weight: 600;
}

/* Search Form Styles */
form[method="GET"] {
    display: flex;
    gap: 12px;
    justify-content: center;
    margin-bottom: 40px;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

form[method="GET"] input {
    padding: 12px 16px;
    border: 2px solid var(--border-color, #e1e8ed);
    border-radius: 8px;
    font-size: 1rem;
    background: var(--input-bg, #fff);
    color: var(--text-primary, #333);
    flex: 1;
    transition: all 0.3s ease;
}

form[method="GET"] input:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

form[method="GET"] button {
    padding: 12px 24px;
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(52, 152, 219, 0.3);
}

form[method="GET"] button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.4);
}

/* Theme Toggle Button */
#toggle-theme-btn {
    padding: 12px 24px;
    background: linear-gradient(135deg, #9b59b6, #8e44ad);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    margin-bottom: 30px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(155, 89, 182, 0.3);
}

#toggle-theme-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(155, 89, 182, 0.4);
}

/* Light/Dark Mode Variables */
.light-mode {
    --text-primary: #2c3e50;
    --text-secondary: #7f8c8d;
    --card-bg: #fff;
    --container-bg: #fff;
    --border-color: #e1e8ed;
    --input-bg: #fff;
    --accent-color: #3498db;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    min-height: 100vh;
}

.dark-mode {
    --text-primary: #ecf0f1;
    --text-secondary: #bdc3c7;
    --card-bg: #34495e;
    --container-bg: #2c3e50;
    --border-color: #4a5f7a;
    --input-bg: #34495e;
    --accent-color: #3498db;
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    min-height: 100vh;
}

.dark-mode .movie-card {
    background: #34495e;
    border-color: #4a5f7a;
}

.dark-mode form[method="GET"] input {
    background: #34495e;
    color: #ecf0f1;
    border-color: #4a5f7a;
}

/* No movies message */
.movie-container p {
    text-align: center;
    color: var(--text-secondary, #7f8c8d);
    font-size: 1.2rem;
    margin-top: 60px;
    padding: 40px;
    background: var(--card-bg, #fff);
    border-radius: 12px;
    border: 2px dashed var(--border-color, #e1e8ed);
}

/* Premium badge for premium content */
.premium-badge {
    background: linear-gradient(135deg, #f39c12, #e67e22);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    margin-left: 10px;
    vertical-align: middle;
}

/* Responsive Design */
@media (max-width: 768px) {
    .dashboard-container {
        padding: 20px;
        margin: 10px;
    }
    
    .movie-card-wrapper {
        flex-direction: column;
        text-align: center;
    }
    
    .movie-card img {
        align-self: center;
        width: 120px;
        height: 180px;
    }
    
    .logout-btn {
        position: relative;
        top: auto;
        right: auto;
        display: inline-block;
        margin-bottom: 20px;
    }
    
    form[method="GET"] {
        flex-direction: column;
        align-items: center;
    }
    
    form[method="GET"] input {
        width: 100%;
        max-width: 300px;
    }
    
    h2 {
        font-size: 1.8rem;
    }
}
</style>

</head>
<body class="<?= isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light' ?>-mode">
    <div class="dashboard-container">
        <h2>Movie Library</h2>
        <a href="../../logout.php" class="logout-btn" style="background-color: #e03e2f;">Logout</a>

        <button id="toggle-theme-btn" style="margin-bottom: 20px; padding: 8px 15px; cursor: pointer; border-radius: 5px; border: none; background-color: #007bff; color: white; font-weight: 600;">
            Toggle Light/Dark Mode
        </button>

        <!-- Search Form -->
        <form method="GET" action="movies.php" style="margin-bottom: 20px;">
            <input type="text" name="search" placeholder="Search by title..." value="<?= htmlspecialchars($search) ?>" style="padding: 8px; width: 250px; border-radius: 5px; border: 1px solid #ccc;">
            <button type="submit" style="padding: 8px 12px; background-color: #007bff; color: white; border: none; border-radius: 5px; font-weight: bold;">
                🔍 Search
            </button>
        </form>

        <div class="movie-container">
            <?php if (count($movies) === 0): ?>
                <p>No movies found.</p>
            <?php else: ?>
                <?php foreach ($movies as $movie): ?>
                    <div class="movie-card">
                        <div class="movie-card-wrapper">
                            <?php if ($isPremium): ?>
                                <img src="<?= htmlspecialchars($movie['poster']) ?>" alt="Poster">
                            <?php endif; ?>
                            <div class="movie-details">
                                <h3><?= htmlspecialchars($movie['title']) ?> (<?= htmlspecialchars($movie['release_year']) ?>)</h3>
                                <p><strong>Genre:</strong> <?= htmlspecialchars($movie['genre']) ?></p>
                                <?php if ($isPremium): ?>
                                    <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($movie['description'])) ?></p>
                                    <p><strong>Rating:</strong> <?= number_format($movie['rating'], 1) ?>/10</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="../../js/theme.js"></script>
</body>
</html>
