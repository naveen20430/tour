<?php
require_once 'config/config.php';

// Get search and filter parameters
$search = $_GET['search'] ?? '';
$country = $_GET['country'] ?? '';
$popular = $_GET['popular'] ?? '';

// Build query
$where_conditions = ['status = "active"'];
$params = [];

if ($search) {
    $where_conditions[] = '(name LIKE ? OR description LIKE ? OR country LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($country) {
    $where_conditions[] = 'country = ?';
    $params[] = $country;
}

if ($popular) {
    $where_conditions[] = 'popular = 1';
}

$where_clause = implode(' AND ', $where_conditions);

// Get destinations
$destinations_query = "
    SELECT * FROM destinations 
    WHERE $where_clause
    ORDER BY popular DESC, created_at DESC
";

$destinations = $db->fetchAll($destinations_query, $params);

// Get countries for filter
$countries = $db->fetchAll("SELECT DISTINCT country FROM destinations WHERE status = 'active' ORDER BY country");

// Set page variables
$page_title = 'Destinations - ' . getSetting('site_name');
$current_page = 'destinations';

// Include header
include 'includes/header.php';
?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="page-header__bg"></div>
        <div class="container">
            <h2 class="page-header__title">Travel Destinations</h2>
            <ul class="travhub-breadcrumb list-unstyled">
                <li><a href="<?php echo navUrl('home'); ?>">Home</a></li>
                <li>Destinations</li>
            </ul>
        </div>
    </section>

    <!-- Filters Section -->
    <section class="filter-section" style="background: #f8f9fa; padding: 30px 0; margin-bottom: 40px;">
        <div class="container">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" placeholder="Search destinations..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-3">
                    <select name="country" class="form-control">
                        <option value="">All Countries</option>
                        <?php foreach ($countries as $countryOption): ?>
                            <option value="<?php echo htmlspecialchars($countryOption['country']); ?>" <?php echo $country == $countryOption['country'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($countryOption['country']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="popular" class="form-control">
                        <option value="">All</option>
                        <option value="1" <?php echo $popular == '1' ? 'selected' : ''; ?>>Popular Only</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-1">
                    <a href="<?php echo navUrl('destinations'); ?>" class="btn btn-outline-secondary w-100">Clear</a>
                </div>
            </form>
        </div>
    </section>

    <!-- Destinations Grid -->
    <section class="destinations-grid section-space">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title text-center">
                        <span class="section-title__tagline">Explore The World</span>
                        <h2 class="section-title__title">Amazing Destinations</h2>
                        <p class="section-title__text">
                            Discover breathtaking destinations around the globe and create unforgettable memories
                        </p>
                    </div>
                </div>
            </div>
            
            <?php if (empty($destinations)): ?>
                <div class="row">
                    <div class="col-12 text-center">
                        <div class="alert alert-info">
                            <h4>No destinations found</h4>
                            <p>Try adjusting your search criteria or browse all destinations.</p>
                            <a href="<?php echo navUrl('destinations'); ?>" class="btn btn-primary">View All Destinations</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($destinations as $destination): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100 shadow-sm" style="border-radius: 15px; overflow: hidden; transition: transform 0.3s ease;">
                                <div class="position-relative">
                                    <img src="<?php echo $destination['featured_image'] ?: 'assets/images/destinations/default.jpg'; ?>" 
                                         class="card-img-top" style="height: 250px; object-fit: cover;" 
                                         alt="<?php echo htmlspecialchars($destination['name']); ?>">
                                    
                                    <?php if ($destination['popular']): ?>
                                        <div class="position-absolute top-0 start-0 m-3">
                                            <span class="badge bg-danger">Popular</span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="position-absolute bottom-0 start-0 end-0 p-3" 
                                         style="background: linear-gradient(transparent, rgba(0,0,0,0.7));">
                                        <h5 class="text-white mb-1 fw-bold"><?php echo htmlspecialchars($destination['name']); ?></h5>
                                        <small class="text-light">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            <?php echo htmlspecialchars($destination['country']); ?>
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="card-body d-flex flex-column">
                                    <p class="card-text text-muted flex-grow-1">
                                        <?php echo substr(htmlspecialchars($destination['short_description']), 0, 120); ?>...
                                    </p>
                                    
                                    <?php if ($destination['best_time_to_visit']): ?>
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                Best Time: <?php echo htmlspecialchars($destination['best_time_to_visit']); ?>
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="d-grid gap-2">
                                        <a href="<?php echo destinationUrl($destination['slug']); ?>" class="btn btn-primary">
                                            <i class="fas fa-eye me-1"></i>View Details
                                        </a>
                                        <a href="<?php echo toursUrl(['destination' => $destination['slug']]); ?>" class="btn btn-outline-primary">
                                            <i class="fas fa-map me-1"></i>View Tours
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="row mt-5">
                    <div class="col-12 text-center">
                        <p class="text-muted">Showing <?php echo count($destinations); ?> destinations</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <style>
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }
    .filter-section {
        border-radius: 10px;
    }
    .destinations-grid .card {
        border: none;
    }
    </style>

<?php include 'includes/footer.php'; ?>
