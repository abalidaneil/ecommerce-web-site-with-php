<?php
require_once 'config.php';

// Check if user is admin
// if(!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
//     header('Location: admin.php');
//     exit();
// }

// Handle logout
if(isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}

// Fetch all products
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle product deletion
if(isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: admin_panel.php?deleted=1');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Metro Store</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="metro-header">
        <div class="logo">
            <div class="logo-icon">M</div>
            <span>Admin Panel</span>
        </div>
        <div class="nav-links">
            <span style="color: #FFA500;">
                <i class="fas fa-user-shield"></i> Admin: <?php echo htmlspecialchars($_SESSION['username']); ?>
            </span>
            <a href="add_product.php" class="nav-button accent">
                <i class="fas fa-plus"></i> Add Product
            </a>
            <a href="index.php" class="nav-button">Store Front</a>
            <a href="?logout" class="nav-button">Logout</a>
        </div>
    </header>

    <main class="container">
        <?php if(isset($_GET['deleted'])): ?>
            <div class="message success">
                <i class="fas fa-trash-alt"></i> Product deleted successfully!
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['added'])): ?>
            <div class="message success">
                <i class="fas fa-check-circle"></i> Product added successfully!
            </div>
        <?php endif; ?>
        
        <div class="admin-panel">
            <h2 style="margin-bottom: 20px; color: white;">
                <i class="fas fa-cogs"></i> Product Management
            </h2>
            
            <div style="background: #2a2a2a; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                <h3 style="margin-bottom: 10px;">Products in Store: <?php echo count($products); ?></h3>
                <a href="add_product.php" class="btn" style="width: auto; display: inline-block;">
                    <i class="fas fa-plus"></i> Add New Product
                </a>
            </div>
            
            <div class="product-list">
                <?php foreach($products as $product): ?>
                    <div class="product-item">
                        <div style="flex: 1;">
                            <h4 style="color: white;"><?php echo htmlspecialchars($product['name']); ?></h4>
                            <p style="color: #aaa; font-size: 0.9rem; margin-top: 5px;">
                                $<?php echo number_format($product['price'], 2); ?> | 
                                <?php echo $product['image_path'] ? 'Has Image' : 'No Image'; ?>
                            </p>
                        </div>
                        <div class="product-actions">
                            <a href="edit_product.php?id=<?php echo $product['id']; ?>" 
                               class="nav-button" style="padding: 8px 15px; font-size: 0.9rem;">
                               <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="?delete=<?php echo $product['id']; ?>" 
                               class="nav-button accent" 
                               style="padding: 8px 15px; font-size: 0.9rem;"
                               onclick="return confirm('Delete this product?')">
                               <i class="fas fa-trash"></i> Delete
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div style="margin-top: 30px; background: var(--metro-dark); padding: 20px; border-radius: 8px;">
            <h3 style="margin-bottom: 15px;">
                <i class="fas fa-chart-bar"></i> Quick Stats
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <div style="background: #2a2a2a; padding: 15px; border-radius: 4px;">
                    <p style="color: #aaa; font-size: 0.9rem;">Total Products</p>
                    <p style="font-size: 1.5rem; color: var(--metro-primary);"><?php echo count($products); ?></p>
                </div>
                <div style="background: #2a2a2a; padding: 15px; border-radius: 4px;">
                    <p style="color: #aaa; font-size: 0.9rem;">Store Status</p>
                    <p style="font-size: 1.5rem; color: var(--metro-success);">Online</p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>