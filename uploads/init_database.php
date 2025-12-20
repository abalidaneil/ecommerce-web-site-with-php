<?php
$host = 'localhost';
$user = 'root';  
$pass = '';      

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS metro_store DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
    $pdo->exec("USE metro_store");

    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        image_path VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create admin user (username: admin, password: admin123)
    $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->exec("INSERT IGNORE INTO users (username, email, password)
                VALUES ('admin', 'admin@store.com', '$adminPass')");

    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    if ($stmt->fetchColumn() == 0) {
        $sampleProducts = [
            "INSERT INTO products (name, description, price, image_path) VALUES
            ('Metro Headphones', 'Premium wireless headphones with noise cancellation', 129.99, 'headphones.jpg')",
            "INSERT INTO products (name, description, price, image_path) VALUES
            ('Smart Watch Pro', 'Fitness tracker with heart rate monitor', 199.99, 'watch.jpg')",
            "INSERT INTO products (name, description, price, image_path) VALUES
            ('Gaming Keyboard', 'Mechanical keyboard with RGB lighting', 89.99, 'keyboard.jpg')",
            "INSERT INTO products (name, description, price, image_path) VALUES
            ('USB-C Hub', '7-in-1 multiport adapter for laptops', 49.99, 'hub.jpg')",
            "INSERT INTO products (name, description, price, image_path) VALUES
            ('Wireless Mouse', 'Ergonomic design with long battery life', 39.99, 'mouse.jpg')",
            "INSERT INTO products (name, description, price, image_path) VALUES
            ('Laptop Stand', 'Adjustable aluminum stand for better posture', 59.99, 'stand.jpg')",
            "INSERT INTO products (name, description, price, image_path) VALUES
            ('Phone Case', 'Durable case with metro design pattern', 24.99, 'case.jpg')",
            "INSERT INTO products (name, description, price, image_path) VALUES
            ('Bluetooth Speaker', 'Portable speaker with 12-hour battery', 79.99, 'speaker.jpg')"
        ];

        foreach ($sampleProducts as $sql) {
            $pdo->exec($sql);
        }
    }

    // echo "Database setup complete!<br>";
    // echo "Admin login: username=admin, password=admin123<br>";
    // echo "<a href='index.php'>Go to Store</a>";

} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
