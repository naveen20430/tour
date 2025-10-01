<?php
require_once 'config/config.php';
require_once 'includes/tour_slider_helper.php';

// Enable tour slider CSS for this page
enableTourSliderCSS();

include 'includes/header.php';
?>

<div class="container" style="padding: 50px 0;">
    <h1 class="text-center mb-5">Tour Carousel Test</h1>
    
    <div class="alert alert-info">
        <h4>Testing Tour Carousel Display</h4>
        <p>This page tests the tour carousel that should show 3 tour cards side by side.</p>
    </div>
</div>

<?php displayTourCarousel(); ?>

<div class="container" style="padding: 50px 0;">
    <div class="alert alert-success">
        <h4>What You Should See:</h4>
        <ul>
            <li>✅ 3 tour cards displayed side by side on desktop</li>
            <li>✅ 2 tour cards on tablet</li>
            <li>✅ 1 tour card on mobile</li>
            <li>✅ Navigation arrows on sides</li>
            <li>✅ Dots navigation at bottom</li>
            <li>✅ Auto-play carousel</li>
            <li>✅ Cards with consistent heights</li>
        </ul>
    </div>
    
    <div class="alert alert-warning">
        <h4>If Not Working Correctly:</h4>
        <ul>
            <li>❌ Cards overlapping or stacked vertically</li>
            <li>❌ Cards with different heights</li>
            <li>❌ No navigation or dots</li>
            <li>❌ Carousel not auto-playing</li>
        </ul>
    </div>
</div>

<?php include 'includes/footer.php'; ?>