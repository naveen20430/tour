<?php
require_once '../config/config.php';
requireLogin();

// Get real statistics from database (with error handling)
try {
    $totalTours = $db->fetch("SELECT COUNT(*) as count FROM tours")['count'] ?? 0;
} catch (Exception $e) {
    $totalTours = 0;
}

try {
    $activeTours = $db->fetch("SELECT COUNT(*) as count FROM tours WHERE status = 'active'")['count'] ?? 0;
} catch (Exception $e) {
    $activeTours = 0;
}

try {
    $totalDestinations = $db->fetch("SELECT COUNT(*) as count FROM destinations")['count'] ?? 0;
} catch (Exception $e) {
    $totalDestinations = 0;
}

try {
    $totalBookings = $db->fetch("SELECT COUNT(*) as count FROM bookings")['count'] ?? 0;
} catch (Exception $e) {
    $totalBookings = 0;
}

try {
    $totalUsers = $db->fetch("SELECT COUNT(*) as count FROM users")['count'] ?? 0;
} catch (Exception $e) {
    $totalUsers = 0;
}

try {
    $totalBlogs = $db->fetch("SELECT COUNT(*) as count FROM blog_posts")['count'] ?? 0;
} catch (Exception $e) {
    $totalBlogs = 0;
}

try {
    $featuredTours = $db->fetch("SELECT COUNT(*) as count FROM tours WHERE featured = 1")['count'] ?? 0;
} catch (Exception $e) {
    $featuredTours = 0;
}

try {
    $recentBookings = $db->fetch("SELECT COUNT(*) as count FROM bookings WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")['count'] ?? 0;
} catch (Exception $e) {
    $recentBookings = 0;
}

// Get recent activity
try {
    $recentTours = $db->fetchAll("SELECT title, created_at FROM tours ORDER BY created_at DESC LIMIT 5");
} catch (Exception $e) {
    $recentTours = [];
}

try {
    $recentBookings = $db->fetchAll("SELECT b.*, t.title as tour_title FROM bookings b LEFT JOIN tours t ON b.tour_id = t.id ORDER BY b.created_at DESC LIMIT 5");
} catch (Exception $e) {
    $recentBookingsData = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo getSetting('site_name'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar { background: #2c3e50; min-height: 100vh; }
        .sidebar .nav-link { color: #bdc3c7; padding: 15px 20px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background: #34495e; }
        .main-content { background: #ecf0f1; min-height: 100vh; }
        .stats-card { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            border-radius: 15px; 
            transition: transform 0.2s;
            border: none;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .navbar-brand { font-weight: bold; }
        .card { 
            border: none; 
            border-radius: 15px; 
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        }
        .btn { border-radius: 10px; }
        .list-group-item { 
            border: none; 
            border-radius: 8px !important; 
            margin-bottom: 2px;
        }
        .list-group-item:hover {
            background: #f8f9fa;
            transform: translateX(5px);
            transition: all 0.2s;
        }
        .badge { border-radius: 20px; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 px-0 sidebar">
                <div class="p-3 text-center">
                    <h4 class="text-white"><?php echo getSetting('site_name'); ?></h4>
                    <small class="text-muted">Admin Panel</small>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link active" href="index.php">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                    <a class="nav-link" href="tours.php">
                        <i class="fas fa-map-marked-alt me-2"></i> Tours
                    </a>
                    <a class="nav-link" href="destinations.php">
                        <i class="fas fa-globe me-2"></i> Destinations
                    </a>
                    <a class="nav-link" href="bookings.php">
                        <i class="fas fa-calendar-check me-2"></i> Bookings
                    </a>
                    <a class="nav-link" href="cab_pricing.php">
                        <i class="fas fa-car me-2"></i> Cab Pricing
                    </a>
                    <a class="nav-link" href="blog.php">
                        <i class="fas fa-blog me-2"></i> Blog Posts
                    </a>
                    <a class="nav-link" href="users.php">
                        <i class="fas fa-users me-2"></i> Users
                    </a>
                    <a class="nav-link" href="hero-images.php">
                        <i class="fas fa-image me-2"></i> Hero Images
                    </a>
                    <a class="nav-link" href="settings.php">
                        <i class="fas fa-cog me-2"></i> Settings
                    </a>
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <!-- Header -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                    <div class="container-fluid">
                        <span class="navbar-brand mb-0 h1">Dashboard</span>
                        <div class="navbar-nav ms-auto">
                            <span class="nav-link">Welcome, <?php echo $_SESSION['admin_name']; ?>!</span>
                        </div>
                    </div>
                </nav>
                
                <!-- Dashboard Content -->
                <div class="p-4">
                    <!-- Main Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-map-marked-alt fa-3x mb-3"></i>
                                    <h3><?php echo $totalTours; ?></h3>
                                    <p>Total Tours</p>
                                    <small><?php echo $activeTours; ?> Active</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-globe fa-3x mb-3"></i>
                                    <h3><?php echo $totalDestinations; ?></h3>
                                    <p>Destinations</p>
                                    <small><?php echo $featuredTours; ?> Featured Tours</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-calendar-check fa-3x mb-3"></i>
                                    <h3><?php echo $totalBookings; ?></h3>
                                    <p>Total Bookings</p>
                                    <small><?php echo $recentBookings; ?> This Week</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-blog fa-3x mb-3"></i>
                                    <h3><?php echo $totalBlogs; ?></h3>
                                    <p>Blog Posts</p>
                                    <small><?php echo $totalUsers; ?> Users</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-rocket me-2"></i>Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <a href="tour-add.php" class="btn btn-primary w-100">
                                                <i class="fas fa-plus me-2"></i>Add New Tour
                                            </a>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <a href="destinations.php" class="btn btn-success w-100">
                                                <i class="fas fa-globe me-2"></i>Manage Destinations
                                            </a>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <a href="blog.php" class="btn btn-info w-100">
                                                <i class="fas fa-blog me-2"></i>Manage Blog
                                            </a>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <a href="tours.php" class="btn btn-warning w-100">
                                                <i class="fas fa-map-marked-alt me-2"></i>All Tours
                                            </a>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <a href="bookings.php" class="btn btn-danger w-100">
                                                <i class="fas fa-calendar-check me-2"></i>View Bookings
                                            </a>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <a href="cab_pricing.php" class="btn btn-dark w-100">
                                                <i class="fas fa-car me-2"></i>Cab Pricing
                                            </a>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <a href="users.php" class="btn btn-secondary w-100">
                                                <i class="fas fa-users me-2"></i>Manage Users
                                            </a>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <a href="upload-test.php" class="btn btn-outline-primary w-100 btn-sm">
                                                <i class="fas fa-upload me-2"></i>Test Upload
                                            </a>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <a href="settings.php" class="btn btn-outline-secondary w-100 btn-sm">
                                                <i class="fas fa-cog me-2"></i>Settings
                                            </a>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <a href="setup-check.php" class="btn btn-outline-info w-100 btn-sm">
                                                <i class="fas fa-database me-2"></i>DB Check
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-clock me-2"></i>Recent Activity</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($recentTours)): ?>
                                        <h6>Latest Tours:</h6>
                                        <?php foreach ($recentTours as $tour): ?>
                                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                                <small><?php echo htmlspecialchars(substr($tour['title'], 0, 30)); ?>...</small>
                                                <small class="text-muted"><?php echo date('M d', strtotime($tour['created_at'])); ?></small>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-muted mb-2">No tours yet</p>
                                    <?php endif; ?>
                                    
                                    <hr>
                                    
                                    <h6>System Status:</h6>
                                    <div class="small mb-3">
                                        <p class="mb-1"><strong>Site:</strong> <?php echo getSetting('site_name') ?: 'Travel Hub'; ?></p>
                                        <p class="mb-1"><strong>Admin:</strong> <?php echo ucfirst($_SESSION['admin_role'] ?? 'admin'); ?></p>
                                        <p class="mb-1"><span class="badge bg-success">Database Connected</span></p>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <a href="../index.php" class="btn btn-outline-primary btn-sm" target="_blank">
                                            <i class="fas fa-external-link-alt me-1"></i>View Website
                                        </a>
                                        <a href="../install.php" class="btn btn-outline-warning btn-sm" target="_blank">
                                            <i class="fas fa-database me-1"></i>Re-run Setup
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Comprehensive Management Links -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-tools me-2"></i>Complete Admin Panel - All Features</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Tour Management -->
                                        <div class="col-md-3 mb-4">
                                            <h6 class="text-primary border-bottom pb-2"><i class="fas fa-map-marked-alt me-2"></i>Tour Management</h6>
                                            <div class="list-group list-group-flush">
                                                <a href="tours.php" class="list-group-item list-group-item-action py-2">
                                                    <i class="fas fa-list me-2"></i>All Tours (<?php echo $totalTours; ?>)
                                                </a>
                                                <a href="tour-add.php" class="list-group-item list-group-item-action py-2">
                                                    <i class="fas fa-plus me-2"></i>Add New Tour
                                                </a>
                                                <a href="destinations.php" class="list-group-item list-group-item-action py-2">
                                                    <i class="fas fa-globe me-2"></i>Destinations (<?php echo $totalDestinations; ?>)
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <!-- Booking Management -->
                                        <div class="col-md-3 mb-4">
                                            <h6 class="text-success border-bottom pb-2"><i class="fas fa-calendar-check me-2"></i>Bookings & Users</h6>
                                            <div class="list-group list-group-flush">
                                                <a href="bookings.php" class="list-group-item list-group-item-action py-2">
                                                    <i class="fas fa-calendar-check me-2"></i>All Bookings (<?php echo $totalBookings; ?>)
                                                </a>
                                                <a href="cab_pricing.php" class="list-group-item list-group-item-action py-2">
                                                    <i class="fas fa-car me-2"></i>Cab Pricing Management
                                                    <span class="badge bg-success ms-2">New</span>
                                                </a>
                                                <a href="users.php" class="list-group-item list-group-item-action py-2">
                                                    <i class="fas fa-users me-2"></i>Manage Users (<?php echo $totalUsers; ?>)
                                                </a>
                                                <a href="../booking.php" class="list-group-item list-group-item-action py-2" target="_blank">
                                                    <i class="fas fa-external-link-alt me-2"></i>View Booking Form
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <!-- Content Management -->
                                        <div class="col-md-3 mb-4">
                                            <h6 class="text-info border-bottom pb-2"><i class="fas fa-blog me-2"></i>Content & Blog</h6>
                                            <div class="list-group list-group-flush">
                                                <a href="blog.php" class="list-group-item list-group-item-action py-2">
                                                    <i class="fas fa-blog me-2"></i>Blog Posts (<?php echo $totalBlogs; ?>)
                                                </a>
                                                <a href="../blog.php" class="list-group-item list-group-item-action py-2" target="_blank">
                                                    <i class="fas fa-external-link-alt me-2"></i>View Blog Page
                                                </a>
                                                <a href="settings.php" class="list-group-item list-group-item-action py-2">
                                                    <i class="fas fa-cog me-2"></i>Site Settings
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <!-- System & Tools -->
                                        <div class="col-md-3 mb-4">
                                            <h6 class="text-warning border-bottom pb-2"><i class="fas fa-tools me-2"></i>System & Tools</h6>
                                            <div class="list-group list-group-flush">
                                                <a href="upload-test.php" class="list-group-item list-group-item-action py-2">
                                                    <i class="fas fa-upload me-2"></i>Test File Upload
                                                </a>
                                                <a href="simple-upload-test.php" class="list-group-item list-group-item-action py-2">
                                                    <i class="fas fa-vial me-2"></i>Simple Upload Test
                                                </a>
                                                <a href="setup-check.php" class="list-group-item list-group-item-action py-2">
                                                    <i class="fas fa-clipboard-check me-2"></i>Database Status Check
                                                </a>
                                                <a href="../install.php" class="list-group-item list-group-item-action py-2" target="_blank">
                                                    <i class="fas fa-database me-2"></i>Database Setup
                                                </a>
                                                <a href="../update_cab_pricing.php" class="list-group-item list-group-item-action py-2" target="_blank">
                                                    <i class="fas fa-car me-2"></i>Setup Cab Pricing
                                                    <span class="badge bg-warning ms-2">Run Once</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <hr>
                                    
                                    <!-- Frontend Links -->
                                    <div class="row">
                                        <div class="col-12">
                                            <h6 class="text-secondary mb-3"><i class="fas fa-external-link-alt me-2"></i>Frontend Pages (View Your Website)</h6>
                                            <div class="btn-group flex-wrap" role="group">
                                                <a href="../index.php" class="btn btn-outline-primary btn-sm" target="_blank">
                                                    <i class="fas fa-home me-1"></i>Homepage
                                                </a>
                                                <a href="../tours.php" class="btn btn-outline-primary btn-sm" target="_blank">
                                                    <i class="fas fa-map-marked-alt me-1"></i>All Tours
                                                </a>
                                                <a href="../blog.php" class="btn btn-outline-primary btn-sm" target="_blank">
                                                    <i class="fas fa-blog me-1"></i>Blog
                                                </a>
                                                <a href="../booking.php" class="btn btn-outline-primary btn-sm" target="_blank">
                                                    <i class="fas fa-calendar-check me-1"></i>Booking
                                                </a>
                                                <a href="../booking_with_cabs.php" class="btn btn-outline-success btn-sm" target="_blank">
                                                    <i class="fas fa-car me-1"></i>Cab Booking
                                                    <span class="badge bg-success ms-1">New</span>
                                                </a>
                                                <a href="../register.php" class="btn btn-outline-primary btn-sm" target="_blank">
                                                    <i class="fas fa-user-plus me-1"></i>Register
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
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
