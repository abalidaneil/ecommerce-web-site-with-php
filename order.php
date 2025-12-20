<?php
require_once 'config.php';

// Get product ID
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($product_id <= 0) {
    header('Location: index.php');
    exit();
}

// Fetch product details
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$product) {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        try {
            // Store order in database (optional - you can skip if only email needed)
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, product_id, order_date) VALUES (?, ?, NOW())");
            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
            $stmt->execute([$user_id, $product_id]);
            
            // Send email
            if(sendOrderEmail($email, $product['name'])) {
                $success = 'Order placed successfully! Check your email for confirmation.';
                // Redirect to home with success message
                header('Location: index.php?order_success=1');
                exit();
            } else {
                $error = 'Failed to send email. Please try again.';
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
    <title>Order - <?php echo htmlspecialchars($product['name']); ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="metro-header">
        <div class="logo">
            <div class="logo-icon">M</div>
            <span>Place Order</span>
        </div>
        <div class="nav-links">
            <a href="index.php" class="nav-button">Continue Shopping</a>
        </div>
    </header>

    <div class="container">
        <div style="display: flex; flex-wrap: wrap; gap: 30px; margin-top: 30px;">
            <!-- Product Summary -->
            <div style="flex: 1; min-width: 300px; background: var(--metro-dark); padding: 30px; border-radius: 8px;">
                <h2 style="margin-bottom: 20px; color: white;">Order Summary</h2>
                
                <div style="display: flex; gap: 20px; margin-bottom: 30px;">
                    <div style="width: 100px; height: 100px; background: #2a2a2a; border-radius: 4px; overflow: hidden;">
                        <?php if($product['image_path'] && file_exists('uploads/' . $product['image_path'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($product['image_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #666;">
                                <i class="fas fa-box" style="font-size: 2rem;"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <h3 style="color: white; margin-bottom: 10px;"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p style="color: #aaa; margin-bottom: 10px; font-size: 0.9rem;">
                            <?php echo substr(htmlspecialchars($product['description']), 0, 100); ?>...
                        </p>
                        <div style="color: var(--metro-success); font-size: 1.5rem; font-weight: bold;">
                            $<?php echo number_format($product['price'], 2); ?>
                        </div>
                    </div>
                </div>
                
                <div style="background: #2a2a2a; padding: 15px; border-radius: 4px; margin-top: 20px;">
                    <h4 style="color: white; margin-bottom: 10px;">Order Process:</h4>
                    <ol style="color: #aaa; padding-left: 20px;">
                        <li>Enter your email address</li>
                        <li>Click "Place Order"</li>
                        <li>Check your email for confirmation</li>
                        <li>No payment required (demo system)</li>
                    </ol>
                </div>
            </div>
            
            <!-- Order Form -->
            <div style="flex: 1; min-width: 300px;">
                <div class="form-container">
                    <h2 class="form-title">Complete Order</h2>
                    
                    <?php if($error): ?>
                        <div class="message error"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if($success): ?>
                        <div class="message success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="email">Your Email Address</label>
                            <input type="email" id="email" name="email" class="form-control" 
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : (isset($_SESSION['email']) ? $_SESSION['email'] : ''); ?>" 
                                   required placeholder="you@example.com">
                            <small style="color: #777; display: block; margin-top: 5px;">
                                We'll send the product name to this email
                            </small>
                        </div>
                        
                        <div style="background: rgba(16, 124, 16, 0.1); padding: 15px; border-radius: 4px; margin: 20px 0; border: 1px solid var(--metro-success);">
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                                <i class="fas fa-info-circle" style="color: var(--metro-success);"></i>
                                <span style="color: #90EE90;">Demo Order System</span>
                            </div>
                            <p style="color: #aaa; font-size: 0.9rem;">
                                This is a demo system. No real purchase will be made. 
                                The product name will be sent to your email for demonstration purposes.
                            </p>
                        </div>
                        
                        <button type="submit" class="btn btn-success" style="font-size: 1.1rem;">
                            <i class="fas fa-check-circle"></i> Place Order
                        </button>
                    </form>
                    
                    <p style="text-align: center; margin-top: 20px; color: #aaa;">
                        <a href="index.php" style="color: var(--metro-primary);">
                            <i class="fas fa-arrow-left"></i> Back to Store
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>