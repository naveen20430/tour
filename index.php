<?php
require_once 'config/config.php';
require_once 'includes/hero_helper.php';

// Set page variables
$page_title = getSetting('site_name') . ' || Travel & Tour Booking Agency';
$current_page = 'home';

// Enable hero CSS for this page
enableHeroCSS();

// Get featured tours
$featured_tours = $db->fetchAll("
    SELECT t.*, d.name as destination_name, d.country 
    FROM tours t 
    LEFT JOIN destinations d ON t.destination_id = d.id 
    WHERE t.featured = 1 AND t.status = 'active' 
    ORDER BY t.created_at DESC 
    LIMIT 6
");

// Get popular destinations
$popular_destinations = $db->fetchAll("
    SELECT * FROM destinations 
    WHERE popular = 1 AND status = 'active' 
    ORDER BY created_at DESC 
    LIMIT 4
");

// Include header
include 'includes/header.php';
?>

<?php displayHero(); ?>

    <section class="about-one section-space">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center">
                        <h2>Welcome to <?php echo getSetting('site_name'); ?></h2>
                        <p class="lead">Your Adventure Starts Here</p>
                        <p>Experience the world like never before with our carefully curated travel packages. 
                        From exotic destinations to cultural experiences, we make your travel dreams come true.</p>
                        <div class="mt-4"><?php echo adminUrl(); ?>
                            <a href="<?php echo adminUrl('login'); ?>" class="travhub-btn me-3">
                                <span>Admin Panel</span>
                            </a>
                            <a href="<?php echo navUrl('tours'); ?>" class="travhub-btn">
                                <span>View All Tours</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Tours Section -->
    <?php if (!empty($featured_tours)): ?>
    <section class="tours-one section-space">
        <div class="container">
            <div class="section-title text-center">
                <span class="section-title__tagline">Discover Amazing</span>
                <h2 class="section-title__title">Featured Tour Packages</h2>
                <p class="section-title__text">
                    Explore our handpicked selection of the most popular and exciting tours
                </p>
            </div>
            
            <div class="row mt-5">
                <?php foreach ($featured_tours as $tour): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                            <div class="position-relative">
                                <img src="<?php echo $tour['featured_image'] ?: 'assets/images/tours/default.jpg'; ?>" 
                                     class="card-img-top" style="height: 250px; object-fit: cover;" 
                                     alt="<?php echo htmlspecialchars($tour['title']); ?>">
                                
                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="badge bg-primary px-3 py-2" style="border-radius: 25px;">
                                        <?php if ($tour['discount_price']): ?>
                                            <span style="text-decoration: line-through; opacity: 0.7;">₹<?php echo number_format($tour['price'], 0); ?></span>
                                            ₹<?php echo number_format($tour['discount_price'], 0); ?>
                                        <?php else: ?>
                                            ₹<?php echo number_format($tour['price'], 0); ?>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                
                                <div class="position-absolute top-0 start-0 m-3">
                                    <span class="badge bg-danger" style="border-radius: 15px;">Featured</span>
                                </div>
                            </div>
                            
                            <div class="card-body d-flex flex-column">
                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        <?php echo htmlspecialchars($tour['destination_name'] . ', ' . $tour['country']); ?>
                                    </small>
                                    <small class="text-muted float-end">
                                        <i class="fas fa-clock me-1"></i>
                                        <?php echo $tour['duration_days']; ?> Days
                                    </small>
                                </div>
                                
                                <h5 class="card-title">
                                    <a href="<?php echo tourUrl($tour['slug']); ?>" class="text-decoration-none text-dark">
                                        <?php echo htmlspecialchars($tour['title']); ?>
                                    </a>
                                </h5>
                                
                                <p class="card-text text-muted flex-grow-1">
                                    <?php echo substr(htmlspecialchars($tour['short_description']), 0, 100); ?>...
                                </p>
                                
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <div>
                                        <span class="badge bg-info me-1"><?php echo ucfirst($tour['difficulty_level']); ?></span>
                                        <span class="badge bg-secondary"><?php echo ucfirst($tour['tour_type']); ?></span>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-users me-1"></i>Max <?php echo $tour['max_people']; ?>
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
            
            <div class="text-center mt-5">
                <a href="<?php echo navUrl('tours'); ?>" class="travhub-btn">
                    <span>View All Tours</span>
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Popular Destinations -->
    <?php if (!empty($popular_destinations)): ?>
    <section class="destinations-one section-space" style="background: #f8f9fa;">
        <div class="container">
            <div class="section-title text-center">
                <span class="section-title__tagline">Explore The World</span>
                <h2 class="section-title__title">Popular Destinations</h2>
                <p class="section-title__text">
                    Discover the most sought-after travel destinations around the globe
                </p>
            </div>
            
            <div class="row mt-5">
                <?php foreach ($popular_destinations as $destination): ?>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                            <div class="position-relative">
                                <img src="<?php echo $destination['featured_image'] ?: 'assets/images/destinations/default.jpg'; ?>" 
                                     class="card-img-top" style="height: 200px; object-fit: cover;" 
                                     alt="<?php echo htmlspecialchars($destination['name']); ?>">
                                
                                <div class="position-absolute bottom-0 start-0 end-0 p-3" 
                                     style="background: linear-gradient(transparent, rgba(0,0,0,0.7));">
                                    <h6 class="text-white mb-1 fw-bold"><?php echo htmlspecialchars($destination['name']); ?></h6>
                                    <small class="text-light"><?php echo htmlspecialchars($destination['country']); ?></small>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <p class="card-text text-muted small">
                                    <?php echo substr(htmlspecialchars($destination['short_description']), 0, 80); ?>...
                                </p>
                                
                                <a href="<?php echo toursUrl(['destination' => $destination['slug']]); ?>" class="btn btn-outline-primary btn-sm w-100">
                                    Explore Tours
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

<?php include 'includes/footer.php'; ?>
