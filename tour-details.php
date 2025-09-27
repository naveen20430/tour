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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
