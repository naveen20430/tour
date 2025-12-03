<?php
require_once 'config/config.php';
require_once 'includes/tour_slider_helper.php';

// Load function files
require_once 'app/functions/destination_functions.php';
require_once 'app/functions/tour_functions.php';

// Set page variables
$page_title = getSetting('site_name') . ' || Travel & Tour Booking Agency';
$current_page = 'home';

// Enable tour slider CSS for this page (needed for carousel styling)
enableTourSliderCSS();

// Add page-specific CSS and JS
$extra_css = '<link rel="stylesheet" href="' . BASE_URL . 'assets/css/index.css" />';
$extra_js = '<script>window.BASE_URL = "' . BASE_URL . '";</script><script src="' . BASE_URL . 'assets/js/index.js"></script>';

// Get featured tours using function
$featured_tours = getTours([
    'featured' => true,
    'limit' => 6,
    'order_by' => 't.created_at DESC'
]);

// Get popular destinations using function
$popular_destinations = getDestinations([
    'popular' => true,
    'limit' => 4,
    'order_by' => 'd.created_at DESC'
]);

// Get popular destinations for home page (3 cards)
$home_categories = getPopularDestinationsForHome(3);

// Get countries for search dropdown
$countries = getDestinationCountries();

// Get all destinations for search dropdown
$all_destinations = getDestinations([
    'order_by' => 'd.name ASC'
]);

// Include header
include 'includes/header.php';
?>

<!-- Destinations Section - Before Hero -->


<!-- Search Section -->
<section class="search-section">
    <!-- Background decorative elements -->
    <div class="decorative-element decorative-element-1"></div>
    <div class="decorative-element decorative-element-2"></div>
    
    <div class="container">
        <div class="search-form-wrapper">
            <form id="tourSearchForm" onsubmit="return false;">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <div class="search-field">
                            <label for="country">
                                <i class="flaticon-earth"></i>Country
                            </label>
                            <select name="country" class="form-select" id="country">
                                <option value="">Select Country</option>
                                <?php foreach ($countries as $country): ?>
                                <option value="<?php echo htmlspecialchars($country['country']); ?>"><?php echo htmlspecialchars($country['country']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="search-field">
                            <label for="destination">
                                <i class="flaticon-pin-1"></i>Destination
                            </label>
                            <select name="destination" class="form-select" id="destination">
                                <option value="">Select Destination</option>
                                <?php foreach ($all_destinations as $dest): ?>
                                <option value="<?php echo htmlspecialchars($dest['slug']); ?>"><?php echo htmlspecialchars($dest['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="search-field">
                            <label for="travel_date">
                                <i class="flaticon-calendar"></i>Travel Date
                            </label>
                            <input class="travhub-multi-datepicker form-control" id="travel_date" type="text" name="travel_date" placeholder="Select Date">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="search-field">
                            <label for="return_date">
                                <i class="flaticon-calendar"></i>Return Date
                            </label>
                            <input class="travhub-multi-datepicker form-control" id="return_date" type="text" name="return_date" placeholder="Select Date">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-12">
                        <div class="search-field">
                            <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; opacity: 0;">Button</label>
                            <button class="travhub-btn w-100" type="button" onclick="showPhoneModal()">
                                <span>Search</span>
                                <i class="flaticon-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Phone Number Modal -->
<div id="phoneModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4><i class="flaticon-search"></i> Confirm Your Search</h4>
            <span class="modal-close" onclick="closePhoneModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div class="search-details">
                <h6 style="color: #667eea; font-weight: 600; margin-bottom: 15px;">Your Search Details:</h6>
                <div id="searchDetails"></div>
            </div>
            
            <form id="phoneForm" onsubmit="submitSearch(event)">
                <div class="form-group">
                    <label class="form-label">
                        <i class="flaticon-phone-call"></i> Phone Number <span style="color: red;">*</span>
                    </label>
                    <input type="tel" name="phone" id="modalPhone" class="form-control" placeholder="Enter your 10-digit phone number" required
                           pattern="[0-9]{10}" maxlength="10">
                    <small style="color: #6c757d; margin-top: 5px; display: block;">We'll use this to contact you about your tour inquiry</small>
                </div>
                
                <div class="modal-actions">
                    <button type="button" onclick="closePhoneModal()" class="modal-btn modal-btn-cancel">
                        Cancel
                    </button>
                    <button type="submit" class="modal-btn modal-btn-submit">
                        <i class="flaticon-search"></i> Search Tours
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/destinations_cab_section.php'; ?>

<section class="about-one section-space">
    <!-- Floating elements for visual appeal -->
    <div class="floating-element floating-element-1"></div>
    <div class="floating-element floating-element-2"></div>
    
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="text-center scroll-reveal">
                    <div style="margin-bottom: 30px;">
                        <span class="badge bg-primary">✈️ Premium Travel Experience</span>
                    </div>
                    
                    <h2 class="gradient-text">Welcome to <?php echo getSetting('site_name'); ?></h2>
                    
                    <p class="lead">Your Adventure Starts Here</p>
                    
                    <p style="font-size: 1.1rem; color: #495057; max-width: 600px; margin: 0 auto 40px; line-height: 1.6;">Experience the world like never before with our carefully curated travel packages. From exotic destinations to cultural experiences, we make your travel dreams come true.</p>
                    
                    <div class="mt-4" style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                        <a href="<?php echo adminUrl('login'); ?>" class="travhub-btn" style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important; box-shadow: 0 4px 15px rgba(108, 117, 125, 0.4) !important;">
                            <span>🔐 Admin Panel</span>
                        </a>
                        <a href="<?php echo navUrl('tours'); ?>" class="travhub-btn">
                            <span>🌟 Explore Tours</span>
                        </a>
                    </div>
                    
                    <!-- Stats section -->
                    <div class="row mt-5">
                        <div class="col-md-4 mb-3">
                            <div class="glass-effect">
                                <h3 class="gradient-text" style="font-size: 2rem; margin-bottom: 5px;">500+</h3>
                                <p style="margin: 0; color: #6c757d;">Happy Travelers</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="glass-effect">
                                <h3 class="gradient-text" style="font-size: 2rem; margin-bottom: 5px;">50+</h3>
                                <p style="margin: 0; color: #6c757d;">Destinations</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="glass-effect">
                                <h3 class="gradient-text" style="font-size: 2rem; margin-bottom: 5px;">24/7</h3>
                                <p style="margin: 0; color: #6c757d;">Support</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Tour Carousel Section - 3 slides at a time -->
<?php displayTourCarousel(9); ?>
<?php if (!empty($home_categories)): ?>
<section class="categories-section">
    <div class="container">
    <div class="section-title text-center scroll-reveal" style="margin-bottom: 60px; position: relative; z-index: 2;">
            <div style="margin-bottom: 15px;">
                <span class="badge" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 8px 16px; border-radius: 20px; font-size: 0.9rem; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);">
                    🌍 Explore The World
                </span>
            </div>
            <h2 class="gradient-text" style="font-size: 2.8rem; font-weight: 700; margin-bottom: 20px;">Popular Destinations</h2>
            <p style="font-size: 1.1rem; color: #6c757d; max-width: 500px; margin: 0 auto; line-height: 1.6;">
                Discover the most sought-after travel destinations around the globe
            </p>
            <div style="width: 80px; height: 4px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); margin: 20px auto 0; border-radius: 2px;"></div>
        </div>
        <div class="row">
            <?php foreach ($home_categories as $index => $destination): 
                $destination_image = getDestinationImageUrl($destination);
                $count = $destination['tour_count'] ?? 0;
            ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="category-card">
                    <!-- Destination Image -->
                    <div class="destination-image" style="background-image: url('<?php echo htmlspecialchars($destination_image); ?>');"></div>
                    
                    <!-- Dark Overlay -->
                    <div class="dark-overlay"></div>
                    
                    <!-- Destination Content -->
                    <div class="destination-content">
                        <h3><?php echo htmlspecialchars($destination['name']); ?></h3>
                        
                        <?php if (!empty($destination['short_description'])): ?>
                        <p><?php echo htmlspecialchars(substr($destination['short_description'], 0, 80)); ?>...</p>
                        <?php endif; ?>
                        
                        <!-- Listing Badge -->
                        <div style="text-align: center; margin-top: 15px;">
                            <span class="listing-badge">
                                <?php echo $count; ?> Listing<?php echo $count != 1 ? 's' : ''; ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Hover Overlay -->
                    <div class="hover-overlay">
                        <a href="<?php echo BASE_URL; ?>tours.php?destination=<?php echo htmlspecialchars($destination['slug']); ?>">
                            Explore Destination <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>


<?php initTourSliderJS(); ?>

<?php include 'includes/footer.php'; ?>
