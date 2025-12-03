<?php
require_once '../includes/db.php';
require_once '../includes/cab_options.php';

session_start();

// Simple authentication check - adjust according to your auth system
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}

// Handle booking status updates
if ($_POST && isset($_POST['action'])) {
    $booking_id = $_POST['booking_id'] ?? 0;
    $new_status = $_POST['new_status'] ?? '';
    
    if ($booking_id && $new_status) {
        try {
            $conn->query("UPDATE bookings SET booking_status = '$new_status', updated_at = NOW() WHERE id = $booking_id");
            $success_message = "Booking status updated successfully!";
        } catch (Exception $e) {
            $error_message = "Error updating booking: " . $e->getMessage();
        }
    }
}

// Get bookings with tour and cab information
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';

$where_conditions = [];
$params = [];

if ($search) {
    $where_conditions[] = "(b.guest_name LIKE ? OR b.guest_email LIKE ? OR b.booking_number LIKE ? OR t.title LIKE ?)";
    $search_term = "%$search%";
    $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term]);
}

if ($status_filter) {
    $where_conditions[] = "b.booking_status = ?";
    $params[] = $status_filter;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

$bookings_query = "
    SELECT b.*, t.title as tour_title, t.duration_days, d.name as destination_name
    FROM bookings b 
    LEFT JOIN tours t ON b.tour_id = t.id 
    LEFT JOIN destinations d ON t.destination_id = d.id
    $where_clause
    ORDER BY b.created_at DESC
";

try {
    $stmt = $conn->prepare($bookings_query);
    if ($params) {
        $stmt->execute($params);
    } else {
        $stmt->execute();
    }
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $bookings = [];
    $error_message = "Error fetching bookings: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings Management - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .booking-card { transition: all 0.3s ease; }
        .booking-card:hover { box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .status-badge { font-size: 0.8em; }
        .cab-info { background: #f8f9fa; border-radius: 5px; padding: 8px; margin-top: 8px; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 bg-dark vh-100">
                <div class="pt-3">
                    <h5 class="text-white text-center">Admin Panel</h5>
                    <nav class="nav flex-column mt-4">
                        <a class="nav-link text-light" href="index.php">
                            <i class="fas fa-dashboard me-2"></i> Dashboard
                        </a>
                        <a class="nav-link text-light" href="tours.php">
                            <i class="fas fa-map me-2"></i> Tours
                        </a>
                        <a class="nav-link text-warning fw-bold" href="bookings.php">
                            <i class="fas fa-calendar-check me-2"></i> Bookings
                        </a>
                        <a class="nav-link text-light" href="users.php">
                            <i class="fas fa-users me-2"></i> Users
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10">
                <div class="container-fluid py-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="fas fa-calendar-check me-2"></i>Bookings Management</h2>
                        <div>
                            <a href="export.php" class="btn btn-success">
                                <i class="fas fa-download me-1"></i> Export CSV
                            </a>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="search" 
                                           placeholder="Search by name, email, booking number, or tour..." 
                                           value="<?php echo htmlspecialchars($search); ?>">
                                </div>
                                <div class="col-md-3">
                                    <select name="status" class="form-select">
                                        <option value="">All Status</option>
                                        <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="confirmed" <?php echo $status_filter == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="cancelled" <?php echo $status_filter == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i> Filter
                                    </button>
                                    <a href="bookings.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-refresh me-1"></i> Reset
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Success/Error Messages -->
                    <?php if (isset($success_message)): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?php echo $success_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?php echo $error_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Bookings List -->
                    <div class="row">
                        <?php if (empty($bookings)): ?>
                            <div class="col-12">
                                <div class="text-center py-5">
                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                    <h4>No bookings found</h4>
                                    <p class="text-muted">Try adjusting your search criteria</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($bookings as $booking): ?>
                                <div class="col-lg-6 col-xl-4 mb-4">
                                    <div class="card booking-card h-100">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <strong>#<?php echo htmlspecialchars($booking['booking_number']); ?></strong>
                                            <span class="badge status-badge <?php 
                                                echo $booking['booking_status'] == 'confirmed' ? 'bg-success' : 
                                                    ($booking['booking_status'] == 'cancelled' ? 'bg-danger' : 'bg-warning'); 
                                            ?>">
                                                <?php echo ucfirst($booking['booking_status']); ?>
                                            </span>
                                        </div>
                                        <div class="card-body">
                                            <h6 class="card-title text-primary">
                                                <?php echo htmlspecialchars($booking['tour_title']); ?>
                                            </h6>
                                            
                                            <div class="mb-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    <?php echo htmlspecialchars($booking['destination_name']); ?>
                                                </small>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-6">
                                                    <strong>Guest:</strong><br>
                                                    <small><?php echo htmlspecialchars($booking['guest_name']); ?></small>
                                                </div>
                                                <div class="col-6">
                                                    <strong>Contact:</strong><br>
                                                    <small><?php echo htmlspecialchars($booking['guest_phone']); ?></small>
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-6">
                                                    <strong>Date:</strong><br>
                                                    <small><?php echo date('M j, Y', strtotime($booking['tour_date'])); ?></small>
                                                </div>
                                                <div class="col-6">
                                                    <strong>People:</strong><br>
                                                    <small><?php echo $booking['number_of_people']; ?> person<?php echo $booking['number_of_people'] > 1 ? 's' : ''; ?></small>
                                                </div>
                                            </div>

                                            <!-- Cab Information -->
                                            <?php if (!empty($booking['cab_type'])): ?>
                                                <div class="cab-info">
                                                    <div class="row">
                                                        <div class="col-12 mb-1">
                                                            <strong><i class="fas fa-car me-1"></i>Cab Details:</strong>
                                                        </div>
                                                        <div class="col-6">
                                                            <small><strong>Type:</strong><br><?php echo getCabDisplayName($booking['cab_type']); ?></small>
                                                        </div>
                                                        <div class="col-6">
                                                            <small><strong>Cab Cost:</strong><br>₹<?php echo number_format($booking['cab_price'] ?? 0, 0); ?></small>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <div class="mt-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>Tour Cost:</strong> ₹<?php echo number_format($booking['total_amount'], 0); ?><br>
                                                        <?php if (!empty($booking['total_with_cab']) && $booking['total_with_cab'] != $booking['total_amount']): ?>
                                                            <strong class="text-success">Total: ₹<?php echo number_format($booking['total_with_cab'], 0); ?></strong>
                                                        <?php endif; ?>
                                                    </div>
                                                    <small class="text-muted">
                                                        <?php echo date('M j, Y', strtotime($booking['created_at'])); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                <input type="hidden" name="action" value="update_status">
                                                <div class="btn-group w-100">
                                                    <select name="new_status" class="form-select form-select-sm">
                                                        <option value="pending" <?php echo $booking['booking_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="confirmed" <?php echo $booking['booking_status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                                        <option value="cancelled" <?php echo $booking['booking_status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                    </select>
                                                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
