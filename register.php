<?php
require_once 'inc/db.php';

$name = $email = $password = $confirm_password = $role = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = strtolower(trim($_POST['email']));  // lowercase and trim email here
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    if (!in_array($role, ['general', 'premium'])) {
        $role = 'general';
    }

    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    } else {
        // Check if email already exists (email stored and checked in lowercase)
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        if (!$stmt) {
            die("Prepare failed: " . implode(":", $conn->errorInfo()));
        }
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $errors[] = "Email already registered.";
        }
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hashedPassword, $role]);

        header("Location: login.php");
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Movie DB</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
     <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
    background: linear-gradient(135deg, #1a2a6c, #2c5a7d, #4a7ba6);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 40px 20px; 
    position: relative;
    
}

        /* Background film reel effect */
        body::before {
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(255,255,255,0.05) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(255,255,255,0.05) 0%, transparent 20%);
            z-index: -1;
        }

        .register-container {
            width: 100%;
            max-width: 500px;
            background: rgba(19, 30, 49, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .header {
            background: linear-gradient(90deg, #ff416c, #ff4b2b);
            padding: 25px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #ff416c, #ff4b2b);
            filter: blur(10px);
        }

        h2 {
            color: white;
            font-size: 2.2rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 2;
        }

        .film-icon {
            position: absolute;
            top: 15px;
            right: 15px;
            color: rgba(255, 255, 255, 0.15);
            font-size: 2.5rem;
        }

        .form-content {
            padding: 30px;
        }

        .errors {
            background: rgba(255, 50, 50, 0.2);
            border-left: 4px solid #ff416c;
            padding: 12px 15px;
            margin-bottom: 25px;
            border-radius: 0 4px 4px 0;
        }

        .errors ul {
            list-style: none;
        }

        .errors li {
            color: #ff9f9f;
            font-size: 0.95rem;
            padding: 5px 0;
            display: flex;
            align-items: center;
        }

        .errors li i {
            margin-right: 8px;
            font-size: 0.9rem;
        }

        .input-group {
            margin-bottom: 20px;
            position: relative;
        }

        label {
            display: block;
            color:rgb(39, 208, 76);
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 42px;
            color: #ff4b2b;
            font-size: 1.1rem;
        }

        input[type="text"], 
        input[type="email"], 
        input[type="password"], 
        select {
            width: 100%;
            padding: 14px 20px 14px 45px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #ff416c;
            background: rgba(255, 255, 255, 0.12);
            box-shadow: 0 0 0 3px rgba(255, 65, 108, 0.2);
        }

        select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23ff4b2b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 42px;
            color: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            font-size: 1.1rem;
            transition: color 0.3s;
        }

        .password-toggle:hover {
            color: #ff416c;
        }

        .role-description {
            display: flex;
            justify-content: space-between;
            margin-top: 5px;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.6);
        }

        .role-description span {
            background: rgba(255, 255, 255, 0.08);
            padding: 3px 8px;
            border-radius: 4px;
            margin-top: 3px;
        }

        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(90deg, #ff416c, #ff4b2b);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 15px;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
        }

        button::after {
            content: "";
            position: absolute;
            top: -50%;
            left: -60%;
            width: 20px;
            height: 200%;
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(25deg);
            transition: all 0.4s;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 15px rgba(255, 75, 43, 0.4);
        }

        button:hover::after {
            left: 120%;
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.95rem;
        }

        .login-link a {
            color: #ff9f9f;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
            position: relative;
        }

        .login-link a::after {
            content: "";
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #ff416c;
            transition: width 0.3s;
        }

        .login-link a:hover {
            color: #ff416c;
        }

        .login-link a:hover::after {
            width: 100%;
        }

        /* Animation for form elements */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .register-container > * {
            animation: fadeIn 2.5s ease-out forwards;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .register-container {
                max-width: 100%;
            }
            
            .form-content {
                padding: 20px;
            }
            
            h2 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="header">
            <h2>Create Your Account</h2>
            <i class="fas fa-film film-icon"></i>
        </div>

        <div class="form-content">

            <!-- ✅ Show errors only after form submission -->
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($errors)): ?>
                <div class="errors">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="input-group">
                    <label for="name">Full Name</label>
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" id="name" name="name" placeholder="Enter your full name" value="<?= htmlspecialchars($name) ?>">
                </div>

                <div class="input-group">
                    <label for="email">Email Address</label>
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" id="email" name="email" placeholder="Enter your email" value="<?= htmlspecialchars($email) ?>">
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="password" name="password" placeholder="Create a password">
                    <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                </div>

                <div class="input-group">
                    <label for="confirm_password">Confirm Password</label>
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password">
                    <i class="fas fa-eye password-toggle" id="toggleConfirmPassword"></i>
                </div>

                <div class="input-group">
                    <label for="role">Account Type</label>
                    <select id="role" name="role">
                        <option value="general"  <?= $role === 'general' ? 'selected' : '' ?>>General Member</option>
                        <option value="premium" <?= $role === 'premium' ? 'selected' : '' ?>>Premium Member</option>
                    </select>
                    <div class="role-description">
                        <span>General: Free access</span>
                        <span>Premium: Exclusive content</span>
                    </div>
                </div>

                <button type="submit">
                    <i class="fas fa-ticket-alt"></i> Register Now
                </button>
            </form>

            <div class="login-link">
                Already have an account? <a href="login.php">Sign in here</a>.
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function () {
            const input = document.getElementById('password');
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        document.getElementById('toggleConfirmPassword').addEventListener('click', function () {
            const input = document.getElementById('confirm_password');
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        // Input animations (optional delay)
        document.querySelectorAll('.input-group').forEach((group, index) => {
            group.style.animationDelay = `${index * 0.5}s`;
        });
    </script>
</body>
</html>
