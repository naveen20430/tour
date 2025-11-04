<?php
require_once 'config/config.php';
require_once 'includes/cab_options.php';

// Get tour slug
$slug = $_GET['slug'] ?? '';

if (!$slug) {
    header('Location: tours.php');
    exit;
}

// Get tour details
$tour = $db->fetch("
    SELECT t.*, d.name as destination_name, d.country, d.description as destination_description
    FROM tours t 
    LEFT JOIN destinations d ON t.destination_id = d.id
    WHERE t.slug = ? AND t.status = 'active'
", [$slug]);

if (!$tour) {
    header('Location: tours.php');
    exit;
}

// Parse JSON fields
$inclusions = json_decode($tour['inclusions'], true) ?: [];
$exclusions = json_decode($tour['exclusions'], true) ?: [];
$itinerary = json_decode($tour['itinerary'], true) ?: [];

// Get related tours
$related_tours = $db->fetchAll("
    SELECT t.*, d.name as destination_name
    FROM tours t 
    LEFT JOIN destinations d ON t.destination_id = d.id
    WHERE t.destination_id = ? AND t.id != ? AND t.status = 'active'
    LIMIT 3
", [$tour['destination_id'], $tour['id']]);

// Initialize cab options with error handling
$availableCabs = [];
$cab_functionality_enabled = false;

try {
    if (file_exists('includes/cab_options.php')) {
        // Test if cab_types table exists
        $db->fetch("SELECT COUNT(*) as count FROM cab_types LIMIT 1");
        $cabOptions = new CabOptions($db);
        $availableCabs = $cabOptions->getCabOptionsForDropdown();
        $cab_functionality_enabled = true;
    }
} catch (Exception $e) {
    // Cab functionality not available, continue without it
    $availableCabs = [];
    $cab_functionality_enabled = false;
}

// Set page variables
$page_title = htmlspecialchars($tour['title']) . ' - ' . getSetting('site_name');
$current_page = 'tours';
$extra_css = '

<style>
    .tour-hero {
        height: 400px;
        background-size: cover;
        background-position: center;
        position: relative;
        display: flex;
        align-items: center;
    }
    .tour-hero::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.4);
    }
    .tour-hero-content {
        position: relative;
        z-index: 2;
        color: white;
    }
    .price-box {
        background: #1bbc9b;
        color: white;
        padding: 30px;
        border-radius: 15px;
        position: sticky;
        top: 100px;
    }
    .itinerary-day {
        border-left: 3px solid #667eea;
        padding-left: 20px;
        margin-bottom: 30px;
        position: relative;
    }
    .itinerary-day::before {
        content: "";
        position: absolute;
        left: -8px;
        top: 0;
        width: 13px;
        height: 13px;
        background: #667eea;
        border-radius: 50%;
    }
    .feature-list {
        list-style: none;
        padding: 0;
    }
    .feature-list li {
        padding: 8px 0;
        border-bottom: 1px solid #eee;
    }
    .feature-list li:last-child {
        border-bottom: none;
    }
    .feature-list li i {
        color: #28a745;
        margin-right: 10px;
    }
</style>';

// Include header
include 'includes/header.php';
?>

    <!-- Tour Hero -->
    <section class="tour-hero" style="background-image: url('<?php echo $tour['featured_image'] ?: 'assets/images/tours/default-tour.jpg'; ?>')">
        <div class="container">
            <div class="tour-hero-content">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo navUrl('home'); ?>" class="text-white">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo navUrl('tours'); ?>" class="text-white">Tours</a></li>
                        <li class="breadcrumb-item active text-white"><?php echo htmlspecialchars($tour['title']); ?></li>
                    </ol>
                </nav>
                <h1 class="display-4 fw-bold"><?php echo htmlspecialchars($tour['title']); ?></h1>
                <p class="lead">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    <?php echo htmlspecialchars($tour['destination_name'] . ', ' . $tour['country']); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Photo Collage Gallery -->
    <?php 
    $gallery_images = json_decode($tour['gallery'], true) ?: [];
    if (!empty($gallery_images) || !empty($tour['featured_image'])): 
    ?>
    <section class="photo-collage-section" style="padding: 80px 0; background: #ffffff;">
        <div class="container">
            <div class="text-center mb-5">
                <span class="badge" style="background: #1bbc9b; color: white; padding: 8px 16px; border-radius: 20px; font-size: 0.9rem; margin-bottom: 15px;">
                    📸 Photo Gallery
                </span>
                <h2 class="mb-3" style="font-size: 2.5rem; font-weight: 700; color: #2c3e50;">Explore <?php echo htmlspecialchars($tour['title']); ?></h2>
                <p style="color: #6c757d; font-size: 1.1rem; max-width: 600px; margin: 0 auto;">Experience the beauty and adventure through our carefully captured moments</p>
            </div>
            
            <div class="photo-collage-container" style="position: relative; max-width: 1200px; margin: 0 auto;">
                <div class="photo-collage-grid" id="photoCollage" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; min-height: 500px;">
                    <?php 
                    // Combine featured image with gallery images
                    $all_images = [];
                    if (!empty($tour['featured_image'])) {
                        $all_images[] = $tour['featured_image'];
                    }
                    $all_images = array_merge($all_images, $gallery_images);
                    
                    $image_count = count($all_images);
                    
                    // Define the layout pattern similar to your reference image
                    if ($image_count > 0): 
                        // First image - large hero image (takes 2 columns, 2 rows)
                        $first_image = $all_images[0];
                    ?>
                    <div class="collage-item hero-item" style="grid-column: 1 / 3; grid-row: 1 / 3; position: relative; overflow: hidden; border-radius: 20px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 10px 30px rgba(0,0,0,0.2); min-height: 400px;" 
                         onclick="openPhotoModal('<?php echo BASE_URL . $first_image; ?>', '<?php echo htmlspecialchars($tour['title']); ?> - Featured Image')">
                        <img src="<?php echo BASE_URL . $first_image; ?>" 
                             alt="<?php echo htmlspecialchars($tour['title']); ?> - Featured Image" 
                             style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;" 
                             loading="lazy">
                        <div class="collage-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(45deg, rgba(0,0,0,0.3), transparent); opacity: 0; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-expand-alt" style="color: white; font-size: 32px; transform: scale(0.8); transition: transform 0.3s ease;"></i>
                        </div>
                        <div class="image-overlay-content" style="position: absolute; bottom: 20px; left: 20px; color: white; z-index: 2;">
                            <div class="featured-badge" style="background: rgba(255, 255, 255, 0.9); color: #333; padding: 8px 16px; border-radius: 25px; font-size: 0.85rem; font-weight: 600; backdrop-filter: blur(10px); display: inline-block; margin-bottom: 10px;">
                                ⭐ Featured
                            </div>
                        </div>
                    </div>
                    
                    <?php 
                    // Right column - smaller images
                    $remaining_images = array_slice($all_images, 1, 3); // Take next 3 images
                    $positions = [
                        ['grid-column: 3 / 5; grid-row: 1; min-height: 190px;'],
                        ['grid-column: 3 / 4; grid-row: 2; min-height: 190px;'],
                        ['grid-column: 4 / 5; grid-row: 2; min-height: 190px;']
                    ];
                    
                    foreach ($remaining_images as $index => $image): 
                        if ($index >= 3) break;
                        $style = $positions[$index];
                        $image_labels = ['Destinations', 'Activity & Sightseeing', 'Stays'];
                        $label = isset($image_labels[$index]) ? $image_labels[$index] : 'Gallery';
                    ?>
                    <div class="collage-item small-item" style="<?php echo $style; ?> position: relative; overflow: hidden; border-radius: 15px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 8px 25px rgba(0,0,0,0.15);" 
                         onclick="openPhotoModal('<?php echo BASE_URL . $image; ?>', '<?php echo htmlspecialchars($tour['title']); ?> - <?php echo $label; ?>')">
                        <img src="<?php echo BASE_URL . $image; ?>" 
                             alt="<?php echo htmlspecialchars($tour['title']); ?> - <?php echo $label; ?>" 
                             style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;" 
                             loading="lazy">
                        <div class="collage-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(45deg, rgba(0,0,0,0.4), transparent); opacity: 0; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-search-plus" style="color: white; font-size: 20px; transform: scale(0.8); transition: transform 0.3s ease;"></i>
                        </div>
                        <div class="image-label" style="position: absolute; bottom: 15px; left: 15px; color: white; font-weight: 600; font-size: 0.9rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.8); z-index: 2;">
                            <?php echo $label; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php if ($image_count > 4): ?>
                    <!-- View All Images Button -->
                    <div class="view-all-btn" style="position: absolute; bottom: 20px; right: 20px; background: rgba(255, 255, 255, 0.95); color: #333; padding: 12px 20px; border-radius: 25px; font-size: 0.9rem; font-weight: 600; backdrop-filter: blur(10px); cursor: pointer; transition: all 0.3s ease; box-shadow: 0 5px 15px rgba(0,0,0,0.1); border: 2px solid transparent; z-index: 5;" 
                         onclick="openAllImagesModal()" 
                         onmouseover="this.style.background='#667eea'; this.style.color='white'; this.style.transform='translateY(-2px)'" 
                         onmouseout="this.style.background='rgba(255, 255, 255, 0.95)'; this.style.color='#333'; this.style.transform='translateY(0)'">
                        <i class="fas fa-images" style="margin-right: 8px;"></i> View All <?php echo $image_count; ?> Images
                    </div>
                    <?php endif; ?>
                    
                    <?php else: ?>
                    <!-- Single image fallback -->
                    <div class="collage-item single-item" style="grid-column: 1 / 5; grid-row: 1; position: relative; overflow: hidden; border-radius: 20px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 10px 30px rgba(0,0,0,0.2); min-height: 400px;" 
                         onclick="openPhotoModal('<?php echo BASE_URL . $tour['featured_image']; ?>', '<?php echo htmlspecialchars($tour['title']); ?>')">
                        <img src="<?php echo BASE_URL . $tour['featured_image']; ?>" 
                             alt="<?php echo htmlspecialchars($tour['title']); ?>" 
                             style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;" 
                             loading="lazy">
                        <div class="collage-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(45deg, rgba(0,0,0,0.3), transparent); opacity: 0; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-expand-alt" style="color: white; font-size: 32px; transform: scale(0.8); transition: transform 0.3s ease;"></i>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (empty($all_images)): ?>
            <div class="no-gallery" style="text-align: center; padding: 80px 20px; background: #f8f9fa; border-radius: 20px;">
                <i class="fas fa-camera" style="font-size: 48px; color: #bdc3c7; margin-bottom: 20px;"></i>
                <h4 style="color: #6c757d; margin-bottom: 10px;">Gallery Coming Soon</h4>
                <p style="color: #95a5a6; margin: 0;">We're preparing beautiful photos of this amazing destination</p>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Photo Modal -->
    <div id="photoModal" class="photo-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.95); z-index: 9999; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease;">
        <div class="modal-content" style="position: relative; max-width: 90%; max-height: 90%; display: flex; align-items: center; justify-content: center;">
            <img id="modalImage" src="" alt="" style="max-width: 100%; max-height: 100%; border-radius: 12px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">
            <div class="modal-caption" style="position: absolute; bottom: -60px; left: 0; right: 0; text-align: center; color: white; font-size: 16px; font-weight: 500;" id="modalCaption"></div>
            <button class="modal-close" onclick="closePhotoModal()" style="position: absolute; top: -50px; right: 0; background: none; border: none; color: white; font-size: 32px; cursor: pointer; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: background 0.3s ease;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='none'">
                <i class="fas fa-times"></i>
            </button>
            <button class="modal-nav modal-prev" onclick="navigatePhoto(-1)" style="position: absolute; left: -60px; top: 50%; transform: translateY(-50%); background: rgba(255, 255, 255, 0.1); border: none; color: white; font-size: 24px; cursor: pointer; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: all 0.3s ease; backdrop-filter: blur(10px);" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="modal-nav modal-next" onclick="navigatePhoto(1)" style="position: absolute; right: -60px; top: 50%; transform: translateY(-50%); background: rgba(255, 255, 255, 0.1); border: none; color: white; font-size: 24px; cursor: pointer; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: all 0.3s ease; backdrop-filter: blur(10px);" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Tour Details -->
    <section class="section-space">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Tour Overview -->
                    <div class="tour-content">
                        <div class="mb-4">
                            <div class="d-flex flex-wrap gap-3 mb-3">
                                <span class="badge bg-primary px-3 py-2">
                                    <i class="fas fa-clock me-1"></i>
                                    <?php echo $tour['duration_days']; ?> Days / <?php echo $tour['duration_nights']; ?> Nights
                                </span>
                                <span class="badge bg-success px-3 py-2">
                                    <i class="fas fa-users me-1"></i>
                                    Max <?php echo $tour['max_people']; ?> People
                                </span>
                                <span class="badge bg-info px-3 py-2">
                                    <i class="fas fa-mountain me-1"></i>
                                    <?php echo ucfirst($tour['difficulty_level']); ?>
                                </span>
                                <span class="badge bg-secondary px-3 py-2">
                                    <i class="fas fa-tag me-1"></i>
                                    <?php echo ucfirst($tour['tour_type']); ?>
                                </span>
                            </div>
                        </div>

                        <div class="mb-5">
                            <h3 class="mb-3">Tour Overview</h3>
                            <p class="lead text-muted"><?php echo htmlspecialchars($tour['short_description']); ?></p>
                            <p><?php echo nl2br(htmlspecialchars($tour['description'])); ?></p>
                        </div>

                        <!-- Inclusions & Exclusions -->
                        <div class="row mb-5">
                            <div class="col-md-6">
                                <h4 class="mb-3 text-success">What's Included</h4>
                                <ul class="feature-list">
                                    <?php foreach ($inclusions as $inclusion): ?>
                                        <li><i class="fas fa-check"></i><?php echo htmlspecialchars($inclusion); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h4 class="mb-3 text-danger">What's Not Included</h4>
                                <ul class="feature-list">
                                    <?php foreach ($exclusions as $exclusion): ?>
                                        <li><i class="fas fa-times text-danger"></i><?php echo htmlspecialchars($exclusion); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>

                        <!-- Itinerary -->
                        <?php if ($itinerary): ?>
                            <div class="mb-5">
                                <h3 class="mb-4">Tour Itinerary</h3>
                                <?php foreach ($itinerary as $day): ?>
                                    <div class="itinerary-day">
                                        <h5 class="fw-bold">Day <?php echo $day['day']; ?>: <?php echo htmlspecialchars($day['title']); ?></h5>
                                        <p class="text-muted"><?php echo htmlspecialchars($day['description']); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Related Tours -->
                        <?php if ($related_tours): ?>
                            <div class="mb-5">
                                <h3 class="mb-4">Related Tours</h3>
                                <div class="row">
                                    <?php foreach ($related_tours as $related): ?>
                                        <div class="col-md-4 mb-3">
                                            <div class="card">
                                                <img src="<?php echo $related['featured_image'] ?: 'assets/images/tours/default-tour.jpg'; ?>" 
                                                     class="card-img-top" style="height: 200px; object-fit: cover;">
                                                <div class="card-body">
                                                    <h6 class="card-title">
                                        <a href="<?php echo tourUrl($related['slug']); ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($related['title']); ?>
                                        </a>
                                                    </h6>
                                                    <p class="card-text small text-muted">
                                                        <?php echo htmlspecialchars($related['destination_name']); ?> • 
                                                        ₹<?php echo number_format($related['price'], 0); ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Booking Sidebar -->
                <div class="col-lg-4">
                    <div class="price-box">
                        <div class="text-center mb-3">
                            <?php if ($tour['discount_price']): ?>
                                <div class="h4 mb-1">
                                    <span class="text-decoration-line-through opacity-50">₹<?php echo number_format($tour['price'], 0); ?></span>
                                    <span class="ms-2">₹<?php echo number_format($tour['discount_price'], 0); ?></span>
                                </div>
                                <small class="text-light">You save ₹<?php echo number_format($tour['price'] - $tour['discount_price'], 0); ?>!</small>
                            <?php else: ?>
                                <div class="h3">₹<?php echo number_format($tour['price'], 0); ?></div>
                            <?php endif; ?>
                            <div class="small">Per Person</div>
                        </div>

                        <hr class="border-light opacity-25">

                        <div class="tour-info mb-4">
                            <div class="d-flex justify-content-between py-2">
                                <span><i class="fas fa-calendar me-2"></i>Duration:</span>
                                <span><?php echo $tour['duration_days']; ?> Days</span>
                            </div>
                            <div class="d-flex justify-content-between py-2">
                                <span><i class="fas fa-users me-2"></i>Max People:</span>
                                <span><?php echo $tour['max_people']; ?></span>
                            </div>
                            <div class="d-flex justify-content-between py-2">
                                <span><i class="fas fa-mountain me-2"></i>Difficulty:</span>
                                <span><?php echo ucfirst($tour['difficulty_level']); ?></span>
                            </div>
                            <div class="d-flex justify-content-between py-2">
                                <span><i class="fas fa-map-marker-alt me-2"></i>Location:</span>
                                <span><?php echo htmlspecialchars($tour['destination_name']); ?></span>
                            </div>
                        </div>

                        <form action="<?php echo bookingUrl(); ?>" method="POST" id="quickBookingForm">
                            <input type="hidden" name="tour_id" value="<?php echo $tour['id']; ?>">
                            
                            <div class="mb-3">
                                <label class="form-label text-light">Tour Date</label>
                                <input type="date" class="form-control" name="tour_date" required 
                                       min="<?php echo date('Y-m-d'); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-light">Number of People</label>
                                <select class="form-control" name="people" required id="peopleSelect">
                                    <?php for ($i = $tour['min_people']; $i <= $tour['max_people']; $i++): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?> Person<?php echo $i > 1 ? 's' : ''; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            
                            <?php if ($cab_functionality_enabled && !empty($availableCabs)): ?>
                            <div class="mb-3">
                                <label class="form-label text-light">Cab Type</label>
                                <select class="form-control" name="cab_type" required id="cabSelect">
                                    <option value="">Select cab type...</option>
                                    <?php foreach ($availableCabs as $cab): ?>
                                        <option value="<?php echo $cab['value']; ?>" 
                                                data-price="<?php echo $cab['price']; ?>"
                                                data-max-passengers="<?php echo $cab['max_passengers']; ?>">
                                            <?php echo htmlspecialchars($cab['text']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-light opacity-75">Cab provided for entire tour duration</small>
                            </div>
                            <?php endif; ?>
                            
                            <button type="submit" class="btn btn-light w-100 fw-bold">
                                <i class="fas fa-calendar-plus me-2"></i>Book This Tour
                            </button>
                        </form>

                        <div class="text-center mt-3">
                            <small class="text-light opacity-75">
                                <i class="fas fa-shield-alt me-1"></i>
                                Secure booking guaranteed
                            </small>
                        </div>

                        <hr class="border-light opacity-25 mt-4">

                        <div class="contact-info">
                            <h6 class="text-light mb-3">Need Help?</h6>
                            <div class="mb-2">
                                <i class="fas fa-phone me-2"></i>
                                <a href="tel:<?php echo getSetting('site_phone'); ?>" class="text-light text-decoration-none">
                                    <?php echo getSetting('site_phone'); ?>
                                </a>
                            </div>
                            <div>
                                <i class="fas fa-envelope me-2"></i>
                                <a href="mailto:<?php echo getSetting('site_email'); ?>" class="text-light text-decoration-none">
                                    <?php echo getSetting('site_email'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<style>
/* Photo Collage Hover Effects */
.collage-item:hover img {
    transform: scale(1.05);
}

.collage-item:hover .collage-overlay {
    opacity: 1;
}

.collage-item:hover .collage-overlay i {
    transform: scale(1);
}

/* Photo Collage Responsive Design */
@media (max-width: 1024px) {
    .photo-collage-container {
        max-width: 100%;
        padding: 0 15px;
    }
}

@media (max-width: 768px) {
    .photo-collage-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 10px !important;
    }
    
    .hero-item {
        grid-column: 1 / 3 !important;
        grid-row: 1 / 3 !important;
        min-height: 300px !important;
    }
    
    .small-item:nth-child(2) {
        grid-column: 1 / 2 !important;
        grid-row: 3 / 4 !important;
        min-height: 150px !important;
    }
    
    .small-item:nth-child(3) {
        grid-column: 2 / 3 !important;
        grid-row: 3 / 4 !important;
        min-height: 150px !important;
    }
    
    .small-item:nth-child(4) {
        grid-column: 1 / 3 !important;
        grid-row: 4 / 5 !important;
        min-height: 150px !important;
    }
    
    .view-all-btn {
        bottom: 10px !important;
        right: 10px !important;
        padding: 10px 16px !important;
        font-size: 0.8rem !important;
    }
    
    .modal-nav {
        display: none !important;
    }
    
    .modal-close {
        top: 20px !important;
        right: 20px !important;
    }
    
    .modal-caption {
        bottom: 20px !important;
        padding: 0 20px;
        font-size: 14px !important;
    }
}

@media (max-width: 480px) {
    .photo-collage-section {
        padding: 40px 0 !important;
    }
    
    .photo-collage-section h2 {
        font-size: 1.8rem !important;
    }
    
    .hero-item {
        min-height: 250px !important;
        border-radius: 15px !important;
    }
    
    .small-item {
        border-radius: 10px !important;
        min-height: 120px !important;
    }
    
    .image-label {
        font-size: 0.8rem !important;
        bottom: 10px !important;
        left: 10px !important;
    }
    
    .featured-badge {
        padding: 6px 12px !important;
        font-size: 0.75rem !important;
    }
}
</style>

<script>
// Photo Gallery Modal Functionality
let currentPhotoIndex = 0;
let allPhotoImages = [];

// Initialize photo arrays when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Collect all images from the collage
    const collageItems = document.querySelectorAll('.collage-item img');
    allPhotoImages = Array.from(collageItems).map(img => ({
        src: img.src,
        alt: img.alt
    }));
    
    // Add hover effects
    const collageGrid = document.getElementById('photoCollage');
    if (collageGrid) {
        // Add smooth fade-in animation
        collageGrid.style.opacity = '0';
        setTimeout(() => {
            collageGrid.style.transition = 'opacity 0.8s ease';
            collageGrid.style.opacity = '1';
        }, 100);
    }
});

// Open photo modal
function openPhotoModal(imageSrc, caption) {
    const modal = document.getElementById('photoModal');
    const modalImage = document.getElementById('modalImage');
    const modalCaption = document.getElementById('modalCaption');
    
    // Find the index of the clicked image
    currentPhotoIndex = allPhotoImages.findIndex(img => img.src === imageSrc);
    if (currentPhotoIndex === -1) currentPhotoIndex = 0;
    
    // Set image and caption
    modalImage.src = imageSrc;
    modalImage.alt = caption;
    modalCaption.textContent = caption;
    
    // Show modal with fade effect
    modal.style.display = 'flex';
    setTimeout(() => {
        modal.style.opacity = '1';
    }, 10);
    
    // Prevent body scrolling
    document.body.style.overflow = 'hidden';
    
    // Add escape key listener
    document.addEventListener('keydown', handleModalKeydown);
}

// Close photo modal
function closePhotoModal() {
    const modal = document.getElementById('photoModal');
    
    // Fade out effect
    modal.style.opacity = '0';
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
    
    // Restore body scrolling
    document.body.style.overflow = 'auto';
    
    // Remove escape key listener
    document.removeEventListener('keydown', handleModalKeydown);
}

// Navigate through photos
function navigatePhoto(direction) {
    if (allPhotoImages.length === 0) return;
    
    // Calculate new index
    currentPhotoIndex += direction;
    if (currentPhotoIndex < 0) currentPhotoIndex = allPhotoImages.length - 1;
    if (currentPhotoIndex >= allPhotoImages.length) currentPhotoIndex = 0;
    
    // Update modal content
    const modalImage = document.getElementById('modalImage');
    const modalCaption = document.getElementById('modalCaption');
    const currentImage = allPhotoImages[currentPhotoIndex];
    
    // Fade transition effect
    modalImage.style.opacity = '0';
    setTimeout(() => {
        modalImage.src = currentImage.src;
        modalImage.alt = currentImage.alt;
        modalCaption.textContent = currentImage.alt;
        modalImage.style.opacity = '1';
    }, 150);
}

// Handle keyboard navigation
function handleModalKeydown(event) {
    switch(event.key) {
        case 'Escape':
            closePhotoModal();
            break;
        case 'ArrowLeft':
            navigatePhoto(-1);
            break;
        case 'ArrowRight':
            navigatePhoto(1);
            break;
    }
}

// Open all images modal (shows first image and allows navigation)
function openAllImagesModal() {
    if (allPhotoImages.length > 0) {
        openPhotoModal(allPhotoImages[0].src, allPhotoImages[0].alt);
    }
}

// Close modal when clicking outside the image
document.addEventListener('click', function(event) {
    const modal = document.getElementById('photoModal');
    if (event.target === modal) {
        closePhotoModal();
    }
});
</script>

<script>
// Booking form validation for tour details page
document.addEventListener('DOMContentLoaded', function() {
    const peopleSelect = document.getElementById('peopleSelect');
    const cabSelect = document.getElementById('cabSelect');
    const form = document.getElementById('quickBookingForm');
    const cabEnabled = <?php echo $cab_functionality_enabled ? 'true' : 'false'; ?>;
    
    function validateCabSelection() {
        if (!cabEnabled || !peopleSelect || !cabSelect) return true;
        
        const people = parseInt(peopleSelect.value);
        const selectedCab = cabSelect.options[cabSelect.selectedIndex];
        const maxPassengers = parseInt(selectedCab.dataset.maxPassengers) || 0;
        
        // Remove existing warnings
        const existingWarning = document.getElementById('cabWarningDetails');
        if (existingWarning) existingWarning.remove();
        
        if (cabSelect.value && people > maxPassengers) {
            const warning = document.createElement('div');
            warning.id = 'cabWarningDetails';
            warning.className = 'alert alert-warning mt-2';
            warning.innerHTML = '<small><i class="fas fa-exclamation-triangle me-1"></i>Selected cab can accommodate maximum ' + maxPassengers + ' passengers.</small>';
            cabSelect.parentNode.appendChild(warning);
            return false;
        }
        return true;
    }
    
    if (peopleSelect) {
        peopleSelect.addEventListener('change', validateCabSelection);
    }
    
    if (cabSelect) {
        cabSelect.addEventListener('change', validateCabSelection);
    }
    
    if (form) {
        form.addEventListener('submit', function(e) {
            if (cabEnabled && !validateCabSelection()) {
                e.preventDefault();
                alert('Please select an appropriate cab type for your group size.');
            }
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>
