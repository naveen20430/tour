<?php
require_once 'config/config.php';
require_once 'includes/tour_slider_helper.php';

// Set page variables
$page_title = getSetting('site_name') . ' || Travel & Tour Booking Agency';
$current_page = 'home';

// Enable tour slider CSS for this page
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

// Include header
include 'includes/header.php';
?>

<?php displayTourSlider(); ?>

    <section class="about-one section-space" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); position: relative; overflow: hidden;">
        <!-- Floating elements for visual appeal -->
        <div style="position: absolute; top: 20%; left: 10%; width: 100px; height: 100px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); border-radius: 50%; animation: float 6s ease-in-out infinite;"></div>
        <div style="position: absolute; bottom: 20%; right: 15%; width: 150px; height: 150px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%); border-radius: 50%; animation: float 8s ease-in-out infinite reverse;"></div>
        
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center scroll-reveal" style="z-index: 2; position: relative;">
                        <div style="margin-bottom: 30px;">
                            <span class="badge bg-primary" style="padding: 8px 20px; font-size: 0.9rem; border-radius: 25px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;">✈️ Premium Travel Experience</span>
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

    <!-- Featured Tours Section -->
    <?php if (!empty($featured_tours)): ?>
    <section class="tours-one section-space" style="background: #fff; position: relative;">
        <div class="container">
            <div class="section-title text-center scroll-reveal" style="margin-bottom: 60px;">
                <div style="margin-bottom: 15px;">
                    <span class="badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 8px 16px; border-radius: 20px; font-size: 0.9rem;">
                        ✨ Discover Amazing
                    </span>
                </div>
                <h2 class="gradient-text" style="font-size: 2.8rem; font-weight: 700; margin-bottom: 20px;">Featured Tour Packages</h2>
                <p style="font-size: 1.1rem; color: #6c757d; max-width: 500px; margin: 0 auto; line-height: 1.6;">
                    Explore our handpicked selection of the most popular and exciting tours
                </p>
                <div style="width: 80px; height: 4px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); margin: 20px auto 0; border-radius: 2px;"></div>
            </div>
            
            <div class="row mt-5">
                <?php foreach ($featured_tours as $index => $tour): ?>
                    <div class="col-lg-4 col-md-6 mb-5 scroll-reveal" style="transition-delay: <?php echo $index * 0.1; ?>s;">
                        <div class="card h-100" style="border: none; border-radius: 20px; overflow: hidden; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1); transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); position: relative; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                            <!-- Light hover overlay -->
                            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%); opacity: 0; transition: all 0.3s ease; z-index: 1; border-radius: 20px;"></div>
                            
                            <div class="position-relative" style="overflow: hidden;">
                                <img src="<?php echo $tour['featured_image'] ?: 'assets/images/tours/default.jpg'; ?>" 
                                     class="card-img-top" style="height: 280px; object-fit: cover; transition: transform 0.4s ease;" 
                                     alt="<?php echo htmlspecialchars($tour['title']); ?>">
                                
                                <!-- Gradient overlay -->
                                <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 100px; background: linear-gradient(transparent, rgba(0,0,0,0.3));"></div>
                                
                                <div class="position-absolute top-0 end-0 m-3" style="z-index: 2;">
                                    <span class="badge px-3 py-2" style="border-radius: 25px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: 600; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
                                        <?php if ($tour['discount_price']): ?>
                                            <span style="text-decoration: line-through; opacity: 0.7; font-size: 0.8rem;">₹<?php echo number_format($tour['price'], 0); ?></span>
                                            <span style="font-size: 1rem;">₹<?php echo number_format($tour['discount_price'], 0); ?></span>
                                        <?php else: ?>
                                            <span style="font-size: 1rem;">₹<?php echo number_format($tour['price'], 0); ?></span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                
                                <div class="position-absolute top-0 start-0 m-3" style="z-index: 2;">
                                    <span class="badge" style="border-radius: 15px; background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%); color: white; padding: 6px 12px; font-weight: 500; box-shadow: 0 4px 15px rgba(255, 107, 107, 0.4);">🔥 Featured</span>
                                </div>
                                
                                <!-- Rating badge -->
                                <div class="position-absolute bottom-0 start-0 m-3" style="z-index: 2;">
                                    <span class="badge" style="background: rgba(255, 255, 255, 0.9); color: #333; border-radius: 20px; padding: 6px 12px; font-weight: 500;">
                                        ⭐ 4.8 (120+ reviews)
                                    </span>
                                </div>
                            </div>
                            
                            <div class="card-body d-flex flex-column" style="padding: 25px; position: relative; z-index: 2; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); border-radius: 0 0 20px 20px;">
                                <div class="mb-3" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                                    <div style="display: flex; align-items: center; color: #667eea; font-weight: 500;">
                                        <i class="fas fa-map-marker-alt me-2" style="color: #667eea;"></i>
                                        <span style="font-size: 0.9rem;"><?php echo htmlspecialchars($tour['destination_name'] . ', ' . $tour['country']); ?></span>
                                    </div>
                                    <div style="display: flex; align-items: center; color: #28a745; font-weight: 500;">
                                        <i class="fas fa-clock me-2" style="color: #28a745;"></i>
                                        <span style="font-size: 0.9rem;"><?php echo $tour['duration_days']; ?> Days</span>
                                    </div>
                                </div>
                                
                                <h5 class="card-title" style="margin-bottom: 15px; font-size: 1.3rem; font-weight: 600; line-height: 1.4; color: #2c3e50;">
                                    <a href="<?php echo tourUrl($tour['slug']); ?>" class="text-decoration-none" style="color: inherit; transition: all 0.3s ease;" onmouseover="this.style.color='#667eea'" onmouseout="this.style.color='#2c3e50'">
                                        <?php echo htmlspecialchars($tour['title']); ?>
                                    </a>
                                </h5>
                                
                                <p class="card-text flex-grow-1" style="color: #6c757d; font-size: 0.95rem; line-height: 1.6; margin-bottom: 20px;">
                                    <?php echo substr(htmlspecialchars($tour['short_description']), 0, 100); ?>...
                                </p>
                                
                                <div class="d-flex justify-content-between align-items-center mb-3" style="flex-wrap: wrap; gap: 10px;">
                                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                        <span class="badge" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 5px 10px; border-radius: 12px; font-size: 0.75rem;"><?php echo ucfirst($tour['difficulty_level']); ?></span>
                                        <span class="badge" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #495057; padding: 5px 10px; border-radius: 12px; font-size: 0.75rem;"><?php echo ucfirst($tour['tour_type']); ?></span>
                                    </div>
                                    <div style="display: flex; align-items: center; color: #6c757d; font-size: 0.85rem;">
                                        <i class="fas fa-users me-1" style="color: #667eea;"></i>
                                        <span>Max <?php echo $tour['max_people']; ?></span>
                                    </div>
                                </div>
                                
                                <div class="mt-auto">
                                    <a href="<?php echo tourUrl($tour['slug']); ?>" class="travhub-btn w-100" style="padding: 12px; font-size: 0.95rem; font-weight: 600; text-transform: none; letter-spacing: 0.5px;">
                                        <span>🔍 Explore Details</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-5 scroll-reveal">
                <a href="<?php echo navUrl('tours'); ?>" class="travhub-btn" style="padding: 15px 40px; font-size: 1.1rem; font-weight: 600;">
                    <span>🌍 View All Tours</span>
                </a>
                <p style="margin-top: 15px; color: #6c757d; font-size: 0.9rem;">Discover <?php echo count($featured_tours); ?>+ amazing destinations worldwide</p>
            </div>
        </div>
    </section>
    <?php endif; ?>

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
                        <div class="card h-100" style="border: none; border-radius: 25px; overflow: hidden; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1); transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); position: relative; background: #fff;">
                            <!-- Hover overlay -->
                            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, rgba(40, 167, 69, 0.9) 0%, rgba(32, 201, 151, 0.9) 100%); opacity: 0; transition: all 0.3s ease; z-index: 1; border-radius: 25px; display: flex; align-items: center; justify-content: center;">
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
                                
                                <a href="<?php echo toursUrl(['destination' => $destination['slug']]); ?>" class="btn w-100" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border: none; border-radius: 15px; padding: 12px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);">
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
