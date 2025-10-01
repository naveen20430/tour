<?php
require_once 'config/config.php';
require_once 'includes/tour_slider_helper.php';

// Enable tour slider CSS for this page
enableTourSliderCSS();

$page_title = 'Homepage Layout Test';
include 'includes/header.php';
?>

<div class="container" style="padding: 30px 0;">
    <h1 class="text-center mb-4">Homepage Layout Test</h1>
    
    <div class="alert alert-info">
        <h4>Testing Complete Homepage Layout</h4>
        <p>This page tests the full homepage layout with both hero slider and tour carousel.</p>
    </div>
</div>

<!-- Hero Slider (Full-screen background slider) -->
<div class="alert alert-warning text-center" style="margin: 0; border-radius: 0;">
    <h4>🎬 MAIN HERO SLIDER (Should be full-screen)</h4>
    <p>This should be a full-screen background slider with tour information</p>
</div>

<?php displayTourSlider(); ?>

<!-- Tour Carousel (3-card carousel) -->
<div class="container" style="padding: 50px 0;">
    <div class="alert alert-success text-center">
        <h4>🎠 TOUR CAROUSEL (Should show 3 cards)</h4>
        <p>This should be a 3-card carousel showing tours side by side</p>
    </div>
</div>

<?php displayTourCarousel(); ?>

<div class="container" style="padding: 50px 0;">
    <div class="row">
        <div class="col-md-6">
            <div class="alert alert-primary">
                <h5>✅ Hero Slider Should Show:</h5>
                <ul class="mb-0">
                    <li>Full-screen background images</li>
                    <li>Tour titles and descriptions</li>
                    <li>Pricing in INR</li>
                    <li>Duration information</li>
                    <li>"View Details" and "Book Now" buttons</li>
                    <li>Auto-play between tours</li>
                    <li>Navigation dots at bottom</li>
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="alert alert-success">
                <h5>✅ Tour Carousel Should Show:</h5>
                <ul class="mb-0">
                    <li>3 tour cards side by side (desktop)</li>
                    <li>2 cards on tablet</li>
                    <li>1 card on mobile</li>
                    <li>Featured badges and ratings</li>
                    <li>Pricing in INR with discounts</li>
                    <li>Navigation arrows and dots</li>
                    <li>Auto-play carousel</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="alert alert-danger">
        <h5>❌ Issues to Report:</h5>
        <ul class="mb-0">
            <li>Hero slider not showing or too small</li>
            <li>Tour cards stacked or overlapping</li>
            <li>Missing navigation elements</li>
            <li>Auto-play not working</li>
            <li>Responsive issues on mobile</li>
        </ul>
    </div>
    
    <div class="text-center">
        <a href="index.php" class="btn btn-primary btn-lg">View Actual Homepage</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>