<?php
// config.php
// Database configuration file

$db_servername = "localhost";
$db_username = "root";        // Changed variable name to avoid conflict
$db_password = "";           // Changed variable name to avoid conflict
$db_name = "jennys_cosmetics"; // Changed variable name to avoid conflict

// Create connection
function getConnection() {
    global $db_servername, $db_username, $db_password, $db_name;
    
    $conn = new mysqli($db_servername, $db_username, $db_password, $db_name);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Initialize shopping cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}
?>