<?php
// cart.php
include 'config.php';

// Handle cart updates
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $product_id => $quantity) {
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$product_id]);
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
    }
    $success_message = "Cart updated successfully!";
}

// Handle remove item
if (isset($_POST['remove_item'])) {
    $product_id = $_POST['product_id'];
    unset($_SESSION['cart'][$product_id]);
    $success_message = "Item removed from cart!";
}

// Handle clear cart
if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = array();
    $success_message = "Cart cleared!";
}

$conn = getConnection();
$cart_items = array();
$total = 0;

if (!empty($_SESSION['cart'])) {
    $product_ids = implode(',', array_keys($_SESSION['cart']));
    $sql = "SELECT * FROM products WHERE id IN ($product_ids)";
    $result = $conn->query($sql);
    
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

$cart_count = array_sum($_SESSION['cart']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Glamour Cosmetics & Jewelry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
   <style>
         :root {
            --primary-color: #e91e63;
            --secondary-color: #f8bbd9;
            --accent-color: #4a148c;
            --text-dark: #2c2c2c;
        }
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
          .footer {
            background: linear-gradient(135deg, #2c2c2c 0%, #1a1a1a 100%);
            color: white;
            padding: 60px 0 20px;
            margin-top: 80px;
        }

        .footer-section h5 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 0.5rem;
        }

        .footer-links a {
            color: #ccc;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--primary-color);
        }

        .social-icons {
            display: flex;
            gap: 1rem;
        }

        .social-icon {
            width: 45px;
            height: 45px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: transform 0.3s ease, background 0.3s ease;
        }

        .social-icon:hover {
            transform: scale(1.1);
            background: var(--accent-color);
            color: white;
        }

        .footer-bottom {
            border-top: 1px solid #444;
            padding-top: 2rem;
            margin-top: 3rem;
            text-align: center;
            color: #aaa;
        }

        .newsletter-form {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .newsletter-form input {
            flex: 1;
            padding: 0.75rem;
            border: none;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .newsletter-form input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .newsletter-form button {
            padding: 0.75rem 1.5rem;
            background: var(--primary-color);
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .newsletter-form button:hover {
            background: var(--accent-color);
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
                <a class="nav-link active" href="cart.php">Cart (<?php echo $cart_count; ?>)</a>
                <a class="nav-link" href="admin/login.php">Admin</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <h2>Shopping Cart</h2>

        <?php if (empty($cart_items)): ?>
            <div class="alert alert-info text-center">
                <h4>Your cart is empty</h4>
                <p>Start shopping to add items to your cart.</p>
                <a href="index.php" class="btn btn-primary">Browse Products</a>
            </div>
        <?php else: ?>
            <form method="POST">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($item['product']['name']); ?></strong><br>
                                        <small class="text-muted"><?php echo htmlspecialchars(substr($item['product']['description'], 0, 50)) . '...'; ?></small>
                                    </td>
                                    <td>$<?php echo number_format($item['product']['price'], 2); ?></td>
                                    <td>
                                        <input type="number" name="quantities[<?php echo $item['product']['id']; ?>]" 
                                               value="<?php echo $item['quantity']; ?>" min="0" max="<?php echo $item['product']['stock_quantity']; ?>" 
                                               class="form-control" style="width: 80px;">
                                    </td>
                                    <td>$<?php echo number_format($item['subtotal'], 2); ?></td>
                                    <td>
                                        <button type="submit" name="remove_item" value="<?php echo $item['product']['id']; ?>" 
                                                class="btn btn-sm btn-danger" onclick="return confirm('Remove this item?')">Remove</button>
                                        <input type="hidden" name="product_id" value="<?php echo $item['product']['id']; ?>">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-dark">
                                <th colspan="3">Total</th>
                                <th>$<?php echo number_format($total, 2); ?></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <button type="submit" name="update_cart" class="btn btn-secondary me-2">Update Cart</button>
                        <button type="submit" name="clear_cart" class="btn btn-outline-danger" onclick="return confirm('Clear entire cart?')">Clear Cart</button>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="index.php" class="btn btn-outline-primary me-2">Continue Shopping</a>
                        <a href="checkout.php" class="btn btn-primary btn-lg">Proceed to Checkout</a>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row g-4">
                <!-- Company Info -->
                <div class="col-lg-4 col-md-6">
                    <div class="footer-section">
                        <h5><i class="fas fa-gem me-2"></i>Glamour Cosmetics</h5>
                        <p class="mb-4 text-light">
                            Your trusted partner for premium cosmetics and elegant jewelry. 
                            Enhancing natural beauty since 2018.
                        </p>
                        <div class="social-icons">
                            <a href="#" class="social-icon">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="social-icon">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="social-icon">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="social-icon">
                                <i class="fab fa-pinterest"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6">
                    <div class="footer-section">
                        <h5>Quick Links</h5>
                        <ul class="footer-links">
                            <li><a href="index.php">Home</a></li>
                            <li><a href="about.php">About Us</a></li>
                            <li><a href="products.php">Products</a></li>
                            <li><a href="contact.php">Contact</a></li>
                            <li><a href="cart.php">Shopping Cart</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Categories -->
                <div class="col-lg-2 col-md-6">
                    <div class="footer-section">
                        <h5>Categories</h5>
                        <ul class="footer-links">
                            <li><a href="#">Skincare</a></li>
                            <li><a href="#">Makeup</a></li>
                            <li><a href="#">Fragrance</a></li>
                            <li><a href="#">Jewelry</a></li>
                            <li><a href="#">Accessories</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Newsletter -->
                <div class="col-lg-4 col-md-6">
                    <div class="footer-section">
                        <h5>Stay Updated</h5>
                        <p class="text-light mb-3">
                            Subscribe to our newsletter for exclusive offers and beauty tips!
                        </p>
                        <form class="newsletter-form">
                            <input type="email" placeholder="Enter your email" required>
                            <button type="submit">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                        <div class="mt-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                <span class="text-light">123 Beauty Street, Glamour City, GC 12345</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-phone me-2 text-primary"></i>
                                <span class="text-light">+1 (555) 123-4567</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-envelope me-2 text-primary"></i>
                                <span class="text-light">hello@Glamourscosmetics.com</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="mb-0">&copy; 2024 Glamour Cosmetics & Jewelry. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="#" class="text-decoration-none me-3" style="color: #aaa;">Privacy Policy</a>
                        <a href="#" class="text-decoration-none me-3" style="color: #aaa;">Terms of Service</a>
                        <a href="#" class="text-decoration-none" style="color: #aaa;">Return Policy</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Newsletter form submission
        document.querySelector('.newsletter-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input').value;
            if (email) {
                alert('Thank you for subscribing! You will receive our latest updates.');
                this.querySelector('input').value = '';
            }
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>