<?php
require_once 'config/config.php';

$page_title = 'URL Test - ' . (function_exists('getSetting') ? getSetting('site_name') : 'Travel Site');
$current_page = 'test';

include 'includes/header.php';
?>

<section class="section-space" style="padding: 4rem 0;">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="text-center">
                    <h2>URL Testing Page</h2>
                    <p class="lead">Testing all URL helper functions</p>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Navigation URLs</h4>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <strong>Home:</strong> <a href="<?php echo navUrl('home'); ?>"><?php echo navUrl('home'); ?></a>
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Tours:</strong> <a href="<?php echo navUrl('tours'); ?>"><?php echo navUrl('tours'); ?></a>
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Destinations:</strong> <a href="<?php echo navUrl('destinations'); ?>"><?php echo navUrl('destinations'); ?></a>
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Blog:</strong> <a href="<?php echo navUrl('blog'); ?>"><?php echo navUrl('blog'); ?></a>
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Contact:</strong> <a href="<?php echo navUrl('contact'); ?>"><?php echo navUrl('contact'); ?></a>
                                    </li>
                                    <li class="list-group-item">
                                        <strong>About:</strong> <a href="<?php echo navUrl('about'); ?>"><?php echo navUrl('about'); ?></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Functional URLs</h4>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <strong>Tours List:</strong> <a href="<?php echo toursUrl(); ?>"><?php echo toursUrl(); ?></a>
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Tours by Category:</strong> <a href="<?php echo toursUrl(['category' => 'adventure']); ?>"><?php echo toursUrl(['category' => 'adventure']); ?></a>
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Booking:</strong> <a href="<?php echo bookingUrl(); ?>"><?php echo bookingUrl(); ?></a>
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Book Tour ID 1:</strong> <a href="<?php echo bookingUrl(1); ?>"><?php echo bookingUrl(1); ?></a>
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Admin Panel:</strong> <a href="<?php echo adminUrl(); ?>"><?php echo adminUrl(); ?></a>
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Admin Login:</strong> <a href="<?php echo adminUrl('login'); ?>"><?php echo adminUrl('login'); ?></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Configuration Info</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>BASE_URL:</strong> <?php echo BASE_URL; ?></p>
                                        <p><strong>BASE_PATH:</strong> <?php echo BASE_PATH; ?></p>
                                        <p><strong>Server Name:</strong> <?php echo $_SERVER['SERVER_NAME']; ?></p>
                                        <p><strong>Request URI:</strong> <?php echo $_SERVER['REQUEST_URI']; ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>HTTP Host:</strong> <?php echo $_SERVER['HTTP_HOST']; ?></p>
                                        <p><strong>Script Name:</strong> <?php echo $_SERVER['SCRIPT_NAME']; ?></p>
                                        <p><strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT']; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 text-center">
                    <a href="index.php" class="btn btn-primary me-3">Back to Homepage</a>
                    <a href="test_hero.php" class="btn btn-secondary">Test Hero Section</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
