<?php
// admin/backup.php
session_start();
include '../config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$success_message = "";
$error_message = "";

// Handle backup creation
if (isset($_POST['create_backup'])) {
    $conn = getConnection();
    
    $tables = ['categories', 'products', 'customers', 'orders', 'order_items', 'admin'];
    
    $backup_content = "-- Glamour Cosmetics Database Backup\n";
    $backup_content .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n\n";
    
    try {
        foreach ($tables as $table) {
            // Get table structure
            $result = $conn->query("SHOW CREATE TABLE `$table`");
            $row = $result->fetch_assoc();
            $backup_content .= "-- Table structure for `$table`\n";
            $backup_content .= "DROP TABLE IF EXISTS `$table`;\n";
            $backup_content .= $row['Create Table'] . ";\n\n";
            
            // Get table data
            $result = $conn->query("SELECT * FROM `$table`");
            if ($result->num_rows > 0) {
                $backup_content .= "-- Dumping data for table `$table`\n";
                $backup_content .= "INSERT INTO `$table` VALUES ";
                
                $first = true;
                while ($row = $result->fetch_assoc()) {
                    if (!$first) {
                        $backup_content .= ",";
                    }
                    $backup_content .= "\n(";
                    $values = array();
                    foreach ($row as $value) {
                        if ($value === null) {
                            $values[] = 'NULL';
                        } else {
                            $values[] = "'" . $conn->real_escape_string($value) . "'";
                        }
                    }
                    $backup_content .= implode(',', $values);
                    $backup_content .= ")";
                    $first = false;
                }
                $backup_content .= ";\n\n";
            }
        }
        
        $filename = "Glamour_cosmetics_backup_" . date('Y-m-d_H-i-s') . ".sql";
        
        // Create backups directory if it doesn't exist
        $backup_dir = "../backups/";
        if (!file_exists($backup_dir)) {
            mkdir($backup_dir, 0777, true);
        }
        
        $filepath = $backup_dir . $filename;
        
        if (file_put_contents($filepath, $backup_content)) {
            $success_message = "Backup created successfully: " . $filename;
        } else {
            $error_message = "Failed to create backup file.";
        }
        
    } catch (Exception $e) {
        $error_message = "Error creating backup: " . $e->getMessage();
    }
    
    $conn->close();
}

// Handle backup download
if (isset($_GET['download'])) {
    $filename = basename($_GET['download']);
    $filepath = "../backups/" . $filename;
    
    if (file_exists($filepath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        
        readfile($filepath);
        exit();
    }
}

// Handle backup deletion
if (isset($_POST['delete_backup'])) {
    $filename = basename($_POST['filename']);
    $filepath = "../backups/" . $filename;
    
    if (file_exists($filepath)) {
        if (unlink($filepath)) {
            $success_message = "Backup deleted successfully.";
        } else {
            $error_message = "Failed to delete backup.";
        }
    }
}

// Get existing backups
$backup_files = array();
$backup_dir = "../backups/";
if (file_exists($backup_dir)) {
    $files = scandir($backup_dir);
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
            $backup_files[] = array(
                'name' => $file,
                'size' => filesize($backup_dir . $file),
                'date' => date('Y-m-d H:i:s', filemtime($backup_dir . $file))
            );
        }
    }
    // Sort by date, newest first
    usort($backup_files, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Backup - Admin</title>
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
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-users me-2"></i>Customers
                            
                        </a>
                        <a href="reports.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-chart-bar me-2"></i>Reports
                        </a>
                        <a href="backup.php" class="list-group-item list-group-item-action active">
                            <i class="fas fa-database me-2"></i>Backup
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 px-4 py-4">
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?php echo $success_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?php echo $error_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-database me-2"></i>Database Backup</h1>
                </div>

                <!-- Backup Information -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5><i class="fas fa-info-circle me-2 text-info"></i>About Database Backup</h5>
                                <p class="mb-3">Regular database backups are essential for protecting your business data. 
                                   This tool creates a complete backup of all your store data including:</p>
                                <ul>
                                    <li>Product catalog and categories</li>
                                    <li>Customer information</li>
                                    <li>Order history and details</li>
                                    <li>Admin account settings</li>
                                </ul>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Important:</strong> Store backup files in a secure location and test them regularly.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                                <h5>Create New Backup</h5>
                                <p class="text-muted">Generate a complete database backup</p>
                                <form method="POST">
                                    <button type="submit" name="create_backup" class="btn btn-primary btn-lg" 
                                            onclick="return confirm('Create a new database backup?')">
                                        <i class="fas fa-download me-2"></i>Create Backup
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Existing Backups -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Backup History</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($backup_files)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Filename</th>
                                            <th>Size</th>
                                            <th>Created Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($backup_files as $backup): ?>
                                            <tr>
                                                <td>
                                                    <i class="fas fa-file-alt me-2 text-muted"></i>
                                                    <?php echo htmlspecialchars($backup['name']); ?>
                                                </td>
                                                <td><?php echo number_format($backup['size'] / 1024, 2); ?> KB</td>
                                                <td><?php echo $backup['date']; ?></td>
                                                <td>
                                                    <a href="?download=<?php echo urlencode($backup['name']); ?>" 
                                                       class="btn btn-sm btn-outline-success me-1" title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <form method="POST" style="display: inline;" 
                                                          onsubmit="return confirm('Delete this backup file?')">
                                                        <input type="hidden" name="filename" value="<?php echo htmlspecialchars($backup['name']); ?>">
                                                        <button type="submit" name="delete_backup" 
                                                                class="btn btn-sm btn-outline-danger" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No backups found</h5>
                                <p class="text-muted">Create your first backup to get started.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Backup Best Practices -->
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-lightbulb me-2 text-warning"></i>Backup Best Practices</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="fas fa-clock me-2"></i>Frequency</h6>
                                <ul>
                                    <li>Create backups daily during business hours</li>
                                    <li>Always backup before major updates</li>
                                    <li>Keep at least 7 days of backup history</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-lock me-2"></i>Security</h6>
                                <ul>
                                    <li>Store backups in multiple secure locations</li>
                                    <li>Test backup restoration regularly</li>
                                    <li>Keep backups encrypted when possible</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Restoration Note:</strong> To restore from a backup, import the SQL file into your database using phpMyAdmin or MySQL command line tools.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
