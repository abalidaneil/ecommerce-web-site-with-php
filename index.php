<?php
require_once 'config.php';

// Fetch all products
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metro Store</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="metro-header">
        <div class="logo">
            <div class="logo-icon">M</div>
            <span>Metro Store</span>
        </div>
        <div class="nav-links">
            <?php if(isset($_SESSION['user_id'])): ?>
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="?logout" class="nav-button">Logout</a>
            <?php else: ?>
                <a href="login.php" class="nav-button">Login</a>
                <a href="register.php" class="nav-button accent">Register</a>
            <?php endif; ?>
            <!-- <a href="admin.php" class="nav-button">Admin</a> -->
        </div>
    </header>

    <main class="container">
        <div style="text-align: center; margin: 40px 0;">
            <h1 style="font-size: 2.5rem; margin-bottom: 10px;">Metro Store</h1>
            <p style="color: #aaa; font-size: 1.1rem;">Simple. Modern. Fast.</p>
        </div>

        <?php if(isset($_GET['order_success'])): ?>
            <div class="message success">
                <i class="fas fa-check-circle"></i> Order placed successfully! Check your email.
            </div>
        <?php endif; ?>

        <div class="metro-grid">
            <?php foreach($products as $product): ?>
                <div class="product-tile">
                    <div class="product-image">
                        <?php if($product['image_path'] && file_exists('uploads/' . $product['image_path'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <?php else: ?>
                            <div style="color: #666; font-size: 3rem;">
                                <i class="fas fa-box"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                        <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                        <a href="order.php?id=<?php echo $product['id']; ?>" class="btn btn-success">
                            <i class="fas fa-shopping-cart"></i> Order Now
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer class="metro-footer">
        <p>&copy; <?php echo date('Y'); ?> Metro Store. All rights reserved.</p>
        <p style="margin-top: 10px; font-size: 0.9rem;">Simple eCommerce with Metro Design</p>
    </footer>
</body>
</html>