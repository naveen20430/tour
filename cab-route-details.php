<?php
require_once 'config/config.php';
require_once 'includes/header.php';

// Get route ID from URL
$route_id = $_GET['route_id'] ?? null;

if (!$route_id) {
    header('Location: index.php');
    exit;
}

// Fetch route details
$route = $db->fetch("SELECT * FROM cab_routes WHERE id = ? AND status = 'active'", [$route_id]);

if (!$route) {
    header('Location: index.php');
    exit;
}

// Fetch all cab pricing options for this route
$pricing_options = $db->fetchAll("
    SELECT crp.*, ct.display_name, ct.description as cab_description, ct.features, ct.max_passengers,
           COALESCE(crp.max_persons, ct.max_passengers) as max_persons
    FROM cab_route_pricing crp
    INNER JOIN cab_types ct ON crp.cab_type_id = ct.id
    WHERE crp.route_id = ? AND crp.status = 'active' AND ct.status = 'active'
    ORDER BY crp.one_way_price ASC
", [$route_id]);

// Handle booking form submission
$errors = [];
$success = false;
$booking_number = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guest_name = trim($_POST['guest_name'] ?? '');
    $guest_email = trim($_POST['guest_email'] ?? '');
    $guest_phone = trim($_POST['guest_phone'] ?? '');
    $travel_date = $_POST['travel_date'] ?? '';
    $trip_type = $_POST['trip_type'] ?? '';
    $selected_pricing_id = $_POST['pricing_id'] ?? '';
    $num_passengers = intval($_POST['num_passengers'] ?? 0);
    $special_requirements = trim($_POST['special_requirements'] ?? '');
    
    // Validation
    if (empty($guest_name)) $errors[] = 'Your name is required';
    if (empty($guest_email)) $errors[] = 'Your email is required';
    if (empty($guest_phone)) $errors[] = 'Your phone number is required';
    if (empty($travel_date)) $errors[] = 'Travel date is required';
    if (empty($trip_type)) $errors[] = 'Trip type is required';
    if (empty($selected_pricing_id)) $errors[] = 'Please select a cab type';
    if ($num_passengers < 1) $errors[] = 'Number of passengers is required';
    
    if (empty($errors)) {
        // Get selected pricing details
        $selected_pricing = $db->fetch("
            SELECT crp.*, ct.display_name, ct.max_passengers
            FROM cab_route_pricing crp
            INNER JOIN cab_types ct ON crp.cab_type_id = ct.id
            WHERE crp.id = ? AND crp.route_id = ?
        ", [$selected_pricing_id, $route_id]);
        
        if ($selected_pricing) {
            // Check passenger capacity
            if ($num_passengers > $selected_pricing['max_passengers']) {
                $errors[] = 'Selected cab can only accommodate ' . $selected_pricing['max_passengers'] . ' passengers';
            } else {
                // Calculate price based on trip type
                $trip_price = ($trip_type === 'one_way') 
                    ? $selected_pricing['one_way_price'] 
                    : $selected_pricing['round_trip_price'];
                
                // Generate booking number
                $booking_number = 'CR' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                
                try {
                    // Insert booking
                    $booking_id = $db->execute("
                        INSERT INTO cab_bookings 
                        (booking_number, route_id, pricing_id, customer_name, customer_email, 
                         customer_phone, travel_date, trip_type, num_passengers, 
                         total_price, special_requirements, status, created_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
                    ", [
                        $booking_number,
                        $route_id,
                        $selected_pricing_id,
                        $guest_name,
                        $guest_email,
                        $guest_phone,
                        $travel_date,
                        $trip_type,
                        $num_passengers,
                        $trip_price,
                        $special_requirements
                    ]);
                    
                    if ($booking_id) {
                        $success = true;
                    } else {
                        $errors[] = 'Failed to create booking. Please try again.';
                    }
                } catch (Exception $e) {
                    $errors[] = 'Database error: ' . $e->getMessage();
                }
            }
        } else {
            $errors[] = 'Invalid cab type selection';
        }
    }
}
?>

<style>
    .route-hero {
        background: #1bbc9b;
        color: white;
        padding: 60px 0;
        margin-bottom: 40px;
    }
    .route-info-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        padding: 30px;
        margin-bottom: 30px;
    }
    .pricing-card {
        border: 2px solid #e0e0e0;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
    }
    .pricing-card:hover {
        border-color: #667eea;
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        transform: translateY(-2px);
    }
    .pricing-card.selected {
        border-color: #667eea;
        background: #f0f4ff;
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }
    .pricing-card input[type="radio"] {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 20px;
        height: 20px;
        cursor: pointer;
    }
    .trip-type-selector {
        display: flex;
        gap: 15px;
        margin-bottom: 25px;
    }
    .trip-type-option {
        flex: 1;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        padding: 15px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .trip-type-option:hover {
        border-color: #667eea;
    }
    .trip-type-option.active {
        border-color: #667eea;
        background: #f0f4ff;
    }
    .trip-type-option input[type="radio"] {
        display: none;
    }
    .badge-location {
        background: rgba(255,255,255,0.2);
        padding: 8px 15px;
        border-radius: 20px;
        display: inline-block;
        margin: 0 5px;
    }
    .booking-section {
        background: #f8f9fa;
        padding: 40px 0;
    }
</style>

<?php if ($success): ?>
    <div class="container my-5">
        <div class="route-info-card text-center">
            <i class="fas fa-check-circle text-success" style="font-size: 4em; margin-bottom: 20px;"></i>
            <h2 class="text-success mb-4">Booking Request Submitted!</h2>
            <div class="alert alert-success">
                <h4>Booking Number: <strong><?php echo htmlspecialchars($booking_number); ?></strong></h4>
                <p class="mb-0">Thank you for your booking! Our team will contact you within 24 hours to confirm your booking and provide payment details.</p>
            </div>
            <div class="mt-4">
                <a href="index.php" class="btn btn-primary me-3">Back to Home</a>
                <a href="cab-route-details.php?route_id=<?php echo $route_id; ?>" class="btn btn-outline-primary">Book Another Cab</a>
            </div>
        </div>
    </div>
<?php else: ?>

<!-- Route Hero Section -->
<div class="route-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                    <span class="badge-location"><?php echo htmlspecialchars($route['from_location']); ?></span>
                    <i class="fas fa-arrow-right"></i>
                    <span class="badge-location"><?php echo htmlspecialchars($route['to_location']); ?></span>
                </div>
                <h1 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 15px;">
                    <?php echo htmlspecialchars($route['route_name']); ?>
                </h1>
                <?php if (!empty($route['description'])): ?>
                    <p style="font-size: 1.1rem; opacity: 0.9;">
                        <?php echo htmlspecialchars($route['description']); ?>
                    </p>
                <?php endif; ?>
            </div>
            <div class="col-md-4 text-md-end">
                <?php if ($route['distance_km'] > 0): ?>
                    <div style="font-size: 1.2rem; margin-bottom: 10px;">
                        <i class="fas fa-road"></i> <?php echo $route['distance_km']; ?> km
                    </div>
                <?php endif; ?>
                <?php if (!empty($route['estimated_duration'])): ?>
                    <div style="font-size: 1.2rem;">
                        <i class="fas fa-clock"></i> <?php echo htmlspecialchars($route['estimated_duration']); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="booking-section">
    <div class="container">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" id="bookingForm">
            <div class="row">
                <!-- Left Column - Booking Form -->
                <div class="col-lg-7">
                    <div class="route-info-card">
                        <h3 style="color: #333; margin-bottom: 25px;">
                            <i class="fas fa-user"></i> Your Details
                        </h3>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name *</label>
                                <input type="text" name="guest_name" class="form-control" required 
                                       value="<?php echo htmlspecialchars($_POST['guest_name'] ?? ''); ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number *</label>
                                <input type="tel" name="guest_phone" class="form-control" required 
                                       value="<?php echo htmlspecialchars($_POST['guest_phone'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address *</label>
                            <input type="email" name="guest_email" class="form-control" required 
                                   value="<?php echo htmlspecialchars($_POST['guest_email'] ?? ''); ?>">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Travel Date *</label>
                                <input type="date" name="travel_date" class="form-control" required 
                                       min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                                       value="<?php echo htmlspecialchars($_POST['travel_date'] ?? ''); ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Number of Passengers *</label>
                                <input type="number" name="num_passengers" id="numPassengers" class="form-control" 
                                       min="1" max="15" required 
                                       value="<?php echo htmlspecialchars($_POST['num_passengers'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Special Requirements</label>
                            <textarea name="special_requirements" class="form-control" rows="3" 
                                      placeholder="Any special requests or notes..."><?php echo htmlspecialchars($_POST['special_requirements'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Cab Selection -->
                <div class="col-lg-5">
                    <div class="route-info-card">
                        <h3 style="color: #333; margin-bottom: 25px;">
                            <i class="fas fa-car"></i> Select Cab & Trip Type
                        </h3>

                        <!-- Trip Type Selection -->
                        <label class="form-label">Trip Type *</label>
                        <div class="trip-type-selector">
                            <label class="trip-type-option <?php echo ($_POST['trip_type'] ?? '') === 'one_way' ? 'active' : ''; ?>">
                                <input type="radio" name="trip_type" value="one_way" required 
                                       <?php echo ($_POST['trip_type'] ?? '') === 'one_way' ? 'checked' : ''; ?>>
                                <div style="font-weight: 600; font-size: 1.1rem;">
                                    <i class="fas fa-arrow-right"></i> One Way
                                </div>
                            </label>
                            <label class="trip-type-option <?php echo ($_POST['trip_type'] ?? '') === 'round_trip' ? 'active' : ''; ?>">
                                <input type="radio" name="trip_type" value="round_trip" required 
                                       <?php echo ($_POST['trip_type'] ?? '') === 'round_trip' ? 'checked' : ''; ?>>
                                <div style="font-weight: 600; font-size: 1.1rem;">
                                    <i class="fas fa-arrows-alt-h"></i> Round Trip
                                </div>
                            </label>
                        </div>

                        <!-- Cab Options -->
                        <label class="form-label mt-3">Select Cab *</label>
                        <div id="cabPricingOptions">
                            <?php foreach ($pricing_options as $pricing): 
                                $features = json_decode($pricing['features'], true) ?: [];
                            ?>
                                <div class="pricing-card <?php echo ($_POST['pricing_id'] ?? '') == $pricing['id'] ? 'selected' : ''; ?>" 
                                     data-pricing-id="<?php echo $pricing['id']; ?>"
                                     data-max-passengers="<?php echo $pricing['max_persons']; ?>"
                                     data-one-way="<?php echo $pricing['one_way_price']; ?>"
                                     data-round-trip="<?php echo $pricing['round_trip_price']; ?>">
                                    
                                    <input type="radio" name="pricing_id" value="<?php echo $pricing['id']; ?>" 
                                           <?php echo ($_POST['pricing_id'] ?? '') == $pricing['id'] ? 'checked' : ''; ?>>
                                    
                                    <h5 style="margin-bottom: 8px; font-weight: 600;">
                                        <?php echo htmlspecialchars($pricing['display_name']); ?>
                                    </h5>
                                    
                                    <p style="color: #6c757d; font-size: 0.9rem; margin-bottom: 10px;">
                                        <?php echo htmlspecialchars($pricing['cab_description']); ?>
                                    </p>
                                    
                                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px; font-size: 0.85rem; color: #6c757d;">
                                        <span>
                                            <i class="fas fa-users" style="color: #667eea;"></i> 
                                            Up to <?php echo $pricing['max_persons']; ?> passengers
                                        </span>
                                    </div>
                                    
                                    <?php if (!empty($features)): ?>
                                        <div style="margin-bottom: 12px;">
                                            <?php foreach (array_slice($features, 0, 3) as $feature): ?>
                                                <span class="badge bg-secondary me-1" style="font-size: 0.7rem;">
                                                    <?php echo htmlspecialchars($feature); ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 12px; border-top: 1px solid #e0e0e0;">
                                        <div>
                                            <div style="font-size: 0.75rem; color: #667eea; text-transform: uppercase;">One Way</div>
                                            <div style="font-size: 1.3rem; font-weight: 700; color: #667eea;" class="one-way-price">
                                                <?php echo formatPriceINR($pricing['one_way_price']); ?>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div style="font-size: 0.75rem; color: #667eea; text-transform: uppercase;">Round Trip</div>
                                            <div style="font-size: 1.3rem; font-weight: 700; color: #667eea;" class="round-trip-price">
                                                <?php echo formatPriceINR($pricing['round_trip_price']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <button type="submit" class="btn w-100 mt-3" 
                                style="background: #1bbc9b; 
                                       color: white; border: none; border-radius: 10px; padding: 12px; 
                                       font-weight: 600; font-size: 1.1rem;">
                            <i class="fas fa-check-circle"></i> Confirm Booking
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Pricing card selection
    document.querySelectorAll('.pricing-card').forEach(function(card) {
        card.addEventListener('click', function() {
            document.querySelectorAll('.pricing-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            this.querySelector('input[type="radio"]').checked = true;
        });
    });
    
    // Trip type selection
    document.querySelectorAll('.trip-type-option').forEach(function(option) {
        option.addEventListener('click', function() {
            document.querySelectorAll('.trip-type-option').forEach(o => o.classList.remove('active'));
            this.classList.add('active');
            this.querySelector('input[type="radio"]').checked = true;
        });
    });
    
    // Passenger capacity validation
    document.getElementById('numPassengers').addEventListener('input', function() {
        const numPassengers = parseInt(this.value) || 0;
        
        document.querySelectorAll('.pricing-card').forEach(function(card) {
            const maxPassengers = parseInt(card.dataset.maxPassengers);
            
            if (numPassengers > maxPassengers) {
                card.style.opacity = '0.5';
                card.style.pointerEvents = 'none';
                card.classList.remove('selected');
                card.querySelector('input[type="radio"]').checked = false;
            } else {
                card.style.opacity = '1';
                card.style.pointerEvents = 'auto';
            }
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
