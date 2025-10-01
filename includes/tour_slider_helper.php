<?php
/**
 * Tour Slider Helper Functions
 * Replaces hero section with dynamic tour slider
 */

/**
 * Enable tour slider CSS for the current page
 * Call this function before including header.php to load slider styles
 */
function enableTourSliderCSS() {
    global $extra_css;
    $extra_css = '<link rel="stylesheet" href="' . BASE_URL . 'assets/css/tour-slider.css">';
}

/**
 * Get featured tours for slider
 * @param int $limit Number of tours to fetch
 * @return array Tour data for slider
 */
function getTourSliderData($limit = 5) {
    global $db;
    
    try {
        // Get slider count from settings
        $slider_count_setting = $db->fetch("SELECT setting_value FROM site_settings WHERE setting_key = 'slider_slides_count'");
        if ($slider_count_setting) {
            $limit = intval($slider_count_setting['setting_value']);
        }
        
        // First try to get manually selected tours for slider
        $tours = $db->fetchAll("
            SELECT t.*, d.name as destination_name, d.country, d.city
            FROM tours t 
            LEFT JOIN destinations d ON t.destination_id = d.id 
            WHERE t.status = 'active' AND t.in_slider = 1
            ORDER BY t.slider_order ASC, t.featured DESC, t.popular DESC, t.created_at DESC 
            LIMIT ?
        ", [$limit]);
        
        // If no manually selected tours, get featured/popular tours
        if (empty($tours)) {
            $tours = $db->fetchAll("
                SELECT t.*, d.name as destination_name, d.country, d.city
                FROM tours t 
                LEFT JOIN destinations d ON t.destination_id = d.id 
                WHERE t.status = 'active' AND (t.featured = 1 OR t.popular = 1)
                ORDER BY t.featured DESC, t.popular DESC, t.created_at DESC 
                LIMIT ?
            ", [$limit]);
        }
        
        // If still no tours, get recent active tours
        if (empty($tours)) {
            $tours = $db->fetchAll("
                SELECT t.*, d.name as destination_name, d.country, d.city
                FROM tours t 
                LEFT JOIN destinations d ON t.destination_id = d.id 
                WHERE t.status = 'active'
                ORDER BY t.created_at DESC 
                LIMIT ?
            ", [$limit]);
        }
        
        return $tours;
    } catch (Exception $e) {
        // Return sample data if database error
        return getSampleTourData();
    }
}

/**
 * Get sample tour data for fallback
 * @return array Sample tour data
 */
function getSampleTourData() {
    return [
        [
            'id' => 1,
            'title' => 'Amazing Shimla Tour',
            'description' => 'Experience the beauty of Shimla with our premium tour package',
            'price' => 5000.00,
            'duration_days' => 3,
            'featured_image' => 'assets/images/tours/default.jpg',
            'destination_name' => 'Shimla',
            'country' => 'India'
        ],
        [
            'id' => 2,
            'title' => 'Manali Adventure',
            'description' => 'Thrilling adventure activities in the beautiful Manali',
            'price' => 7000.00,
            'duration_days' => 4,
            'featured_image' => 'assets/images/tours/default.jpg',
            'destination_name' => 'Manali',
            'country' => 'India'
        ],
        [
            'id' => 3,
            'title' => 'Dharamshala Retreat',
            'description' => 'Peaceful spiritual retreat in the mountains',
            'price' => 4500.00,
            'duration_days' => 3,
            'featured_image' => 'assets/images/tours/default.jpg',
            'destination_name' => 'Dharamshala',
            'country' => 'India'
        ]
    ];
}

/**
 * Render tour slider HTML
 * @param array $tours Tour data
 * @return string Tour slider HTML
 */
function renderTourSlider($tours) {
    if (empty($tours)) {
        return '';
    }
    
    global $db;
    
    // Get slider settings from database
    $slider_settings = [];
    try {
        $settings_result = $db->fetchAll("SELECT setting_key, setting_value FROM site_settings WHERE setting_key LIKE 'slider_%'");
        foreach ($settings_result as $setting) {
            $slider_settings[$setting['setting_key']] = $setting['setting_value'];
        }
    } catch (Exception $e) {
        // Use defaults if database error
    }
    
    // Set defaults
    $show_arrows = isset($slider_settings['slider_show_arrows']) ? ($slider_settings['slider_show_arrows'] == '1') : true;
    $show_dots = isset($slider_settings['slider_show_dots']) ? ($slider_settings['slider_show_dots'] == '1') : true;
    
    ob_start();
    ?>
    <!-- Tour Slider Section -->
    <section class="tour-slider-section">
        <div class="tour-slider-container">
            <div class="tour-slider-wrapper">
                <div class="owl-carousel owl-theme tour-slider" id="tourSlider">
                    <?php foreach ($tours as $tour): 
                        $image_path = !empty($tour['featured_image']) ? $tour['featured_image'] : 'assets/images/tours/default.jpg';
                        $price = isset($tour['price']) ? formatPriceINR($tour['price']) : 'Contact Us';
                        $duration = isset($tour['duration_days']) ? $tour['duration_days'] . ' Days' : '';
                        $destination = $tour['destination_name'] ?? $tour['city'] ?? 'Amazing Destination';
                    ?>
                    <div class="tour-slide-item">
                        <div class="tour-slide-bg" style="background-image: url('<?php echo BASE_URL . $image_path; ?>');">
                            <div class="tour-slide-overlay"></div>
                            <div class="tour-slide-content">
                                <div class="container">
                                    <div class="row align-items-center min-vh-100">
                                        <div class="col-lg-8 col-xl-7">
                                            <div class="tour-slide-text">
                                                <span class="tour-slide-category">
                                                    <i class="flaticon-pin-1"></i>
                                                    <?php echo htmlspecialchars($destination); ?>
                                                </span>
                                                
                                                <h1 class="tour-slide-title">
                                                    <?php echo htmlspecialchars($tour['title']); ?>
                                                </h1>
                                                
                                                <?php if (!empty($tour['description']) || !empty($tour['short_description'])): ?>
                                                <p class="tour-slide-description">
                                                    <?php 
                                                    $description = $tour['short_description'] ?? $tour['description'] ?? '';
                                                    echo htmlspecialchars(substr($description, 0, 150) . (strlen($description) > 150 ? '...' : ''));
                                                    ?>
                                                </p>
                                                <?php endif; ?>
                                                
                                                <div class="tour-slide-meta">
                                                    <?php if ($duration): ?>
                                                    <span class="tour-slide-duration">
                                                        <i class="flaticon-three-o-clock-clock"></i>
                                                        <?php echo $duration; ?>
                                                    </span>
                                                    <?php endif; ?>
                                                    
                                                    <span class="tour-slide-price">
                                                        <i class="flaticon-price-tag"></i>
                                                        Starting from <?php echo $price; ?>
                                                    </span>
                                                </div>
                                                
                                                <div class="tour-slide-buttons">
                                                    <a href="<?php echo BASE_URL; ?>tour-details.php?id=<?php echo $tour['id']; ?>" class="travhub-btn travhub-btn--primary">
                                                        <span>View Details</span>
                                                    </a>
                                                    <a href="<?php echo BASE_URL; ?>booking.php?tour_id=<?php echo $tour['id']; ?>" class="travhub-btn travhub-btn--outline">
                                                        <span>Book Now</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Navigation arrows -->
                <?php if ($show_arrows): ?>
                <div class="tour-slider-nav">
                    <div class="tour-slider-prev">
                        <i class="flaticon-arrow-2"></i>
                    </div>
                    <div class="tour-slider-next">
                        <i class="flaticon-arrow-2"></i>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Dots navigation -->
                <?php if ($show_dots): ?>
                <div class="tour-slider-dots">
                    <?php for($i = 0; $i < count($tours); $i++): ?>
                    <span class="tour-slider-dot <?php echo $i === 0 ? 'active' : ''; ?>" data-slide="<?php echo $i; ?>"></span>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

/**
 * Easy function to add tour slider to any page
 * Call this function after header to add tour slider section
 */
function displayTourSlider($limit = 5) {
    $tours = getTourSliderData($limit);
    echo renderTourSlider($tours);
}

/**
 * Initialize tour slider JavaScript
 * Call this function before closing body tag
 */
function initTourSliderJS() {
    global $db;
    
    // Get slider settings from database
    $slider_settings = [];
    try {
        $settings_result = $db->fetchAll("SELECT setting_key, setting_value FROM site_settings WHERE setting_key LIKE 'slider_%'");
        foreach ($settings_result as $setting) {
            $slider_settings[$setting['setting_key']] = $setting['setting_value'];
        }
    } catch (Exception $e) {
        // Use defaults if database error
    }
    
    // Set defaults
    $autoplay = isset($slider_settings['slider_autoplay']) ? ($slider_settings['slider_autoplay'] == '1') : true;
    $autoplay_speed = isset($slider_settings['slider_autoplay_speed']) ? intval($slider_settings['slider_autoplay_speed']) : 6000;
    $animation_speed = isset($slider_settings['slider_animation_speed']) ? intval($slider_settings['slider_animation_speed']) : 1000;
    $show_arrows = isset($slider_settings['slider_show_arrows']) ? ($slider_settings['slider_show_arrows'] == '1') : true;
    $show_dots = isset($slider_settings['slider_show_dots']) ? ($slider_settings['slider_show_dots'] == '1') : true;
    $pause_on_hover = isset($slider_settings['slider_pause_on_hover']) ? ($slider_settings['slider_pause_on_hover'] == '1') : true;
    
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Owl Carousel for tour slider
        if (typeof $.fn.owlCarousel !== 'undefined') {
            $('#tourSlider').owlCarousel({
                items: 1,
                loop: true,
                nav: false,
                dots: false,
                autoplay: <?php echo $autoplay ? 'true' : 'false'; ?>,
                autoplayTimeout: <?php echo $autoplay_speed; ?>,
                autoplayHoverPause: <?php echo $pause_on_hover ? 'true' : 'false'; ?>,
                animateOut: 'fadeOut',
                animateIn: 'fadeIn',
                smartSpeed: <?php echo $animation_speed; ?>,
                mouseDrag: true,
                touchDrag: true,
                responsive: {
                    0: { items: 1 },
                    768: { items: 1 },
                    1024: { items: 1 }
                }
            });
            
            // Custom navigation
            $('.tour-slider-next').click(function() {
                $('#tourSlider').trigger('next.owl.carousel');
            });
            
            $('.tour-slider-prev').click(function() {
                $('#tourSlider').trigger('prev.owl.carousel');
            });
            
            // Custom dots
            $('.tour-slider-dot').click(function() {
                var slideIndex = $(this).data('slide');
                $('#tourSlider').trigger('to.owl.carousel', [slideIndex]);
            });
            
            // Update dots on slide change
            $('#tourSlider').on('changed.owl.carousel', function(event) {
                var currentIndex = event.item.index - event.relatedTarget._clones.length / 2;
                $('.tour-slider-dot').removeClass('active');
                $('.tour-slider-dot').eq(currentIndex).addClass('active');
            });
        }
        
        // Parallax effect for slide backgrounds
        $(window).scroll(function() {
            var scrolled = $(window).scrollTop();
            var parallaxSpeed = 0.5;
            $('.tour-slide-bg').css('transform', 'translateY(' + (scrolled * parallaxSpeed) + 'px)');
        });
    });
    </script>
    <?php
}
?>