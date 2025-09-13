<?php
// Simple test page to check if resources are loading correctly
$page_title = "Resource Test Page";
$current_page = "test";

include 'includes/header.php';
?>

<div class="container py-5">
    <h1 class="mb-4">🧪 Resource Loading Test</h1>
    
    <div class="row">
        <div class="col-md-6">
            <h3>✅ CSS & Fonts Test</h3>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> FontAwesome icon should display
            </div>
            <div class="alert alert-success">
                <i class="flaticon-check"></i> Custom icon should display  
            </div>
            <p>If you can see the icons above, CSS and fonts are loading correctly!</p>
        </div>
        
        <div class="col-md-6">
            <h3>🎯 JavaScript Test</h3>
            <button id="test-btn" class="btn btn-primary">Test jQuery</button>
            <div id="test-result" class="mt-3"></div>
            <p class="mt-3">Click the button above to test if JavaScript is working.</p>
        </div>
    </div>
    
    <div class="row mt-5">
        <div class="col-12">
            <h3>📊 Resource Status</h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Resource</th>
                            <th>Status</th>
                            <th>Path</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Compressed CSS</td>
                            <td><span class="badge bg-success">✓ Loaded</span></td>
                            <td><code>assets/compressed/all-styles.min.css</code></td>
                        </tr>
                        <tr>
                            <td>Compressed JS</td>
                            <td><span class="badge bg-success">✓ Loaded</span></td>
                            <td><code>assets/compressed/all-scripts.min.js</code></td>
                        </tr>
                        <tr>
                            <td>FontAwesome Fonts</td>
                            <td><span class="badge bg-info">Check Console</span></td>
                            <td><code>assets/vendors/fontawesome/webfonts/</code></td>
                        </tr>
                        <tr>
                            <td>TravHub Icons</td>
                            <td><span class="badge bg-info">Check Console</span></td>
                            <td><code>assets/vendors/travhub-icons/</code></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <h3>🔧 Debug Information</h3>
            <div class="bg-light p-3 rounded">
                <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
                <p><strong>Page Load Time:</strong> <span id="load-time"></span></p>
                <p><strong>User Agent:</strong> <?php echo $_SERVER['HTTP_USER_AGENT'] ?? 'Not available'; ?></p>
            </div>
        </div>
    </div>
</div>

<script>
// Test JavaScript functionality
document.addEventListener('DOMContentLoaded', function() {
    // Record page load time
    const loadTime = performance.now();
    document.getElementById('load-time').textContent = Math.round(loadTime) + 'ms';
    
    // Test jQuery
    const testBtn = document.getElementById('test-btn');
    const testResult = document.getElementById('test-result');
    
    testBtn.addEventListener('click', function() {
        if (typeof jQuery !== 'undefined') {
            testResult.innerHTML = '<div class="alert alert-success">✓ jQuery is working! Version: ' + jQuery.fn.jquery + '</div>';
            
            // Test animation
            jQuery(testResult).hide().fadeIn(500);
        } else {
            testResult.innerHTML = '<div class="alert alert-danger">✗ jQuery not found</div>';
        }
    });
    
    // Log resource loading status
    console.log('=== RESOURCE LOADING TEST ===');
    console.log('jQuery available:', typeof jQuery !== 'undefined');
    console.log('Bootstrap available:', typeof bootstrap !== 'undefined');
    console.log('Page load time:', Math.round(loadTime) + 'ms');
});
</script>

<?php include 'includes/footer.php'; ?>
