<?php
require_once 'config/config.php';
require_once 'includes/tour_slider_helper.php';

enableTourSliderCSS();
include 'includes/header.php';
?>

<div class="container" style="padding: 40px 0;">
    <div class="alert alert-info text-center">
        <h2>🎯 Navigation Arrow Centering Test</h2>
        <p>Testing if the carousel navigation arrows are perfectly centered in their circular buttons</p>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="alert alert-success">
                <h5>✅ What Should Be Fixed:</h5>
                <ul class="mb-0">
                    <li>Left/Right arrows perfectly centered in circles</li>
                    <li>No FontAwesome alignment issues</li>
                    <li>Clean SVG icons instead of font icons</li>
                    <li>Smooth hover scaling effect</li>
                    <li>Consistent positioning on all screen sizes</li>
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="alert alert-warning">
                <h5>🔍 Test Visual Centering:</h5>
                <ul class="mb-0">
                    <li>Hover over navigation arrows</li>
                    <li>Check if arrows are centered vertically & horizontally</li>
                    <li>Test on different screen sizes</li>
                    <li>Verify smooth hover animations</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php displayTourCarousel(); ?>

<div class="container" style="padding: 40px 0;">
    <div class="alert alert-secondary">
        <h4>🔧 Technical Details:</h4>
        <ul>
            <li><strong>Fixed:</strong> Replaced FontAwesome icons with custom SVG icons</li>
            <li><strong>Centering:</strong> Using CSS flexbox with perfect alignment</li>
            <li><strong>Animations:</strong> Added smooth hover scaling effects</li>
            <li><strong>Size:</strong> Icons are 18x18px in 50x50px circles</li>
        </ul>
        
        <hr>
        
        <div class="row">
            <div class="col-md-6">
                <h5>Before (FontAwesome):</h5>
                <div style="display: flex; gap: 20px; margin: 20px 0;">
                    <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chevron-left" style="color: white; font-size: 16px;"></i>
                    </div>
                    <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chevron-right" style="color: white; font-size: 16px;"></i>
                    </div>
                </div>
                <small class="text-muted">FontAwesome icons may have alignment issues</small>
            </div>
            <div class="col-md-6">
                <h5>After (SVG):</h5>
                <div style="display: flex; gap: 20px; margin: 20px 0;">
                    <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer;" onmouseover="this.querySelector('svg').style.transform='scale(1.1)'" onmouseout="this.querySelector('svg').style.transform='scale(1)'">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="transition: transform 0.2s ease;">
                            <path d="M15 18L9 12L15 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer;" onmouseover="this.querySelector('svg').style.transform='scale(1.1)'" onmouseout="this.querySelector('svg').style.transform='scale(1)'">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="transition: transform 0.2s ease;">
                            <path d="M9 18L15 12L9 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
                <small class="text-success">Perfect SVG centering with hover effects</small>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>