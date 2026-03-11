<?php
// database_setup.php
// Run this file once to create the database and tables

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "jennys_cosmetics";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select database
$conn->select_db($dbname);

// Create categories table
$sql = "CREATE TABLE IF NOT EXISTS categories (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Categories table created successfully<br>";
} else {
    echo "Error creating categories table: " . $conn->error . "<br>";
}

// Create products table
$sql = "CREATE TABLE IF NOT EXISTS products (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category_id INT(11),
    image VARCHAR(255),
    stock_quantity INT(11) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Products table created successfully<br>";
} else {
    echo "Error creating products table: " . $conn->error . "<br>";
}

// Create customers table
$sql = "CREATE TABLE IF NOT EXISTS customers (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    email VARCHAR(100) NOT NULL,
    work_phone VARCHAR(20),
    cell_phone VARCHAR(20),
    date_of_birth DATE,
    category VARCHAR(50),
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Customers table created successfully<br>";
} else {
    echo "Error creating customers table: " . $conn->error . "<br>";
}

// Create orders table
$sql = "CREATE TABLE IF NOT EXISTS orders (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    customer_id INT(11),
    total_amount DECIMAL(10,2) NOT NULL,
    order_status VARCHAR(50) DEFAULT 'pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Orders table created successfully<br>";
} else {
    echo "Error creating orders table: " . $conn->error . "<br>";
}

// Create order_items table
$sql = "CREATE TABLE IF NOT EXISTS order_items (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    order_id INT(11),
    product_id INT(11),
    quantity INT(11) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Order items table created successfully<br>";
} else {
    echo "Error creating order items table: " . $conn->error . "<br>";
}

// Create admin table
$sql = "CREATE TABLE IF NOT EXISTS admin (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Admin table created successfully<br>";
} else {
    echo "Error creating admin table: " . $conn->error . "<br>";
}

// Insert default admin user (username: admin, password: admin123)
$admin_password = password_hash('admin123', PASSWORD_DEFAULT);
$sql = "INSERT IGNORE INTO admin (username, password) VALUES ('admin', '$admin_password')";
if ($conn->query($sql) === TRUE) {
    echo "Default admin user created (username: admin, password: admin123)<br>";
}

// Insert sample categories
$categories = [
    ['Cosmetics', 'Beauty and makeup products'],
    ['Lipsticks', 'Various lipstick shades and brands'],
    ['Foundation', 'Face foundation and base makeup'],
    ['Eye Makeup', 'Eye shadows, mascara, eyeliners'],
    ['Jewelry', 'Imitation jewelry and accessories'],
    ['Necklaces', 'Beautiful necklace collections'],
    ['Earrings', 'Stylish earring designs'],
    ['Bracelets', 'Trendy bracelet collections']
];

foreach ($categories as $category) {
    $name = $category[0];
    $desc = $category[1];
    $sql = "INSERT IGNORE INTO categories (name, description) VALUES ('$name', '$desc')";
    $conn->query($sql);
}

// Insert sample products
$products = [
    ['Matte Lipstick Red', 'Long-lasting matte red lipstick', 15.99, 2, 50],
    ['Foundation Shade 01', 'Natural coverage foundation', 25.99, 3, 30],
    ['Eyeshadow Palette', 'Professional 12-color eyeshadow palette', 35.99, 4, 20],
    ['Gold Chain Necklace', 'Elegant gold-plated chain necklace', 12.99, 6, 25],
    ['Pearl Earrings', 'Classic white pearl stud earrings', 8.99, 7, 40],
    ['Silver Bracelet', 'Stylish silver-tone bracelet', 18.99, 8, 35]
];

foreach ($products as $product) {
    $name = $product[0];
    $desc = $product[1];
    $price = $product[2];
    $cat_id = $product[3];
    $stock = $product[4];
    $sql = "INSERT IGNORE INTO products (name, description, price, category_id, stock_quantity) VALUES ('$name', '$desc', $price, $cat_id, $stock)";
    $conn->query($sql);
}

echo "<br>Database setup completed successfully!<br>";
echo "You can now access the website.<br>";
echo "Admin login: username = admin, password = admin123";

$conn->close();
?>