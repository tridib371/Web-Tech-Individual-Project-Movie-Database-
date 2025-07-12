<?php
session_start();
require_once 'inc/db.php';

$email = $password = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $errors[] = "Both fields are required.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            // Redirect by role
            switch ($user['role']) {
                case 'general':
                    header('Location: dashboard/general.php');
                    break;
                case 'premium':
                    header('Location: dashboard/premium.php');
                    break;
                case 'admin':
                    header('Location: dashboard/admin.php');
                    break;
            }
            exit;
        } else {
            $errors[] = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Movie DB</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1a1a2e;
            --secondary-color: #16213e;
            --accent-color: #f05454;
            --text-color: #e6e6e6;
            --light-accent: #4cc9f0;
            --dark-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
            color: var(--text-color);
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80') center/cover no-repeat;
            opacity: 0.15;
            z-index: -1;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            background: rgba(26, 26, 46, 0.9);
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: var(--dark-shadow);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
            transition: var(--transition);
            margin: auto; /* Ensures perfect centering */
        }

        .login-container::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to bottom right,
                transparent 45%,
                rgba(76, 201, 240, 0.1) 50%,
                transparent 55%
            );
            transform: rotate(30deg);
            animation: shine 6s infinite;
            z-index: 0;
        }

        @keyframes shine {
            0% { transform: rotate(30deg) translate(-30%, -30%); }
            100% { transform: rotate(30deg) translate(30%, 30%); }
        }

        h2 {
            font-family: 'Playfair Display', serif;
            text-align: center;
            margin-bottom: 2rem;
            color: white;
            font-size: 2.2rem;
            position: relative;
            z-index: 1;
        }

        h2::after {
            content: '';
            display: block;
            width: 60px;
            height: 3px;
            background: var(--accent-color);
            margin: 0.8rem auto;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--light-accent);
        }

        input[type="email"], 
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            font-size: 1rem;
            color: white;
            transition: var(--transition);
        }

        input[type="email"]:focus, 
        input[type="password"]:focus {
            outline: none;
            border-color: var(--light-accent);
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 0 3px rgba(76, 201, 240, 0.2);
        }

        input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(45deg, var(--accent-color), #d83a3a);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 1rem;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, #d83a3a, var(--accent-color));
            transition: var(--transition);
            z-index: -1;
        }

        button:hover::before {
            left: 0;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(240, 84, 84, 0.4);
        }

        button:active {
            transform: translateY(0);
        }

        .errors {
            color: #ff6b6b;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: rgba(255, 0, 0, 0.1);
            border-left: 4px solid #ff6b6b;
            border-radius: 0 4px 4px 0;
        }

        .errors ul {
            list-style-position: inside;
        }

        .errors li {
            margin-bottom: 0.3rem;
        }

        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            color: rgba(255, 255, 255, 0.7);
            position: relative;
            z-index: 1;
        }

        .register-link a {
            color: var(--light-accent);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            position: relative;
        }

        .register-link a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--light-accent);
            transition: var(--transition);
        }

        .register-link a:hover::after {
            width: 100%;
        }

        .movie-icon {
            text-align: center;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .movie-icon i {
            font-size: 2.5rem;
            color: var(--accent-color);
        }

        /* Film strip effect on sides */
        .film-strip {
            position: absolute;
            height: 100%;
            width: 50px;
            background: repeating-linear-gradient(
                to bottom,
                #000,
                #000 20px,
                rgba(240, 84, 84, 0.3) 20px,
                rgba(240, 84, 84, 0.3) 40px
            );
            top: 0;
            opacity: 0.2;
        }

        .film-strip.left {
            left: 0;
            border-radius: 15px 0 0 15px;
        }

        .film-strip.right {
            right: 0;
            border-radius: 0 15px 15px 0;
        }

        @media (max-width: 768px) {
            .login-container {
                padding: 2rem 1.5rem;
            }
            
            .film-strip {
                width: 30px;
            }
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 1.5rem 1rem;
                width: 95%;
            }
            
            h2 {
                font-size: 1.8rem;
            }
            
            .film-strip {
                display: none;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="film-strip left"></div>
        <div class="film-strip right"></div>
        
        <div class="movie-icon">
            <i class="fas fa-film"></i>
        </div>
        
        <h2>Movie DB Login</h2>

        <?php if (!empty($errors)): ?>
            <div class="errors">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="Enter your email">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password">
            </div>

            <button type="submit">
                <i class="fas fa-ticket-alt" style="margin-right: 8px;"></i> Login
            </button>
        </form>

        <div class="register-link">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>
</body>
</html>
