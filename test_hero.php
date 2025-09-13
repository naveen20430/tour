<?php
require_once 'config/config.php';

// Set page variables
$page_title = 'Hero Test - ' . (function_exists('getSetting') ? getSetting('site_name') : 'Travel Site');
$current_page = 'home';

// Include hero CSS only for this test page
$extra_css = '<link rel="stylesheet" href="' . BASE_URL . 'assets/css/hero.css">';

// Test hero image data
$hero_image = [
    'title' => 'Welcome to Adventure Tours',
    'subtitle' => 'Discover Amazing Destinations',
    'description' => 'Experience the world like never before with our carefully curated travel packages. From exotic destinations to cultural experiences, we make your travel dreams come true.',
    'image_path' => 'assets/images/hero/default-hero.jpg'
];

// Include header
include 'includes/header.php';
?>

<!-- Dynamic Hero Section Test -->
<section class="hero-one hero-dynamic" style="background-image: url('<?php echo BASE_URL . $hero_image['image_path']; ?>'); background-color: #007bff;">
    <div class="hero-overlay"></div>
    <div class="container">
        <div class="hero-one__content">
            <?php if ($hero_image['subtitle']): ?>
                <h5 class="hero-one__sub-title sub-title"><?php echo htmlspecialchars($hero_image['subtitle']); ?></h5>
            <?php endif; ?>
            
            <?php if ($hero_image['title']): ?>
                <h2 class="hero-one__title title"><?php echo htmlspecialchars($hero_image['title']); ?></h2>
            <?php endif; ?>
            
            <?php if ($hero_image['description']): ?>
                <p class="hero-one__text sub-title"><?php echo htmlspecialchars($hero_image['description']); ?></p>
            <?php endif; ?>
            
            <div class="hero-one__buttons mt-4">
                <a href="tours.php" class="travhub-btn me-3">
                    <span>Explore Tours</span>
                </a>
                <a href="destinations.php" class="travhub-btn travhub-btn--outline">
                    <span>View Destinations</span>
                </a>
            </div>
        </div>
    </div>
</section>

<section class="section-space" style="padding: 4rem 0;">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="text-center">
                    <h2>Hero Section Test</h2>
                    <p class="lead">This page tests the hero section styling.</p>
                    <div class="alert alert-info">
                        <h4>Test Results:</h4>
                        <ul class="text-left">
                            <li>✓ Hero background image: <?php echo $hero_image['image_path']; ?></li>
                            <li>✓ Hero overlay: Applied</li>
                            <li>✓ Hero title: "<?php echo $hero_image['title']; ?>"</li>
                            <li>✓ Hero subtitle: "<?php echo $hero_image['subtitle']; ?>"</li>
                            <li>✓ Hero description: "<?php echo substr($hero_image['description'], 0, 50); ?>..."</li>
                            <li>✓ Action buttons: Displayed</li>
                        </ul>
                    </div>
                    <div class="mt-4">
                        <a href="index.php" class="btn btn-primary me-3">Back to Homepage</a>
                        <a href="admin/hero-images.php" class="btn btn-secondary">Manage Hero Images</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
