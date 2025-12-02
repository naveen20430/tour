<?php
require_once 'config/config.php';
require_once 'includes/tour_slider_helper.php';

// Set page variables
$page_title = getSetting('site_name') . ' || Travel & Tour Booking Agency';
$current_page = 'home';

// Enable tour slider CSS for this page (needed for carousel styling)
enableTourSliderCSS();

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

// Get popular destinations for home page (3 cards) - Shimla, Manali, and Other
$home_categories = $db->fetchAll("
    SELECT d.*, COUNT(t.id) as tour_count
    FROM destinations d
    LEFT JOIN tours t ON d.id = t.destination_id AND t.status = 'active'
    WHERE d.status = 'active' 
    AND d.popular = 1
    GROUP BY d.id
    HAVING tour_count >= 0
    ORDER BY d.created_at DESC
    LIMIT 3
");

// Include header
include 'includes/header.php';
?>

<!-- Destinations Section - Before Hero -->
<?php if (!empty($home_categories)): ?>
<section class="categories-section" style="background: #f8f9fa; padding: 20px 0; position: relative; overflow: hidden;">
    <div class="container">
        <div class="row">
            <?php foreach ($home_categories as $index => $destination): 
                // Get destination image or use default
                $destination_image = !empty($destination['featured_image']) ? BASE_URL . $destination['featured_image'] : BASE_URL . 'assets/images/destinations/default.jpg';
                
                // Tour count is already in the query result
                $count = $destination['tour_count'] ?? 0;
            ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="category-card" style="position: relative; overflow: hidden; height: 350px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1); transition: all 0.4s ease; cursor: pointer; background: #fff; border-radius: 0;">
                    <!-- Destination Image -->
                    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-image: url('<?php echo htmlspecialchars($destination_image); ?>'); background-size: cover; background-position: center; transition: transform 0.4s ease;">
                    </div>
                    
                    <!-- Dark Overlay -->
                    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.3) 50%, transparent 100%); z-index: 1;"></div>
                    
                    <!-- Destination Content -->
                    <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 30px; z-index: 2;">
                        <h3 style="color: white; font-size: 1.8rem; font-weight: 700; margin-bottom: 10px; text-shadow: 0 2px 10px rgba(0,0,0,0.5);">
                            <?php echo htmlspecialchars($destination['name']); ?>
                        </h3>
                        
                        <?php if (!empty($destination['short_description'])): ?>
                        <p style="color: rgba(255,255,255,0.9); font-size: 0.95rem; margin-bottom: 15px; text-shadow: 0 1px 5px rgba(0,0,0,0.5); line-height: 1.5;">
                            <?php echo htmlspecialchars(substr($destination['short_description'], 0, 80)); ?>...
                        </p>
                        <?php endif; ?>
                        
                        <!-- Listing Badge -->
                        <div style="text-align: center; margin-top: 15px;">
                            <span style="background: #764ba2; color: white; padding: 8px 20px; font-size: 0.9rem; font-weight: 600; display: inline-block; border-radius: 0;">
                                <?php echo $count; ?> Listing<?php echo $count != 1 ? 's' : ''; ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Hover Overlay -->
                    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(118, 75, 162, 0.9); opacity: 0; transition: all 0.3s ease; z-index: 3; display: flex; align-items: center; justify-content: center;">
                        <a href="<?php echo BASE_URL; ?>tours.php?destination=<?php echo htmlspecialchars($destination['slug']); ?>" 
                           style="color: white; text-decoration: none; font-weight: 700; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 1px;">
                            Explore Destination <i class="fas fa-arrow-right" style="margin-left: 10px;"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <style>
    .category-card:hover {
        transform: translateY(-10px) !important;
        box-shadow: 0 25px 50px rgba(118, 75, 162, 0.3) !important;
    }
    
    .category-card:hover > div:first-child {
        transform: scale(1.1) !important;
    }
    
    .category-card:hover > div:last-child {
        opacity: 1 !important;
    }
    
    @media (max-width: 768px) {
        .categories-section {
            padding: 20px 0 !important;
        }
        
        .category-card {
            height: 300px !important;
            margin-bottom: 20px !important;
        }
    }
    </style>
</section>
<?php endif; ?>

<!-- Search Section -->
<section class="search-section" style="background: linear-gradient(135deg, #764ba2 0%, #667eea 100%); padding: 20px 0; position: relative; overflow: hidden;">
    <!-- Background decorative elements -->
    <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255, 255, 255, 0.1); border-radius: 50%; animation: float 10s ease-in-out infinite;"></div>
    <div style="position: absolute; bottom: -100px; left: -100px; width: 300px; height: 300px; background: rgba(255, 255, 255, 0.05); border-radius: 50%; animation: float 12s ease-in-out infinite reverse;"></div>
    
    <div class="container" style="position: relative; z-index: 2;">
       
        
        <div class="search-form-wrapper" style="background: white; border-radius: 0; padding: 40px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); position: relative; z-index: 2;">
            <form id="tourSearchForm" onsubmit="return false;">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <div class="search-field" style="position: relative;">
                            <label for="country" style="display: block; font-weight: 600; color: #333; margin-bottom: 8px; font-size: 0.9rem;">
                                <i class="flaticon-earth" style="color: #764ba2; margin-right: 5px;"></i>Country
                            </label>
                            <select name="country" class="form-select" id="country" style="border: 2px solid #e9ecef; border-radius: 0; padding: 12px 15px; height: 50px; font-size: 1rem; transition: all 0.3s ease;">
                                <option value="">Select Country</option>
                                <?php 
                                $countries = $db->fetchAll("SELECT DISTINCT country FROM destinations WHERE status = 'active' AND country IS NOT NULL AND country != '' ORDER BY country");
                                foreach ($countries as $country): 
                                ?>
                                <option value="<?php echo htmlspecialchars($country['country']); ?>"><?php echo htmlspecialchars($country['country']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="search-field" style="position: relative;">
                            <label for="destination" style="display: block; font-weight: 600; color: #333; margin-bottom: 8px; font-size: 0.9rem;">
                                <i class="flaticon-pin-1" style="color: #764ba2; margin-right: 5px;"></i>Destination
                            </label>
                            <select name="destination" class="form-select" id="destination" style="border: 2px solid #e9ecef; border-radius: 0; padding: 12px 15px; height: 50px; font-size: 1rem; transition: all 0.3s ease;">
                                <option value="">Select Destination</option>
                                <?php 
                                $destinations = $db->fetchAll("SELECT slug, name FROM destinations WHERE status = 'active' ORDER BY name");
                                foreach ($destinations as $dest): 
                                ?>
                                <option value="<?php echo htmlspecialchars($dest['slug']); ?>"><?php echo htmlspecialchars($dest['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="search-field" style="position: relative;">
                            <label for="travel_date" style="display: block; font-weight: 600; color: #333; margin-bottom: 8px; font-size: 0.9rem;">
                                <i class="flaticon-calendar" style="color: #764ba2; margin-right: 5px;"></i>Travel Date
                            </label>
                            <input class="travhub-multi-datepicker form-control" id="travel_date" type="text" name="travel_date" placeholder="Select Date" style="border: 2px solid #e9ecef; border-radius: 0; padding: 12px 15px; height: 50px; font-size: 1rem; transition: all 0.3s ease;">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="search-field" style="position: relative;">
                            <label for="return_date" style="display: block; font-weight: 600; color: #333; margin-bottom: 8px; font-size: 0.9rem;">
                                <i class="flaticon-calendar" style="color: #764ba2; margin-right: 5px;"></i>Return Date
                            </label>
                            <input class="travhub-multi-datepicker form-control" id="return_date" type="text" name="return_date" placeholder="Select Date" style="border: 2px solid #e9ecef; border-radius: 0; padding: 12px 15px; height: 50px; font-size: 1rem; transition: all 0.3s ease;">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-12">
                        <div class="search-field" style="position: relative;">
                            <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; opacity: 0;">Button</label>
                            <button class="travhub-btn w-100" type="button" onclick="showPhoneModal()" style="background: #764ba2; color: white; border: none; border-radius: 0; padding: 12px 20px; height: 50px; font-weight: 600; font-size: 1rem; box-shadow: 0 4px 15px rgba(118, 75, 162, 0.4); transition: all 0.3s ease; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                                <span>Search</span>
                                <i class="flaticon-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <style>
    .search-section {
        position: relative;
    }
    
    .search-form-wrapper .form-select:focus,
    .search-form-wrapper .form-control:focus {
        border-color: #764ba2 !important;
        box-shadow: 0 0 0 0.2rem rgba(118, 75, 162, 0.25) !important;
        outline: none;
    }
    
    .search-form-wrapper .form-select:hover,
    .search-form-wrapper .form-control:hover {
        border-color: #764ba2;
    }
    
    .search-form-wrapper .travhub-btn:hover {
        background: #667eea !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(118, 75, 162, 0.6) !important;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }
    
    @media (max-width: 991px) {
        .search-section {
            padding: 20px 0 !important;
        }
        
        .search-form-wrapper {
            padding: 30px 20px !important;
        }
        
        .search-section h2 {
            font-size: 2rem !important;
        }
    }
    
    @media (max-width: 768px) {
        .search-section {
            padding: 20px 0 !important;
        }
        
        .search-form-wrapper {
            padding: 25px 15px !important;
        }
        
        .search-section h2 {
            font-size: 1.75rem !important;
        }
        
        .search-section p {
            font-size: 1rem !important;
        }
    }
    </style>
</section>

<!-- Phone Number Modal -->
<div id="phoneModal" class="modal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.6); overflow: auto;">
    <div class="modal-content" style="background-color: white; margin: 8% auto; padding: 0; border-radius: 20px; max-width: 500px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); animation: slideDown 0.3s ease;">
        <div class="modal-header" style="background: #1bbc9b; color: white; padding: 25px 30px; border-radius: 20px 20px 0 0; position: relative;">
            <h4 style="margin: 0; font-weight: 700;"><i class="flaticon-search"></i> Confirm Your Search</h4>
            <span onclick="closePhoneModal()" style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); font-size: 28px; font-weight: bold; color: white; cursor: pointer; line-height: 1;">&times;</span>
        </div>
        <div class="modal-body" style="padding: 30px;">
            <div style="background: #f8f9fa; padding: 20px; border-radius: 15px; margin-bottom: 25px;">
                <h6 style="color: #667eea; font-weight: 600; margin-bottom: 15px;">Your Search Details:</h6>
                <div id="searchDetails" style="font-size: 0.95rem; line-height: 1.8;"></div>
            </div>
            
            <form id="phoneForm" onsubmit="submitSearch(event)">
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; color: #333; margin-bottom: 10px; display: block;">
                        <i class="flaticon-phone-call"></i> Phone Number <span style="color: red;">*</span>
                    </label>
                    <input type="tel" name="phone" id="modalPhone" class="form-control" placeholder="Enter your 10-digit phone number" required
                           pattern="[0-9]{10}" maxlength="10"
                           style="border: 2px solid #e9ecef; border-radius: 10px; padding: 12px 15px; width: 100%; font-size: 1rem;" 
                           onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 0.2rem rgba(102, 126, 234, 0.25)'" 
                           onblur="this.style.borderColor='#e9ecef'; this.style.boxShadow='none'">
                    <small style="color: #6c757d; margin-top: 5px; display: block;">We'll use this to contact you about your tour inquiry</small>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 25px;">
                    <button type="button" onclick="closePhoneModal()" class="btn" 
                            style="flex: 1; background: #e9ecef; color: #495057; border: none; border-radius: 0; padding: 12px 20px; font-weight: 600; transition: all 0.3s ease;"
                            onmouseover="this.style.background='#dee2e6'" onmouseout="this.style.background='#e9ecef'">
                        Cancel
                    </button>
                    <button type="submit" class="btn" 
                            style="flex: 1; background: #764ba2; color: white; border: none; border-radius: 0; padding: 12px 20px; font-weight: 600; box-shadow: 0 4px 15px rgba(118, 75, 162, 0.4); transition: all 0.3s ease;"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(102, 126, 234, 0.6)'"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(102, 126, 234, 0.4)'">
                        <i class="flaticon-search"></i> Search Tours
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
@keyframes slideDown {
    from {
        transform: translateY(-50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Search Bar Responsive Styles */
@media (max-width: 768px) {
    .search-bar-section {
        margin-top: 0 !important;
        padding: 20px 0;
    }
    
    .search-bar-wrapper {
        padding: 25px 20px !important;
    }
    
    .search-bar-wrapper h3 {
        font-size: 1.5rem !important;
    }
}

@media (max-width: 991px) {
    .search-bar-section {
        margin-top: -20px !important;
    }
}

/* Hero Section Improvements */
.hero-one {
    position: relative;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 80px 0 120px;
    overflow: hidden;
}

.hero-one::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
    pointer-events: none;
    z-index: 1;
}

.hero-one .container {
    position: relative;
    z-index: 2;
}

/* Banner Form Responsive Improvements */
@media (max-width: 991px) {
    .hero-one__form .banner-form {
        flex-direction: column;
    }
    
    .hero-one__form .banner-form__control {
        width: 100% !important;
        margin-bottom: 15px;
    }
    
    .hero-one__form .banner-form__button {
        width: 100% !important;
    }
    
    .hero-one__form .banner-form__button button {
        width: 100%;
    }
}

@media (max-width: 768px) {
    .hero-one {
        padding: 60px 0 80px;
    }
    
    .hero-one__title {
        font-size: 2.5rem !important;
    }
    
    .hero-one__sub-title {
        font-size: 1.2rem !important;
    }
    
    .hero-one__text {
        font-size: 1rem !important;
    }
}

@media (max-width: 575px) {
    .hero-one {
        padding: 40px 0 60px;
    }
    
    .hero-one__title {
        font-size: 2rem !important;
    }
    
    .hero-one__sub-title {
        font-size: 1rem !important;
    }
    
    .hero-one__text {
        font-size: 0.9rem !important;
    }
}
</style>

<script>
function showPhoneModal() {
    // Get form values - handle both selectpicker and regular selects
    const destination = document.querySelector('select[name="destination"]');
    const destinationText = destination ? (destination.options[destination.selectedIndex]?.text || '') : '';
    const destinationValue = destination ? destination.value : '';
    
    const country = document.querySelector('select[name="country"]');
    const countryText = country ? (country.options[country.selectedIndex]?.text || '') : '';
    const countryValue = country ? country.value : '';
    
    // Handle datepicker inputs
    const travelDateInput = document.querySelector('input[name="travel_date"]');
    const travelDate = travelDateInput ? travelDateInput.value : '';
    
    const returnDateInput = document.querySelector('input[name="return_date"]');
    const returnDate = returnDateInput ? returnDateInput.value : '';
    
    // Build search details HTML
    let detailsHTML = '';
    if (destinationValue) detailsHTML += `<div><strong>Destination:</strong> ${destinationText}</div>`;
    if (countryValue) detailsHTML += `<div><strong>Country:</strong> ${countryText}</div>`;
    if (travelDate) detailsHTML += `<div><strong>Travel Date:</strong> ${travelDate}</div>`;
    if (returnDate) detailsHTML += `<div><strong>Return Date:</strong> ${returnDate}</div>`;
    
    if (!detailsHTML) {
        detailsHTML = '<div style="color: #6c757d; font-style: italic;">No search filters selected</div>';
    }
    
    document.getElementById('searchDetails').innerHTML = detailsHTML;
    document.getElementById('phoneModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closePhoneModal() {
    document.getElementById('phoneModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    document.getElementById('modalPhone').value = '';
}

function submitSearch(event) {
    event.preventDefault();
    
    const phone = document.getElementById('modalPhone').value;
    const phonePattern = /^[0-9]{10}$/;
    
    if (!phonePattern.test(phone)) {
        alert('Please enter a valid 10-digit phone number');
        return false;
    }
    
    // Get form data
    const formData = new FormData();
    const destination = document.querySelector('select[name="destination"]');
    const country = document.querySelector('select[name="country"]');
    const travelDateInput = document.querySelector('input[name="travel_date"]');
    const returnDateInput = document.querySelector('input[name="return_date"]');
    
    if (destination) formData.append('destination', destination.value);
    if (country) formData.append('country', country.value);
    if (travelDateInput) formData.append('travel_date', travelDateInput.value);
    if (returnDateInput) formData.append('return_date', returnDateInput.value);
    formData.append('phone', phone);
    
    // Save to database
    fetch('<?php echo BASE_URL; ?>api/save-search-query.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // Log raw response for debugging
        return response.text().then(text => {
            console.log('Raw Response:', text);
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('JSON Parse Error:', e);
                throw new Error('Server returned invalid JSON: ' + text.substring(0, 200));
            }
        });
    })
    .then(data => {
        console.log('API Response:', data);
        if (data.success) {
            // Redirect to tours page with search parameters
            const params = new URLSearchParams();
            formData.forEach((value, key) => {
                if (value && key !== 'phone') params.append(key, value);
            });
            window.location.href = '<?php echo navUrl('tours'); ?>?' + params.toString();
        } else {
            console.error('API Error:', data);
            alert('Error: ' + (data.message || 'Failed to save search query. Please try again.'));
        }
    })
    .catch(error => {
        console.error('Network Error:', error);
        alert('Network error. Please check your connection and try again.');
    });
    
    return false;
}

// Close modal on outside click
window.onclick = function(event) {
    const modal = document.getElementById('phoneModal');
    if (event.target == modal) {
        closePhoneModal();
    }
}

// Close modal on ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closePhoneModal();
    }
});
</script>

<?php include 'includes/destinations_cab_section.php'; ?>

    <section class="about-one section-space" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); position: relative; overflow: hidden;">
        <!-- Floating elements for visual appeal -->
        <div style="position: absolute; top: 20%; left: 10%; width: 100px; height: 100px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); border-radius: 50%; animation: float 6s ease-in-out infinite;"></div>
        <div style="position: absolute; bottom: 20%; right: 15%; width: 150px; height: 150px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%); border-radius: 50%; animation: float 8s ease-in-out infinite reverse;"></div>
        
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center scroll-reveal" style="z-index: 2; position: relative;">
                        <div style="margin-bottom: 30px;">
                            <span class="badge bg-primary" style="padding: 8px 20px; font-size: 0.9rem; border-radius: 25px; background: #1bbc9b !important;">✈️ Premium Travel Experience</span>
                        </div>
                        
                        <h2 class="gradient-text" style="font-size: 3.5rem; font-weight: 700; margin-bottom: 20px; line-height: 1.2;">Welcome to <?php echo getSetting('site_name'); ?></h2>
                        
                        <p class="lead" style="font-size: 1.5rem; color: #6c757d; font-weight: 400; margin-bottom: 15px;">Your Adventure Starts Here</p>
                        
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
                                <div class="glass-effect" style="padding: 20px; border-radius: 15px; text-align: center;">
                                    <h3 class="gradient-text" style="font-size: 2rem; margin-bottom: 5px;">500+</h3>
                                    <p style="margin: 0; color: #6c757d;">Happy Travelers</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="glass-effect" style="padding: 20px; border-radius: 15px; text-align: center;">
                                    <h3 class="gradient-text" style="font-size: 2rem; margin-bottom: 5px;">50+</h3>
                                    <p style="margin: 0; color: #6c757d;">Destinations</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="glass-effect" style="padding: 20px; border-radius: 15px; text-align: center;">
                                    <h3 class="gradient-text" style="font-size: 2rem; margin-bottom: 5px;">24/7</h3>
                                    <p style="margin: 0; color: #6c757d;">Support</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        /* Homepage specific enhancements */
        .card:hover .card-img-top {
            transform: scale(1.05) !important;
        }
        
        .card:hover {
            transform: translateY(-8px) scale(1.02) !important;
            box-shadow: 0 25px 50px rgba(102, 126, 234, 0.15), 0 0 0 1px rgba(102, 126, 234, 0.1) !important;
        }
        
        /* Light hover overlay animations */
        .card:hover > div:nth-child(2) {
            opacity: 1 !important;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%) !important;
        }
        
        /* Button hover enhancements */
        .btn:hover, .travhub-btn:hover {
            transform: translateY(-2px) scale(1.02) !important;
        }
        
        /* Glass effect hover */
        .glass-effect:hover {
            background: rgba(255, 255, 255, 0.2) !important;
            transform: translateY(-5px) !important;
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.2) !important;
        }
        
        /* Responsive improvements */
        @media (max-width: 991px) {
            .gradient-text {
                font-size: 2.5rem !important;
            }
            
            .section-title h2 {
                font-size: 2rem !important;
            }
        }
        
        @media (max-width: 768px) {
            .gradient-text {
                font-size: 2rem !important;
            }
            
            .section-space {
                padding: 50px 0 !important;
            }
            
            .card {
                margin-bottom: 25px !important;
            }
            
            /* Adjust button spacing in mobile */
            .mt-4 {
                margin-top: 2rem !important;
            }
            
            .travhub-btn {
                margin: 5px 0;
                display: inline-block;
                width: auto;
                min-width: 140px;
            }
        }
        
        @media (max-width: 575px) {
            .gradient-text {
                font-size: 1.6rem !important;
            }
            
            .section-space {
                padding: 40px 0 !important;
            }
            
            .card {
                margin-bottom: 20px !important;
            }
            
            .card-body {
                padding: 15px !important;
            }
            
            .card-title {
                font-size: 1.1rem !important;
            }
            
            .card-text {
                font-size: 0.9rem !important;
            }
            
            /* Mobile hero adjustments */
            .lead {
                font-size: 1.1rem !important;
            }
            
            /* Stats section mobile */
            .glass-effect {
                padding: 15px !important;
                margin-bottom: 15px !important;
            }
            
            /* Button improvements */
            .travhub-btn {
                padding: 10px 20px !important;
                font-size: 0.9rem !important;
                min-width: 120px;
                margin: 8px 5px;
            }
            
            /* Two column layout for buttons on very small screens */
            .mt-4 {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 10px;
            }
        }
        </style>
        
        <!-- Homepage Specific JavaScript -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add hover effects to cards
            const cards = document.querySelectorAll('.card');
            
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    // Add enhanced glow effect
                    this.style.boxShadow = '0 25px 50px rgba(102, 126, 234, 0.15), 0 0 0 1px rgba(102, 126, 234, 0.1)';
                    
                    // Show subtle hover overlay
                    const overlay = this.querySelector('div[style*="opacity: 0"]');
                    if (overlay) {
                        overlay.style.opacity = '1';
                        overlay.style.background = 'linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%)';
                    }
                    
                    // Add slight scale effect
                    this.style.transform = 'translateY(-8px) scale(1.02)';
                });
                
                card.addEventListener('mouseleave', function() {
                    // Reset styles
                    this.style.boxShadow = '0 15px 35px rgba(0, 0, 0, 0.1)';
                    this.style.transform = 'translateY(0) scale(1)';
                    
                    // Hide hover overlay
                    const overlay = this.querySelector('div[style*="opacity: 1"]');
                    if (overlay && overlay.style.background.includes('rgba(102, 126, 234')) {
                        overlay.style.opacity = '0';
                        overlay.style.background = 'linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%)';
                    }
                });
            });
            
            // Stats counter animation
            const counters = document.querySelectorAll('.gradient-text');
            const observerOptions = { threshold: 0.7 };
            
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const counter = entry.target;
                        const text = counter.textContent;
                        if (text.includes('+')) {
                            const number = parseInt(text);
                            if (number > 0) {
                                animateCounter(counter, 0, number, 1500);
                            }
                        }
                        observer.unobserve(counter);
                    }
                });
            }, observerOptions);
            
            counters.forEach(counter => {
                if (counter.textContent.includes('+')) {
                    observer.observe(counter);
                }
            });
            
            function animateCounter(element, start, end, duration) {
                const startTime = performance.now();
                const suffix = element.textContent.match(/\+|\w+/g)?.slice(1).join(' ') || '';
                
                function updateCounter(currentTime) {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    const current = Math.floor(progress * (end - start) + start);
                    element.textContent = current + '+' + (suffix ? ' ' + suffix : '');
                    
                    if (progress < 1) {
                        requestAnimationFrame(updateCounter);
                    }
                }
                
                requestAnimationFrame(updateCounter);
            }
            
            // Add parallax effect to floating elements
            window.addEventListener('scroll', function() {
                const scrolled = window.pageYOffset;
                const floatingElements = document.querySelectorAll('[style*="animation: float"]');
                
                floatingElements.forEach((el, index) => {
                    const speed = 0.5 + (index * 0.2);
                    el.style.transform = `translateY(${scrolled * speed * -0.1}px)`;
                });
            });
        });
        </script>
    </section>

    <!-- Tour Carousel Section - 3 slides at a time -->
    <?php displayTourCarousel(9); ?>

    <!-- Popular Destinations -->
    <?php if (!empty($popular_destinations)): ?>
    <section class="destinations-one section-space" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 50%, #f8f9fa 100%); position: relative; overflow: hidden;">
        <!-- Background decorative elements -->
        <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%); border-radius: 50%; animation: float 10s ease-in-out infinite;"></div>
        <div style="position: absolute; bottom: -100px; left: -100px; width: 300px; height: 300px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.03) 0%, rgba(118, 75, 162, 0.03) 100%); border-radius: 50%; animation: float 12s ease-in-out infinite reverse;"></div>
        
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
            
            <div class="row mt-5">
                <?php foreach ($popular_destinations as $index => $destination): ?>
                    <div class="col-lg-3 col-md-6 mb-5 scroll-reveal" style="transition-delay: <?php echo $index * 0.15; ?>s; position: relative; z-index: 2;">
                        <div class="card h-100" style="border: none; border-radius: 0; overflow: hidden; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1); transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); position: relative; background: #fff;">
                            <!-- Hover overlay -->
                            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, rgba(40, 167, 69, 0.9) 0%, rgba(32, 201, 151, 0.9) 100%); opacity: 0; transition: all 0.3s ease; z-index: 1; border-radius: 0; display: flex; align-items: center; justify-content: center;">
                                <div style="text-align: center; color: white; transform: translateY(20px); transition: all 0.3s ease;">
                                    <i class="fas fa-plane" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
                                    <p style="font-weight: 600; margin: 0;">Explore Destination</p>
                                </div>
                            </div>
                            
                            <div class="position-relative" style="overflow: hidden;">
                                <img src="<?php echo $destination['featured_image'] ?: 'assets/images/destinations/default.jpg'; ?>" 
                                     class="card-img-top" style="height: 250px; object-fit: cover; transition: transform 0.4s ease;" 
                                     alt="<?php echo htmlspecialchars($destination['name']); ?>">
                                
                                <!-- Gradient overlay -->
                                <div style="position: absolute; bottom: 0; start: 0; end: 0; padding: 25px; background: linear-gradient(transparent, rgba(0,0,0,0.8)); z-index: 2;">
                                    <div style="display: flex; justify-content: space-between; align-items: end;">
                                        <div>
                                            <h6 class="text-white mb-1" style="font-weight: 700; font-size: 1.2rem; text-shadow: 0 2px 10px rgba(0,0,0,0.5);"><?php echo htmlspecialchars($destination['name']); ?></h6>
                                            <small class="text-light" style="font-size: 0.9rem; opacity: 0.9;">🌍 <?php echo htmlspecialchars($destination['country']); ?></small>
                                        </div>
                                        <div>
                                            <span class="badge" style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); color: white; padding: 5px 10px; border-radius: 15px; font-size: 0.75rem;">
                                                📍 Popular
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-body" style="padding: 25px; position: relative; z-index: 2;">
                                <p class="card-text" style="color: #6c757d; font-size: 0.95rem; line-height: 1.6; margin-bottom: 20px; height: 60px; overflow: hidden;">
                                    <?php echo substr(htmlspecialchars($destination['short_description']), 0, 85); ?>...
                                </p>
                                
                                <a href="<?php echo toursUrl(['destination' => $destination['slug']]); ?>" class="btn w-100" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border: none; border-radius: 0; padding: 12px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);">
                                    🗺️ Explore Tours
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
