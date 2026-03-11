<?php
// admin/dashboard.php
session_start();
include '../config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$conn = getConnection();

// Get statistics
$total_products = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$total_customers = $conn->query("SELECT COUNT(*) as count FROM customers")->fetch_assoc()['count'];
$total_revenue = $conn->query("SELECT SUM(total_amount) as total FROM orders")->fetch_assoc()['total'] ?: 0;

// Get recent orders
$recent_orders = $conn->query("
    SELECT o.*, c.name as customer_name, c.email 
    FROM orders o 
    JOIN customers c ON o.customer_id = c.id 
    ORDER BY o.order_date DESC 
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);

// Get low stock products
$low_stock = $conn->query("
    SELECT * FROM products 
    WHERE stock_quantity <= 10 
    ORDER BY stock_quantity ASC 
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Glamour Cosmetics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            transition: transform 0.2s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-card-2 {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .stats-card-3 {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .stats-card-4 {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
            </a>
            
            <div class="navbar-nav ms-auto">
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="../index.php" target="_blank">View Website</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="bg-white shadow-sm" style="min-height: calc(100vh - 56px);">
                    <div class="list-group list-group-flush">
                        <a href="dashboard.php" class="list-group-item list-group-item-action active">
                            <i class="fas fa-home me-2"></i>Dashboard
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-tags me-2"></i>Categories
                        </a>
                        <a href="products.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-box me-2"></i>Products
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-shopping-cart me-2"></i>Orders
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-users me-2"></i>Customers
                        </a>
                        <a href="reports.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-chart-bar me-2"></i>Reports
                        </a>
                        <a href="backup.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-database me-2"></i>Backup
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 px-4 py-4">
                <h1 class="mb-4">Dashboard Overview</h1>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card text-center py-3">
                            <div class="card-body">
                                <i class="fas fa-box fa-2x mb-2"></i>
                                <h3><?php echo $total_products; ?></h3>
                                <p class="mb-0">Total Products</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card stats-card-2 text-center py-3">
                            <div class="card-body">
                                <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                <h3><?php echo $total_orders; ?></h3>
                                <p class="mb-0">Total Orders</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card stats-card-3 text-center py-3">
                            <div class="card-body">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <h3><?php echo $total_customers; ?></h3>
                                <p class="mb-0">Total Customers</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card stats-card-4 text-center py-3">
                            <div class="card-body">
                                <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                                <h3>$<?php echo number_format($total_revenue, 2); ?></h3>
                                <p class="mb-0">Total Revenue</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Recent Orders -->
                    <div class="col-md-8 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Recent Orders</h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($recent_orders)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th>Customer</th>
                                                    <th>Amount</th>
                                                    <th>Date</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($recent_orders as $order): ?>
                                                    <tr>
                                                        <td>#<?php echo $order['id']; ?></td>
                                                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                                        <td><?php echo date('M j, Y', strtotime($order['order_date'])); ?></td>
                                                        <td>
                                                            <span class="badge bg-primary"><?php echo ucfirst($order['order_status']); ?></span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted text-center py-3">No orders yet</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Low Stock Alert -->
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2 text-warning"></i>Low Stock Alert</h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($low_stock)): ?>
                                    <?php foreach ($low_stock as $product): ?>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <strong><?php echo htmlspecialchars($product['name']); ?></strong><br>
                                                <small class="text-muted">Stock: <?php echo $product['stock_quantity']; ?></small>
                                            </div>
                                            <span class="badge bg-<?php echo $product['stock_quantity'] <= 5 ? 'danger' : 'warning'; ?>">
                                                <?php echo $product['stock_quantity']; ?>
                                            </span>
                                        </div>
                                        <hr class="my-2">
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted text-center">All products have sufficient stock</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-2 mb-3">
                                        <a href="products.php?action=add" class="btn btn-outline-primary btn-lg w-100">
                                            <i class="fas fa-plus fa-2x mb-2"></i><br>
                                            Add Product
                                        </a>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <a href="#" class="btn btn-outline-success btn-lg w-100">
                                            <i class="fas fa-tags fa-2x mb-2"></i><br>
                                            Add Category
                                        </a>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <a href="#" class="btn btn-outline-info btn-lg w-100">
                                            <i class="fas fa-list fa-2x mb-2"></i><br>
                                            View Orders
                                        </a>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <a href="#" class="btn btn-outline-warning btn-lg w-100">
                                            <i class="fas fa-users fa-2x mb-2"></i><br>
                                            View Customers
                                        </a>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <a href="reports.php" class="btn btn-outline-dark btn-lg w-100">
                                            <i class="fas fa-chart-line fa-2x mb-2"></i><br>
                                            Reports
                                        </a>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <a href="backup.php" class="btn btn-outline-secondary btn-lg w-100">
                                            <i class="fas fa-download fa-2x mb-2"></i><br>
                                            Backup
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>