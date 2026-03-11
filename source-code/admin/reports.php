<?php
// admin/reports.php
session_start();
include '../config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$conn = getConnection();

// Get top 10 best selling products
$top_products_query = "
    SELECT p.name, p.price, SUM(oi.quantity) as total_sold, SUM(oi.quantity * oi.price) as total_revenue
    FROM products p
    JOIN order_items oi ON p.id = oi.product_id
    JOIN orders o ON oi.order_id = o.id
    GROUP BY p.id, p.name, p.price
    ORDER BY total_sold DESC
    LIMIT 10
";
$top_products = $conn->query($top_products_query)->fetch_all(MYSQLI_ASSOC);

// Get top 10 customers by spending
$top_customers_query = "
    SELECT c.name, c.email, COUNT(o.id) as total_orders, SUM(o.total_amount) as total_spent
    FROM customers c
    JOIN orders o ON c.id = o.customer_id
    GROUP BY c.id, c.name, c.email
    ORDER BY total_spent DESC
    LIMIT 10
";
$top_customers = $conn->query($top_customers_query)->fetch_all(MYSQLI_ASSOC);

// Get monthly sales data
$monthly_sales_query = "
    SELECT 
        DATE_FORMAT(order_date, '%Y-%m') as month,
        COUNT(*) as total_orders,
        SUM(total_amount) as total_revenue
    FROM orders
    WHERE order_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(order_date, '%Y-%m')
    ORDER BY month DESC
    LIMIT 12
";
$monthly_sales = $conn->query($monthly_sales_query)->fetch_all(MYSQLI_ASSOC);

// Get category-wise sales
$category_sales_query = "
    SELECT 
        c.name as category_name,
        COUNT(oi.id) as items_sold,
        SUM(oi.quantity * oi.price) as revenue
    FROM categories c
    JOIN products p ON c.id = p.category_id
    JOIN order_items oi ON p.id = oi.product_id
    JOIN orders o ON oi.order_id = o.id
    GROUP BY c.id, c.name
    ORDER BY revenue DESC
";
$category_sales = $conn->query($category_sales_query)->fetch_all(MYSQLI_ASSOC);

// Get overall statistics
$stats_query = "
    SELECT 
        (SELECT COUNT(*) FROM products) as total_products,
        (SELECT COUNT(*) FROM orders) as total_orders,
        (SELECT COUNT(*) FROM customers) as total_customers,
        (SELECT SUM(total_amount) FROM orders) as total_revenue,
        (SELECT AVG(total_amount) FROM orders) as avg_order_value
";
$stats = $conn->query($stats_query)->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
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
        .stats-card-5 {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
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
                        <a href="dashboard.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-home me-2"></i>Dashboard
                        </a>
                        <a href="categories.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-tags me-2"></i>Categories
                        </a>
                        <a href="products.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-box me-2"></i>Products
                        </a>
                        <a href="orders.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-shopping-cart me-2"></i>Orders
                        </a>
                        <a href="customers.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-users me-2"></i>Customers
                        </a>
                        <a href="reports.php" class="list-group-item list-group-item-action active">
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-chart-line me-2"></i>Business Reports</h1>
                    <button onclick="window.print()" class="btn btn-outline-primary">
                        <i class="fas fa-print me-1"></i>Print Report
                    </button>
                </div>

                <!-- Overall Statistics -->
                <div class="row mb-4">
                    <div class="col-md-2 col-sm-4 mb-3">
                        <div class="card stats-card text-center py-3">
                            <div class="card-body">
                                <i class="fas fa-box fa-2x mb-2"></i>
                                <h4><?php echo number_format($stats['total_customers']); ?></h4>
                                <small>Customers</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card stats-card stats-card-4 text-center py-3">
                            <div class="card-body">
                                <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                                <h4>$<?php echo number_format($stats['total_revenue'], 2); ?></h4>
                                <small>Total Revenue</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card stats-card stats-card-5 text-center py-3">
                            <div class="card-body">
                                <i class="fas fa-chart-line fa-2x mb-2"></i>
                                <h4>$<?php echo number_format($stats['avg_order_value'], 2); ?></h4>
                                <small>Avg Order Value</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Top 10 Best Selling Products -->
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-trophy me-2"></i>Top 10 Best Selling Products</h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($top_products)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Rank</th>
                                                    <th>Product Name</th>
                                                    <th>Units Sold</th>
                                                    <th>Revenue</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($top_products as $index => $product): ?>
                                                    <tr>
                                                        <td>
                                                            <?php if ($index < 3): ?>
                                                                <i class="fas fa-medal text-<?php echo ['warning', 'secondary', 'warning'][$index]; ?>"></i>
                                                            <?php else: ?>
                                                                <?php echo $index + 1; ?>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                                                        <td><?php echo number_format($product['total_sold']); ?></td>
                                                        <td>$<?php echo number_format($product['total_revenue'], 2); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted text-center py-3">No sales data available</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Top 10 Customers -->
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Top 10 Customers</h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($top_customers)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Rank</th>
                                                    <th>Customer</th>
                                                    <th>Orders</th>
                                                    <th>Total Spent</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($top_customers as $index => $customer): ?>
                                                    <tr>
                                                        <td>
                                                            <?php if ($index < 3): ?>
                                                                <i class="fas fa-star text-<?php echo ['warning', 'secondary', 'warning'][$index]; ?>"></i>
                                                            <?php else: ?>
                                                                <?php echo $index + 1; ?>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($customer['name']); ?></strong><br>
                                                            <small class="text-muted"><?php echo htmlspecialchars($customer['email']); ?></small>
                                                        </td>
                                                        <td><?php echo number_format($customer['total_orders']); ?></td>
                                                        <td>$<?php echo number_format($customer['total_spent'], 2); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted text-center py-3">No customer data available</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Monthly Sales Chart -->
                    <div class="col-md-8 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-chart-area me-2"></i>Monthly Sales Trend</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="monthlySalesChart" style="max-height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Category Sales -->
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Category Performance</h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($category_sales)): ?>
                                    <?php foreach ($category_sales as $category): ?>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between">
                                                <strong><?php echo htmlspecialchars($category['category_name']); ?></strong>
                                                <span>$<?php echo number_format($category['revenue'], 2); ?></span>
                                            </div>
                                            <small class="text-muted"><?php echo number_format($category['items_sold']); ?> items sold</small>
                                        </div>
                                        <hr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted text-center">No category data available</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sales Summary -->
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-header bg-dark text-white">
                                <h5 class="mb-0"><i class="fas fa-calendar me-2"></i>Monthly Sales Summary</h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($monthly_sales)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Month</th>
                                                    <th>Total Orders</th>
                                                    <th>Total Revenue</th>
                                                    <th>Average Order Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($monthly_sales as $month_data): ?>
                                                    <tr>
                                                        <td><?php echo date('F Y', strtotime($month_data['month'] . '-01')); ?></td>
                                                        <td><?php echo number_format($month_data['total_orders']); ?></td>
                                                        <td>$<?php echo number_format($month_data['total_revenue'], 2); ?></td>
                                                        <td>$<?php echo number_format($month_data['total_revenue'] / $month_data['total_orders'], 2); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted text-center py-4">No sales data available for the past 12 months</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Export Options -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <h5><i class="fas fa-download me-2"></i>Export Reports</h5>
                                <p class="text-muted">Download detailed reports for further analysis</p>
                                <button onclick="exportToCSV()" class="btn btn-outline-success me-2">
                                    <i class="fas fa-file-csv me-1"></i>Export to CSV
                                </button>
                                <button onclick="window.print()" class="btn btn-outline-primary">
                                    <i class="fas fa-print me-1"></i>Print Report
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Monthly Sales Chart
        <?php if (!empty($monthly_sales)): ?>
        const monthlyData = <?php echo json_encode(array_reverse($monthly_sales)); ?>;
        const ctx = document.getElementById('monthlySalesChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: monthlyData.map(data => {
                    const date = new Date(data.month + '-01');
                    return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
                }),
                datasets: [{
                    label: 'Revenue ($)',
                    data: monthlyData.map(data => parseFloat(data.total_revenue)),
                    borderColor: '#e91e63',
                    backgroundColor: 'rgba(233, 30, 99, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Orders',
                    data: monthlyData.map(data => parseInt(data.total_orders)),
                    borderColor: '#2196F3',
                    backgroundColor: 'rgba(33, 150, 243, 0.1)',
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Monthly Sales Performance'
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Revenue ($)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Number of Orders'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                }
            }
        });
        <?php endif; ?>

        function exportToCSV() {
            // Create CSV content
            let csv = 'Report Type,Data\n';
            csv += '"Top Products Report",\n';
            csv += 'Rank,Product Name,Units Sold,Revenue\n';
            
            <?php if (!empty($top_products)): ?>
            <?php foreach ($top_products as $index => $product): ?>
            csv += '<?php echo $index + 1; ?>,"<?php echo addslashes($product['name']); ?>",<?php echo $product['total_sold']; ?>,<?php echo $product['total_revenue']; ?>\n';
            <?php endforeach; ?>
            <?php endif; ?>

            csv += '\n"Top Customers Report",\n';
            csv += 'Rank,Customer Name,Email,Orders,Total Spent\n';
            
            <?php if (!empty($top_customers)): ?>
            <?php foreach ($top_customers as $index => $customer): ?>
            csv += '<?php echo $index + 1; ?>,"<?php echo addslashes($customer['name']); ?>","<?php echo addslashes($customer['email']); ?>",<?php echo $customer['total_orders']; ?>,<?php echo $customer['total_spent']; ?>\n';
            <?php endforeach; ?>
            <?php endif; ?>

            // Download CSV
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = 'business_report_' + new Date().toISOString().split('T')[0] + '.csv';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>