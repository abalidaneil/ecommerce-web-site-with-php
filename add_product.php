<?php
require_once 'config.php';

// Check if user is admin
// if(!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
//     header('Location: admin.php');
//     exit();
// }

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $image = $_FILES['image'];
    
    // Validation
    if(empty($name) || empty($description) || $price <= 0) {
        $error = 'Please fill all fields correctly';
    } else {
        try {
            $imagePath = null;
            
            // Handle image upload
            if($image['error'] === 0) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $maxSize = 2 * 1024 * 1024; // 2MB
                
                if(!in_array($image['type'], $allowedTypes)) {
                    $error = 'Only JPG, PNG, GIF, and WebP images are allowed';
                } elseif($image['size'] > $maxSize) {
                    $error = 'Image size must be less than 2MB';
                } else {
                    // Create uploads directory if it doesn't exist
                    if(!is_dir('uploads')) {
                        mkdir('uploads', 0755, true);
                    }
                    
                    // Generate unique filename
                    $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
                    $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $name) . '.' . $extension;
                    $targetPath = 'uploads/' . $filename;
                    
                    if(move_uploaded_file($image['tmp_name'], $targetPath)) {
                        $imagePath = $filename;
                    } else {
                        $error = 'Failed to upload image';
                    }
                }
            }
            
            if(!$error) {
                // Insert product into database
                $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image_path) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $description, $price, $imagePath]);
                
                $success = 'Product added successfully!';
                // Clear form
                $_POST = array();
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
    <title>Add Product - Metro Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="metro-header">
        <div class="logo">
            <div class="logo-icon">M</div>
            <span>Add Product</span>
        </div>
        <div class="nav-links">
            <a href="admin_panel.php" class="nav-button">Admin Panel</a>
            <a href="index.php" class="nav-button">Store Front</a>
        </div>
    </header>

    <div class="container">
        <div class="form-container">
            <h2 class="form-title">Add New Product</h2>
            
            <?php if($error): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="message success">
                    <?php echo $success; ?>
                    <br>
                    <a href="admin_panel.php?added=1" style="color: white; text-decoration: underline;">
                        Back to Admin Panel
                    </a>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input type="text" id="name" name="name" class="form-control" 
                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="4" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="price">Price ($)</label>
                    <input type="number" id="price" name="price" class="form-control" 
                           step="0.01" min="0.01" 
                           value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="image">Product Image (Optional)</label>
                    <input type="file" id="image" name="image" class="form-control" accept="image/*">
                    <small style="color: #777; display: block; margin-top: 5px;">
                        Max size: 2MB. Formats: JPG, PNG, GIF, WebP
                    </small>
                </div>
                
                <button type="submit" class="btn btn-success">Add Product</button>
            </form>
        </div>
        
        <div style="margin-top: 30px; background: var(--metro-dark); padding: 20px; border-radius: 8px;">
            <h3 style="margin-bottom: 10px;">Tips:</h3>
            <ul style="color: #aaa; padding-left: 20px;">
                <li>Use clear, descriptive product names</li>
                <li>Images should be square or landscape orientation</li>
                <li>Recommended image size: 500x500 pixels</li>
                <li>You can add products without images initially</li>
            </ul>
        </div>
    </div>
</body>
</html>