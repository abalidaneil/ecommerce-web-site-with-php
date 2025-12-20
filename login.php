<?php
require_once 'config.php';

if(isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if(empty($username) || empty($password)) {
        $error = 'Please enter username and password';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                
                header('Location: index.php');
                exit();
            } else {
                $error = 'Invalid username or password';
            }
        } catch(PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Metro Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="metro-header">
        <div class="logo">
            <div class="logo-icon">M</div>
            <span>Metro Store</span>
        </div>
        <div class="nav-links">
            <a href="index.php" class="nav-button">Home</a>
            <a href="register.php" class="nav-button accent">Register</a>
        </div>
    </header>

    <div class="container">
        <div class="form-container">
            <h2 class="form-title">Login</h2>
            
            <?php if($error): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username or Email</label>
                    <input type="text" id="username" name="username" class="form-control" 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn">Login</button>
            </form>
            
            <p style="text-align: center; margin-top: 20px; color: #aaa;">
                Don't have an account? <a href="register.php" style="color: var(--metro-primary);">Register here</a>
            </p>
            
            <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #444;">
                <p style="color: #777; margin-bottom: 10px;">Admin Login:</p>
                <p style="color: #aaa; font-size: 0.9rem;">Username: admin<br>Password: admin123</p>
            </div>
        </div>
    </div>
</body>
</html>