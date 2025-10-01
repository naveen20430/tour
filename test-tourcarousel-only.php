<?php
require_once 'config/config.php';
require_once 'includes/tour_slider_helper.php';

// Enable tour slider CSS
enableTourSliderCSS();

include 'includes/header.php';
?>

<div class="container" style="padding: 40px 0 20px;">
    <div class="alert alert-primary text-center">
        <h2>🎠 Tour Carousel Test</h2>
        <p><strong>Testing ONLY the tourCarousel section</strong></p>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="alert alert-success">
                <h5>✅ Should Work:</h5>
                <ul class="mb-0">
                    <li>3 cards side by side on desktop</li>
                    <li>2 cards on tablet (576px+)</li>
                    <li>1 card on mobile</li>
                    <li>Auto-play every 4 seconds</li>
                    <li>Navigation arrows (desktop only)</li>
                    <li>Dot navigation at bottom</li>
                    <li>Cards same height</li>
                    <li>Hover effects on cards</li>
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="alert alert-warning">
                <h5>🔍 Check Console:</h5>
                <ul class="mb-0">
                    <li>Open Developer Tools (F12)</li>
                    <li>Look for "Tour Carousel initialized successfully"</li>
                    <li>Check for any JavaScript errors</li>
                    <li>Verify jQuery and Owl Carousel are loaded</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- TOUR CAROUSEL SECTION ONLY -->
<?php displayTourCarousel(); ?>

<div class="container" style="padding: 40px 0;">
    <div class="alert alert-info">
        <h4>🔧 Troubleshooting:</h4>
        <ul>
            <li><strong>Cards stacked vertically:</strong> CSS/Owl Carousel not loading properly</li>
            <li><strong>No navigation:</strong> JavaScript not initialized</li>
            <li><strong>Not auto-playing:</strong> Check console for errors</li>
            <li><strong>Different card heights:</strong> Flexbox CSS issue</li>
        </ul>
        
        <hr>
        
        <h5>JavaScript Test:</h5>
        <button onclick="testCarousel()" class="btn btn-primary">Test Carousel Functions</button>
        <div id="testResult" style="margin-top: 10px;"></div>
    </div>
</div>

<script>
function testCarousel() {
    const result = document.getElementById('testResult');
    let messages = [];
    
    // Check if jQuery is loaded
    if (typeof jQuery === 'undefined') {
        messages.push('❌ jQuery not loaded');
    } else {
        messages.push('✅ jQuery loaded');
        
        // Check if Owl Carousel is available
        if (typeof jQuery.fn.owlCarousel === 'undefined') {
            messages.push('❌ Owl Carousel not loaded');
        } else {
            messages.push('✅ Owl Carousel loaded');
            
            // Check if tour carousel exists
            const carousel = jQuery('#tourCarousel');
            if (carousel.length === 0) {
                messages.push('❌ #tourCarousel element not found');
            } else {
                messages.push('✅ #tourCarousel element found');
                
                // Check if carousel is initialized
                if (carousel.hasClass('owl-loaded')) {
                    messages.push('✅ Carousel is initialized');
                    
                    // Test navigation
                    try {
                        carousel.trigger('next.owl.carousel');
                        messages.push('✅ Navigation test successful');
                    } catch (e) {
                        messages.push('❌ Navigation test failed: ' + e.message);
                    }
                } else {
                    messages.push('❌ Carousel not initialized');
                }
            }
        }
    }
    
    result.innerHTML = '<div class="alert alert-secondary"><strong>Test Results:</strong><br>' + 
                      messages.join('<br>') + '</div>';
}

// Auto-run test after page load
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(testCarousel, 2000); // Wait 2 seconds for carousel to initialize
});
</script>

<?php include 'includes/footer.php'; ?>