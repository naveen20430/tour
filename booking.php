<?php
require_once 'config/config.php';

// Try to include cab options, but handle gracefully if not available
$cab_functionality_enabled = false;
try {
    if (file_exists('includes/cab_options.php')) {
        require_once 'includes/cab_options.php';
        // Test if cab_types table exists
        $db->fetch("SELECT COUNT(*) as count FROM cab_types LIMIT 1");
        $cab_functionality_enabled = true;
    }
} catch (Exception $e) {
    // Cab functionality not available, continue without it
    $cab_functionality_enabled = false;
}

$errors = [];
$success = false;
$booking_number = '';

if ($_POST) {
    $tour_id = $_POST['tour_id'] ?? '';
    $tour_date = $_POST['tour_date'] ?? '';
    $people = $_POST['people'] ?? '';
    $guest_name = $_POST['guest_name'] ?? '';
    $guest_email = $_POST['guest_email'] ?? '';
    $guest_phone = $_POST['guest_phone'] ?? '';
    $special_requirements = $_POST['special_requirements'] ?? '';
    $cab_type = $_POST['cab_type'] ?? '';
    
    // Validation
    if (empty($tour_id)) $errors[] = 'Tour selection is required';
    if (empty($tour_date)) $errors[] = 'Tour date is required';
    if (empty($people)) $errors[] = 'Number of people is required';
    if (empty($guest_name)) $errors[] = 'Your name is required';
    if (empty($guest_email)) $errors[] = 'Your email is required';
    if (empty($guest_phone)) $errors[] = 'Your phone number is required';
    if ($cab_functionality_enabled && empty($cab_type)) $errors[] = 'Cab type selection is required';
    
    // Get tour details
    $tour = $db->fetch("SELECT * FROM tours WHERE id = ? AND status = 'active'", [$tour_id]);
    if (!$tour) {
        $errors[] = 'Invalid tour selection';
    }
    
    if (empty($errors)) {
        // Calculate total amount
        $price_per_person = $tour['discount_price'] ?: $tour['price'];
        $total_amount = $price_per_person * $people;
        
        // Initialize cab-related variables
        $cab_price = 0;
        $total_with_cab = $total_amount;
        
        // Handle cab functionality if enabled
        if ($cab_functionality_enabled && !empty($cab_type)) {
            try {
                $cabOptions = new CabOptions($db);
                
                // Validate cab selection can accommodate the number of people
                if (!$cabOptions->canAccommodate($cab_type, $people)) {
                    $errors[] = 'Selected cab type cannot accommodate ' . $people . ' people';
                } else {
                    // Calculate cab charges
                    $cab_price = $cabOptions->calculateCabPrice($cab_type, $tour['duration_days']);
                    $total_with_cab = $total_amount + $cab_price;
                }
            } catch (Exception $e) {
                // Fallback: continue without cab functionality
                $cab_type = null;
                $cab_price = 0;
                $total_with_cab = $total_amount;
            }
        }
        
        // Generate booking number
        $booking_number = 'TH' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        try {
            // Use different SQL based on whether cab columns exist
            if ($cab_functionality_enabled) {
                $booking_id = $db->execute(
                    "INSERT INTO bookings (booking_number, tour_id, guest_name, guest_email, guest_phone, number_of_people, tour_date, total_amount, cab_type, cab_price, special_requirements, booking_status, payment_status, created_at) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'pending', NOW())",
                    [$booking_number, $tour_id, $guest_name, $guest_email, $guest_phone, $people, $tour_date, $total_amount, $cab_type, $cab_price, $special_requirements]
                );
            } else {
                $booking_id = $db->execute(
                    "INSERT INTO bookings (booking_number, tour_id, guest_name, guest_email, guest_phone, number_of_people, tour_date, total_amount, special_requirements, booking_status, payment_status, created_at) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'pending', NOW())",
                    [$booking_number, $tour_id, $guest_name, $guest_email, $guest_phone, $people, $tour_date, $total_amount, $special_requirements]
                );
            }
            
            if ($booking_id) {
                $success = true;
                // In a real application, you'd send confirmation email here
            } else {
                $errors[] = 'Failed to create booking. Please try again.';
            }
        } catch (Exception $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}

// Get tour details if tour_id is provided
$tour = null;
if (!empty($_GET['tour_id']) || !empty($_POST['tour_id'])) {
    $tour_id = $_GET['tour_id'] ?? $_POST['tour_id'];
    $tour = $db->fetch("
        SELECT t.*, d.name as destination_name, d.country 
        FROM tours t 
        LEFT JOIN destinations d ON t.destination_id = d.id 
        WHERE t.id = ? AND t.status = 'active'
    ", [$tour_id]);
}

// Initialize cab options for form if available
$availableCabs = [];
if ($cab_functionality_enabled) {
    try {
        $cabOptions = new CabOptions($db);
        $availableCabs = $cabOptions->getCabOptionsForDropdown();
    } catch (Exception $e) {
        $availableCabs = [];
        $cab_functionality_enabled = false;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Your Tour - <?php echo getSetting('site_name'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .booking-container { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 50px 0; }
        .booking-card { background: white; border-radius: 15px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .tour-info { background: #f8f9fa; border-radius: 10px; padding: 20px; margin-bottom: 30px; }
        .price-display { font-size: 2em; font-weight: bold; color: #667eea; }
    </style>
</head>
<body>
    <div class="booking-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="booking-card p-5">
                        <?php if ($success): ?>
                            <div class="text-center">
                                <div class="mb-4">
                                    <i class="fas fa-check-circle text-success" style="font-size: 4em;"></i>
                                </div>
                                <h2 class="text-success mb-4">Booking Confirmed!</h2>
                                <div class="alert alert-success">
                                    <h4>Booking Number: <strong><?php echo $booking_number; ?></strong></h4>
                                    <p class="mb-0">We've received your booking request. Our team will contact you within 24 hours to confirm your booking and payment details.</p>
                                    
                                    <?php if (!empty($cab_type)): ?>
                                        <hr>
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <strong>Selected Cab:</strong> <?php echo getCabDisplayName($cab_type); ?>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Total Amount:</strong> ₹<?php echo number_format($total_with_cab, 0); ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="mt-4">
                                    <a href="index" class="btn btn-primary me-3">Back to Home</a>
                                    <a href="tours" class="btn btn-outline-primary">Browse More Tours</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center mb-4">
                                <h2><i class="fas fa-calendar-plus me-2"></i>Book Your Tour</h2>
                                <p class="text-muted">Fill in the details below to reserve your spot</p>
                            </div>

                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?php echo htmlspecialchars($error); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <?php if ($tour): ?>
                                <div class="tour-info">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h4 class="mb-1"><?php echo htmlspecialchars($tour['title']); ?></h4>
                                            <p class="text-muted mb-2">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                <?php echo htmlspecialchars($tour['destination_name'] . ', ' . $tour['country']); ?>
                                            </p>
                                            <p class="mb-0">
                                                <span class="badge bg-primary me-2"><?php echo $tour['duration_days']; ?> Days</span>
                                                <span class="badge bg-secondary me-2">Max <?php echo $tour['max_people']; ?> People</span>
                                                <span class="badge bg-info"><?php echo ucfirst($tour['difficulty_level']); ?></span>
                                            </p>
                                        </div>
                                        <div class="col-md-4 text-md-end">
                                            <div class="price-display">
                                                <?php if ($tour['discount_price']): ?>
                                                    <small class="text-decoration-line-through text-muted">₹<?php echo number_format($tour['price'], 0); ?></small>
                                                    ₹<?php echo number_format($tour['discount_price'], 0); ?>
                                                <?php else: ?>
                                                    ₹<?php echo number_format($tour['price'], 0); ?>
                                                <?php endif; ?>
                                            </div>
                                            <small class="text-muted">per person</small>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <form method="POST" id="bookingForm">
                                <?php if ($tour): ?>
                                    <input type="hidden" name="tour_id" value="<?php echo $tour['id']; ?>">
                                <?php else: ?>
                                    <div class="mb-3">
                                        <label class="form-label">Select Tour *</label>
                                        <select name="tour_id" class="form-select" required>
                                            <option value="">Choose a tour...</option>
                                            <?php
                                            $available_tours = $db->fetchAll("SELECT id, title, price, discount_price FROM tours WHERE status = 'active' ORDER BY title");
                                            foreach ($available_tours as $t):
                                            ?>
                                                <option value="<?php echo $t['id']; ?>" <?php echo ($_POST['tour_id'] ?? '') == $t['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($t['title']); ?> - ₹<?php echo number_format($t['discount_price'] ?: $t['price'], 0); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                <?php endif; ?>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tour Date *</label>
                                        <input type="date" name="tour_date" class="form-select" required 
                                               min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                                               value="<?php echo htmlspecialchars($_POST['tour_date'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Number of People *</label>
                                        <select name="people" class="form-select" required>
                                            <option value="">Select...</option>
                                            <?php for ($i = 1; $i <= ($tour['max_people'] ?? 10); $i++): ?>
                                                <option value="<?php echo $i; ?>" <?php echo ($_POST['people'] ?? '') == $i ? 'selected' : ''; ?>>
                                                    <?php echo $i; ?> Person<?php echo $i > 1 ? 's' : ''; ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Full Name *</label>
                                        <input type="text" name="guest_name" class="form-control" required
                                               value="<?php echo htmlspecialchars($_POST['guest_name'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email Address *</label>
                                        <input type="email" name="guest_email" class="form-control" required
                                               value="<?php echo htmlspecialchars($_POST['guest_email'] ?? ''); ?>">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Phone Number *</label>
                                    <input type="tel" name="guest_phone" class="form-control" required
                                           value="<?php echo htmlspecialchars($_POST['guest_phone'] ?? ''); ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Cab Type *</label>
                                    <select name="cab_type" class="form-select" required id="cabTypeSelect">
                                        <option value="">Select cab type...</option>
                                        <?php foreach ($availableCabs as $cab): ?>
                                            <option value="<?php echo $cab['value']; ?>" 
                                                    data-price="<?php echo $cab['price']; ?>"
                                                    data-max-passengers="<?php echo $cab['max_passengers']; ?>"
                                                    <?php echo ($_POST['cab_type'] ?? '') == $cab['value'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($cab['text']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="form-text text-muted">Cab will be provided for the entire tour duration</small>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Special Requirements (Optional)</label>
                                    <textarea name="special_requirements" class="form-control" rows="3"
                                              placeholder="Any dietary restrictions, accessibility needs, or special requests..."><?php echo htmlspecialchars($_POST['special_requirements'] ?? ''); ?></textarea>
                                </div>

                                <div id="totalAmount" class="alert alert-info mb-4" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Tour Cost:</strong> <span id="tourPrice">₹0</span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Cab Cost:</strong> <span id="cabPrice">₹0</span>
                                        </div>
                                    </div>
                                    <hr>
                                    <h5><strong>Total Amount: <span id="totalPrice">₹0</span></strong></h5>
                                    <small class="text-muted">Final amount will be confirmed by our team</small>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-calendar-check me-2"></i>Submit Booking Request
                                    </button>
                                    <a href="tours" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Back to Tours
                                    </a>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Calculate total amount dynamically
        function updateTotal() {
            const people = document.querySelector('select[name="people"]').value;
            const pricePerPerson = <?php echo $tour['discount_price'] ?: $tour['price'] ?: 0; ?>;
            const durationDays = <?php echo $tour['duration_days'] ?? 1; ?>;
            
            const cabSelect = document.getElementById('cabTypeSelect');
            const selectedCabOption = cabSelect.options[cabSelect.selectedIndex];
            const cabPricePerDay = selectedCabOption.dataset.price || 0;
            const maxPassengers = selectedCabOption.dataset.maxPassengers || 0;
            
            if (people && pricePerPerson) {
                const tourTotal = people * pricePerPerson;
                const cabTotal = cabPricePerDay * durationDays;
                const grandTotal = tourTotal + cabTotal;
                
                document.getElementById('tourPrice').textContent = '₹' + tourTotal.toLocaleString();
                document.getElementById('cabPrice').textContent = cabTotal > 0 ? '₹' + cabTotal.toLocaleString() : '₹0';
                document.getElementById('totalPrice').textContent = '₹' + grandTotal.toLocaleString();
                
                // Show warning if cab can't accommodate people
                const warningDiv = document.getElementById('cabWarning');
                if (cabSelect.value && people > maxPassengers) {
                    if (!warningDiv) {
                        const warning = document.createElement('div');
                        warning.id = 'cabWarning';
                        warning.className = 'alert alert-warning mt-2';
                        warning.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Selected cab can accommodate maximum ' + maxPassengers + ' passengers. Please select a different cab or reduce number of people.';
                        cabSelect.parentNode.appendChild(warning);
                    }
                } else if (warningDiv) {
                    warningDiv.remove();
                }
                
                document.getElementById('totalAmount').style.display = 'block';
            } else {
                document.getElementById('totalAmount').style.display = 'none';
            }
        }

        // Update total when people count or cab type changes
        document.addEventListener('DOMContentLoaded', function() {
            const peopleSelect = document.querySelector('select[name="people"]');
            const cabSelect = document.getElementById('cabTypeSelect');
            
            if (peopleSelect) {
                peopleSelect.addEventListener('change', updateTotal);
            }
            
            if (cabSelect) {
                cabSelect.addEventListener('change', updateTotal);
            }
            
            updateTotal(); // Initial calculation
        });
    </script>
</body>
</html>
