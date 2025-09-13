<?php
require_once 'config/config.php';

// Get search and filter parameters
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$difficulty = $_GET['difficulty'] ?? '';
$tour_type = $_GET['tour_type'] ?? '';

// Build query
$where_conditions = ['t.status = "active"'];
$params = [];

if ($search) {
    $where_conditions[] = '(t.title LIKE ? OR t.description LIKE ? OR d.name LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category) {
    $where_conditions[] = 'tc.slug = ?';
    $params[] = $category;
}

if ($min_price) {
    $where_conditions[] = 't.price >= ?';
    $params[] = $min_price;
}

if ($max_price) {
    $where_conditions[] = 't.price <= ?';
    $params[] = $max_price;
}

if ($difficulty) {
    $where_conditions[] = 't.difficulty_level = ?';
    $params[] = $difficulty;
}

if ($tour_type) {
    $where_conditions[] = 't.tour_type = ?';
    $params[] = $tour_type;
}

$where_clause = implode(' AND ', $where_conditions);

// Get tours
$tours_query = "
    SELECT DISTINCT t.*, d.name as destination_name, d.country
    FROM tours t 
    LEFT JOIN destinations d ON t.destination_id = d.id
    LEFT JOIN tour_category_relations tcr ON t.id = tcr.tour_id
    LEFT JOIN tour_categories tc ON tcr.category_id = tc.id
    WHERE $where_clause
    ORDER BY t.featured DESC, t.created_at DESC
";

$tours = $db->fetchAll($tours_query, $params);

// Get categories for filter
$categories = $db->fetchAll("SELECT * FROM tour_categories WHERE status = 'active' ORDER BY name");

// Get price range
$price_range = $db->fetch("SELECT MIN(price) as min_price, MAX(price) as max_price FROM tours WHERE status = 'active'");

// Set page variables
$page_title = 'Tours - ' . getSetting('site_name');
$current_page = 'tours';
$extra_css = '

<style>
    .tour-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        border-radius: 15px;
        overflow: hidden;
        margin-bottom: 30px;
    }
    .tour-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }
    .tour-image {
        height: 250px;
        object-fit: cover;
        width: 100%;
    }
    .price-tag {
        position: absolute;
        top: 15px;
        right: 15px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 8px 15px;
        border-radius: 25px;
        font-weight: bold;
    }
    .discount-price {
        text-decoration: line-through;
        color: #999;
        font-size: 0.9em;
    }
    .tour-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: #ff6b6b;
        color: white;
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 0.8em;
        font-weight: bold;
    }
    .filter-section {
        background: #f8f9fa;
        padding: 30px 0;
        border-radius: 10px;
        margin-bottom: 40px;
    }
</style>';

// Include header
include 'includes/header.php';
?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="page-header__bg"></div>
        <div class="container">
            <h2 class="page-header__title">Our Amazing Tours</h2>
            <ul class="travhub-breadcrumb list-unstyled">
                <li><a href="<?php echo navUrl('home'); ?>">Home</a></li>
                <li>Tours</li>
            </ul>
        </div>
    </section>

    <!-- Filters Section -->
    <section class="filter-section">
        <div class="container">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="search" placeholder="Search tours..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-2">
                    <select name="category" class="form-control">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['slug']; ?>" <?php echo $category == $cat['slug'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="difficulty" class="form-control">
                        <option value="">Difficulty</option>
                        <option value="easy" <?php echo $difficulty == 'easy' ? 'selected' : ''; ?>>Easy</option>
                        <option value="moderate" <?php echo $difficulty == 'moderate' ? 'selected' : ''; ?>>Moderate</option>
                        <option value="difficult" <?php echo $difficulty == 'difficult' ? 'selected' : ''; ?>>Difficult</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" name="min_price" placeholder="Min Price" value="<?php echo $min_price; ?>">
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" name="max_price" placeholder="Max Price" value="<?php echo $max_price; ?>">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </section>

    <!-- Tours Grid -->
    <section class="tours-grid section-space">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title text-center">
                        <span class="section-title__tagline">Discover Amazing</span>
                        <h2 class="section-title__title">Our Tour Packages</h2>
                        <p class="section-title__text">
                            Choose from our carefully curated selection of tours designed to give you the best travel experience
                        </p>
                    </div>
                </div>
            </div>
            
            <?php if (empty($tours)): ?>
                <div class="row">
                    <div class="col-12 text-center">
                        <div class="alert alert-info">
                            <h4>No tours found</h4>
                            <p>Try adjusting your search criteria or browse all tours.</p>
                            <a href="<?php echo navUrl('tours'); ?>" class="btn btn-primary">View All Tours</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($tours as $tour): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="card tour-card shadow-sm">
                                <div class="position-relative">
                                    <img src="<?php echo $tour['featured_image'] ?: 'assets/images/tours/default-tour.jpg'; ?>" 
                                         class="tour-image" alt="<?php echo htmlspecialchars($tour['title']); ?>">
                                    
                                    <?php if ($tour['featured']): ?>
                                        <span class="tour-badge">Featured</span>
                                    <?php endif; ?>
                                    
                                    <div class="price-tag">
                                        <?php if ($tour['discount_price']): ?>
                                            <span class="discount-price">₹<?php echo number_format($tour['price'], 0); ?></span>
                                            ₹<?php echo number_format($tour['discount_price'], 0); ?>
                                        <?php else: ?>
                                            ₹<?php echo number_format($tour['price'], 0); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            <?php echo htmlspecialchars($tour['destination_name']); ?>
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            <?php echo $tour['duration_days']; ?> Days
                                        </small>
                                    </div>
                                    
                                    <h5 class="card-title">
                                        <a href="<?php echo tourUrl($tour['slug']); ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($tour['title']); ?>
                                        </a>
                                    </h5>
                                    
                                    <p class="card-text text-muted">
                                        <?php echo substr(htmlspecialchars($tour['short_description']), 0, 120); ?>...
                                    </p>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="tour-meta">
                                            <span class="badge bg-primary me-1"><?php echo ucfirst($tour['difficulty_level']); ?></span>
                                            <span class="badge bg-secondary"><?php echo ucfirst($tour['tour_type']); ?></span>
                                        </div>
                                        <small class="text-muted">
                                            <i class="fas fa-users me-1"></i>
                                            Max <?php echo $tour['max_people']; ?>
                                        </small>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <a href="<?php echo tourUrl($tour['slug']); ?>" class="btn btn-primary w-100">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="row mt-5">
                    <div class="col-12 text-center">
                        <p class="text-muted">Showing <?php echo count($tours); ?> tours</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>
