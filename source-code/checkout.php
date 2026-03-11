<?php
// checkout.php
include 'config.php';

// Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

$conn = getConnection();
$errors = array();
$success_message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    // Validate form data
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);
    $work_phone = trim($_POST['work_phone']);
    $cell_phone = trim($_POST['cell_phone']);
    $date_of_birth = $_POST['date_of_birth'];
    $category = trim($_POST['category']);
    $remarks = trim($_POST['remarks']);
    
    // Validation
    if (empty($name)) $errors[] = "Name is required";
    if (empty($address)) $errors[] = "Address is required";
    if (empty($email)) $errors[] = "Email is required";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
    if (empty($cell_phone)) $errors[] = "Cell phone is required";
    
    if (empty($errors)) {
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Insert or update customer
            $stmt = $conn->prepare("SELECT id FROM customers WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                // Update existing customer
                $customer = $result->fetch_assoc();
                $customer_id = $customer['id'];
                
                $stmt = $conn->prepare("UPDATE customers SET name=?, address=?, work_phone=?, cell_phone=?, date_of_birth=?, category=?, remarks=? WHERE id=?");
                $stmt->bind_param("sssssssi", $name, $address, $work_phone, $cell_phone, $date_of_birth, $category, $remarks, $customer_id);
                $stmt->execute();
            } else {
                // Insert new customer
                $stmt = $conn->prepare("INSERT INTO customers (name, address, email, work_phone, cell_phone, date_of_birth, category, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssss", $name, $address, $email, $work_phone, $cell_phone, $date_of_birth, $category, $remarks);
                $stmt->execute();
                $customer_id = $conn->insert_id;
            }
            
            // Calculate total
            $product_ids = implode(',', array_keys($_SESSION['cart']));
            $products_result = $conn->query("SELECT * FROM products WHERE id IN ($product_ids)");
            
            $total = 0;
            $order_items = array();
            
            while ($product = $products_result->fetch_assoc()) {
                $quantity = $_SESSION['cart'][$product['id']];
                $subtotal = $product['price'] * $quantity;
                $total += $subtotal;
                
                $order_items[] = array(
                    'product_id' => $product['id'],
                    'quantity' => $quantity,
                    'price' => $product['price']
                );
                
                // Check stock availability
                if ($product['stock_quantity'] < $quantity) {
                    throw new Exception("Insufficient stock for " . $product['name']);
                }
            }
            
            // Insert order
            $stmt = $conn->prepare("INSERT INTO orders (customer_id, total_amount) VALUES (?, ?)");
            $stmt->bind_param("id", $customer_id, $total);
            $stmt->execute();
            $order_id = $conn->insert_id;
            
            // Insert order items and update stock
            foreach ($order_items as $item) {
                $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
                $stmt->execute();
                
                // Update product stock
                $stmt = $conn->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
                $stmt->bind_param("ii", $item['quantity'], $item['product_id']);
                $stmt->execute();
            }
            
            // Commit transaction
            $conn->commit();
            
            // Clear cart
            $_SESSION['cart'] = array();
            
            $success_message = "Order placed successfully! Order ID: #" . $order_id;
            
        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = "Error placing order: " . $e->getMessage();
        }
    }
}

// Get cart items for display
$cart_items = array();
$total = 0;

if (!empty($_SESSION['cart'])) {
    $product_ids = implode(',', array_keys($_SESSION['cart']));
    $result = $conn->query("SELECT * FROM products WHERE id IN ($product_ids)");
    
    while ($product = $result->fetch_assoc()) {
        $quantity = $_SESSION['cart'][$product['id']];
        $subtotal = $product['price'] * $quantity;
        $total += $subtotal;
        
        $cart_items[] = array(
            'product' => $product,
            'quantity' => $quantity,
            'subtotal' => $subtotal
        );
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Glamour Cosmetics & Jewelry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar-brand {
            font-weight: bold;
            color: #e91e63 !important;
        }
        .btn-primary {
            background-color: #e91e63;
            border-color: #e91e63;
        }
        .btn-primary:hover {
            background-color: #c2185b;
            border-color: #c2185b;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Glamour Cosmetics & Jewelry</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">Continue Shopping</a>
                <a class="nav-link" href="cart.php">Cart</a>
                <a class="nav-link" href="admin/login.php">Admin</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
                <h4>Order Successful!</h4>
                <p><?php echo $success_message; ?></p>
                <a href="index.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        <?php else: ?>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <h2>Checkout</h2>

            <div class="row">
                <div class="col-md-8">
                    <h4>Customer Information</h4>
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="name" name="name" required
                                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" required
                                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address *</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="work_phone" class="form-label">Work Phone</label>
                                    <input type="tel" class="form-control" id="work_phone" name="work_phone"
                                           value="<?php echo isset($_POST['work_phone']) ? htmlspecialchars($_POST['work_phone']) : ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cell_phone" class="form-label">Cell Phone *</label>
                                    <input type="tel" class="form-control" id="cell_phone" name="cell_phone" required
                                           value="<?php echo isset($_POST['cell_phone']) ? htmlspecialchars($_POST['cell_phone']) : ''; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                                           value="<?php echo isset($_POST['date_of_birth']) ? $_POST['date_of_birth'] : ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Customer Category</label>
                                    <select class="form-control" id="category" name="category">
                                        <option value="">Select Category</option>
                                        <option value="Regular" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Regular') ? 'selected' : ''; ?>>Regular</option>
                                        <option value="VIP" <?php echo (isset($_POST['category']) && $_POST['category'] == 'VIP') ? 'selected' : ''; ?>>VIP</option>
                                        <option value="Wholesale" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Wholesale') ? 'selected' : ''; ?>>Wholesale</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3"><?php echo isset($_POST['remarks']) ? htmlspecialchars($_POST['remarks']) : ''; ?></textarea>
                        </div>

                        <button type="submit" name="place_order" class="btn btn-primary btn-lg">Place Order</button>
                        <a href="cart.php" class="btn btn-secondary ms-2">Back to Cart</a>
                    </form>
                </div>

                <div class="col-md-4">
                    <h4>Order Summary</h4>
                    <div class="card">
                        <div class="card-body">
                            <?php foreach ($cart_items as $item): ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <div>
                                        <strong><?php echo htmlspecialchars($item['product']['name']); ?></strong><br>
                                        <small>Qty: <?php echo $item['quantity']; ?> × $<?php echo number_format($item['product']['price'], 2); ?></small>
                                    </div>
                                    <div>$<?php echo number_format($item['subtotal'], 2); ?></div>
                                </div>
                                <hr>
                            <?php endforeach; ?>
                            
                            <div class="d-flex justify-content-between">
                                <strong>Total: $<?php echo number_format($total, 2); ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>&copy; 2025 Glamour Cosmetics & Jewelry. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>