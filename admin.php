<?php
require_once 'config.php';

// Simple admin check - in real app, use proper role checking
if(isset($_SESSION['username']) && $_SESSION['username'] === 'admin') {
    header('Location: admin_panel.php');
    exit();
}

$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if($username === 'admin') {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = 'admin'");
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($admin && password_verify($password, $admin['password'])) {
                $_SESSION['user_id'] = $admin['id'];
                $_SESSION['username'] = $admin['username'];
                header('Location: admin_panel.php');
                exit();
            } else {
                $error = 'Invalid admin credentials';
            }
        } catch(PDOException $e) {
            $error = 'Database error';
        }
    } else {
        $error = 'Admin access only';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Metro Store</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-warning {
            background: rgba(247, 99, 12, 0.1);
            border: 1px solid var(--metro-accent);
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
            color: #FFA500;
        }
    </style>
</head>
<body>
    <header class="metro-header">
        <div class="logo">
            <div class="logo-icon">M</div>
            <span>Admin Portal</span>
        </div>
        <div class="nav-links">
            <a href="index.php" class="nav-button">Store Front</a>
        </div>
    </header>

    <div class="container">
        <div class="form-container">
            <div class="admin-warning">
                <i class="fas fa-shield-alt"></i> Restricted Area - Admin Access Only
            </div>
            
            <h2 class="form-title">Admin Login</h2>
            
            <?php if($error): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Admin Username</label>
                    <input type="text" id="username" name="username" class="form-control" value="admin" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn">Admin Login</button>
            </form>
            
            <div style="text-align: center; margin-top: 30px; color: #777; font-size: 0.9rem;">
                <p>Default credentials:</p>
                <p>Username: admin<br>Password: admin123</p>
                <p style="margin-top: 10px; color: #999; font-size: 0.8rem;">
                    <i class="fas fa-exclamation-triangle"></i> Change password after first login
                </p>
            </div>
        </div>
    </div>
</body>
</html>