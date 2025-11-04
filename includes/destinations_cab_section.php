<!-- Destinations & Cab Facilities Section -->
<section class="destinations-cab-section section-space" style="background: #f8f9fa; padding: 60px 0;">
    <div class="container">
        <div class="row">
            <!-- 80% - Destinations & Tours -->
            <div class="col-lg-9 col-md-12 mb-4">
                <?php 
                // Get popular destinations with their tours
                $destinations_list = $db->fetchAll("SELECT * FROM destinations WHERE popular = 1 AND status = 'active' ORDER BY created_at DESC LIMIT 3");
                
                foreach ($destinations_list as $index => $dest):
                    // Get tours for this destination
                    $destination_tours = $db->fetchAll("
                        SELECT t.* 
                        FROM tours t 
                        WHERE t.destination_id = ? AND t.status = 'active'
                        ORDER BY t.featured DESC, t.popular DESC, t.created_at DESC 
                        LIMIT 3
                    ", [$dest['id']]);
                    
                    if (empty($destination_tours)) continue; // Skip if no tours
                ?>
                
                <!-- Destination Section -->
                <div class="destination-section mb-5" style="<?php echo $index > 0 ? 'margin-top: 50px;' : ''; ?>">
                    <!-- Destination Header -->
                    <div class="destination-header mb-4" style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-map-marker-alt" style="color: #667eea; font-size: 1.8rem;"></i>
                        <h1 style="font-size: 2rem; font-weight: 700; color: #333; margin: 0;">
                            <?php echo htmlspecialchars($dest['name']); ?>, <?php //echo htmlspecialchars($dest['country']); ?>Tours
                        </h1>
                    </div>
                    
                    <!-- Tours Slider -->
                    <div class="swiper tours-swiper-<?php echo $dest['id']; ?>">
                        <div class="swiper-wrapper">
                            <?php foreach ($destination_tours as $tour): ?>
                            <div class="swiper-slide">
                                <div class="card" style="border: none; border-radius: 1px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: all 0.3s ease; position: relative;">
                                    <!-- Featured Badge -->
                                    <?php if ($tour['featured']): ?>
                                    <div style="position: absolute; top: 15px; left: 15px; z-index: 10;">
                                        <span class="badge" style="background: linear-gradient(135deg, #f09433 0%, #e6683c 100%); color: white; padding: 8px 15px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Featured</span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <!-- Price Badge -->
                                    <div style="position: absolute; top: 15px; right: 15px; z-index: 10;">
                                        <div style="background: rgba(0,0,0,0.8); color: white; padding: 8px 15px; border-radius: 15px; font-weight: 600;">
                                            <?php if ($tour['discount_price'] && $tour['discount_price'] < $tour['price']): ?>
                                                <div style="font-size: 0.7rem; text-decoration: line-through; opacity: 0.7;"><?php echo formatPriceINR($tour['price']); ?></div>
                                                <div style="font-size: 0.95rem;"><?php echo formatPriceINR($tour['discount_price']); ?></div>
                                            <?php else: ?>
                                                <div style="font-size: 0.95rem;"><?php echo formatPriceINR($tour['price']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Tour Image -->
                                    <img src="<?php echo BASE_URL . ($tour['featured_image'] ?: 'assets/images/tours/default.jpg'); ?>" class="card-img-top" style="height: 220px; object-fit: cover;" alt="<?php echo htmlspecialchars($tour['title']); ?>">
                                    
                                    <!-- Card Body -->
                                    <div class="card-body" style="padding: 20px;">
                                        <!-- Location -->
                                        <p style="color: #667eea; font-size: 0.85rem; margin-bottom: 8px; display: flex; align-items: center; gap: 5px;">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <?php echo htmlspecialchars($dest['city'] ?: $dest['name']); ?>, <?php echo htmlspecialchars($dest['country']); ?>
                                        </p>
                                        
                                        <!-- Title -->
                                        <h5 class="card-title" style="font-weight: 700; margin-bottom: 10px; font-size: 1.1rem; color: #333; min-height: 50px;">
                                            <?php echo htmlspecialchars($tour['title']); ?>
                                        </h5>
                                        
                                        <!-- Description -->
                                        <p class="card-text" style="color: #6c757d; font-size: 0.85rem; margin-bottom: 15px; line-height: 1.5; min-height: 40px;">
                                            <?php echo htmlspecialchars(substr($tour['short_description'], 0, 80)); ?>...
                                        </p>
                                        
                                        <!-- Tour Info Tags -->
                                        <div style="display: flex; gap: 10px; margin-bottom: 15px; flex-wrap: wrap;">
                                            <span style="background: #f0f4ff; color: #667eea; padding: 5px 12px; border-radius: 15px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 5px;">
                                                <i class="fas fa-clock"></i> <?php echo $tour['duration_days']; ?> Days
                                            </span>
                                            <span style="background: #f0f4ff; color: #667eea; padding: 5px 12px; border-radius: 15px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">
                                                <?php echo htmlspecialchars($tour['difficulty_level']); ?>
                                            </span>
                                            <span style="background: #f0f4ff; color: #667eea; padding: 5px 12px; border-radius: 15px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">
                                                <?php echo htmlspecialchars($tour['tour_type']); ?>
                                            </span>
                                        </div>
                                        
                                        <!-- Action Button -->
                                        <a href="<?php echo tourUrl($tour['slug']); ?>" class="btn w-100" style="background: #1bbc9b; color: white; border: none; border-radius: 12px; padding: 12px; font-weight: 600; transition: all 0.3s ease;">
                                            Explore Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-pagination"></div>
                    </div>
                    
                    <!-- View All Tours Link -->
                    <div class="text-center mt-3">
                        <a href="<?php echo toursUrl(['destination' => $dest['slug']]); ?>" style="color: #667eea; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                            View all <?php echo htmlspecialchars($dest['name']); ?> tours <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                
                <?php endforeach; ?>
            </div>
            
            <!-- 20% - Cab Routes Sidebar -->
            <div class="col-lg-3 col-md-12">
                <div class="cab-routes-sidebar" style="position: sticky; top: 80px;">
                    <div class="section-header mb-4" style="background: #1bbc9b; padding: 15px; border-radius: 0px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
                        <h4 style="font-size: 1.2rem; font-weight: 700; color: white; margin-bottom: 5px;">
                            <i class="fas fa-route"></i> 🚗 Transport Facilities
                        </h4>
                        <p style="color: rgba(255, 255, 255, 0.9); font-size: 0.75rem; margin: 0;">Popular routes available</p>
                    </div>
                    
                    <?php 
                    // Get active cab routes
                    $cab_routes = $db->fetchAll("SELECT * FROM cab_routes WHERE status = 'active' ORDER BY display_order ASC LIMIT 8");
                    foreach ($cab_routes as $route): 
                        // Get cheapest pricing for this route
                        $cheapest = $db->fetch("
                            SELECT MIN(crp.one_way_price) as min_price
                            FROM cab_route_pricing crp
                            WHERE crp.route_id = ? AND crp.status = 'active' AND crp.one_way_price > 0
                        ", [$route['id']]);
                        
                        $starting_price = $cheapest['min_price'] ?? 0;
                    ?>
                    <div class="card mb-3" style="border: none; border-radius: 0px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.15); transition: all 0.3s ease; overflow: hidden; background: white;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 25px rgba(102, 126, 234, 0.25)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(102, 126, 234, 0.15)'">
                        <!-- Route Header with Gradient -->
                        <div style="background: #1bbc9b; padding: 12px 15px;">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-map-marker-alt" style="color: white; font-size: 0.9rem;"></i>
                                    <span style="color: white; font-weight: 600; font-size: 0.85rem;"><?php echo htmlspecialchars($route['from_location']); ?></span>
                                </div>
                                <i class="fas fa-arrow-right" style="color: rgba(255,255,255,0.8); font-size: 0.75rem;"></i>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="color: white; font-weight: 600; font-size: 0.85rem;"><?php echo htmlspecialchars($route['to_location']); ?></span>
                                    <i class="fas fa-map-marker-alt" style="color: white; font-size: 0.9rem;"></i>
                                </div>
                            </div>
                            
                            <!-- Distance & Duration -->
                            <div style="display: flex; gap: 12px; justify-content: center;">
                                <?php if ($route['distance_km'] > 0): ?>
                                <div style="background: #1bbc9b; padding: 4px 10px; border-radius: 12px; display: flex; align-items: center; gap: 5px;">
                                    <i class="fas fa-road" style="color: white; font-size: 0.7rem;"></i>
                                    <span style="color: white; font-weight: 600; font-size: 0.7rem;"><?php echo $route['distance_km']; ?>km</span>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($route['estimated_duration'])): ?>
                                <div style="background: #1bbc9b; padding: 4px 10px; border-radius: 12px; display: flex; align-items: center; gap: 5px;">
                                    <i class="fas fa-clock" style="color: white; font-size: 0.7rem;"></i>
                                    <span style="color: white; font-weight: 600; font-size: 0.7rem;"><?php echo htmlspecialchars($route['estimated_duration']); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Card Body -->
                        <div class="card-body" style="padding: 15px; text-align: center;">
                            <!-- Pricing Section -->
                            <?php if ($starting_price > 0): ?>
                            <div style="margin-bottom: 12px;">
                                <div style="font-size: 0.75rem; color: #6c757d; font-weight: 500; margin-bottom: 5px;">Starts from</div>
                                <div style="font-size: 1.5rem; font-weight: 700; background: #1bbc9b; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                                    <?php echo formatPriceINR($starting_price); ?>
                                </div>
                            </div>
                            <?php else: ?>
                            <div style="margin-bottom: 12px;">
                                <div style="font-size: 0.9rem; color: #6c757d; font-weight: 500;">Price on request</div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- View Details Button -->
                            <a href="<?php echo BASE_URL; ?>cab-route-details.php?route_id=<?php echo $route['id']; ?>" class="btn w-100" style="background: #1bbc9b; color: white; border: none; border-radius: 10px; padding: 10px 15px; font-weight: 600; font-size: 0.8rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3); transition: all 0.3s ease;" onmouseover="this.style.boxShadow='0 6px 18px rgba(102, 126, 234, 0.5)'" onmouseout="this.style.boxShadow='0 4px 12px rgba(102, 126, 234, 0.3)'">
                                <i class="fas fa-info-circle"></i> View Details
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <!-- Help Card -->
                    <div class="card" style="border: none; border-radius: 12px; background: #1bbc9b; color: white; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
                        <div class="card-body text-center" style="padding: 15px 10px;">
                            <i class="fas fa-headset fa-2x mb-2"></i>
                            <h6 style="font-weight: 700; margin-bottom: 8px; font-size: 0.9rem;">Need Help?</h6>
                            <p style="font-size: 0.7rem; margin-bottom: 10px; opacity: 0.9;">24/7 support</p>
                            <a href="<?php echo navUrl('contact'); ?>" class="btn w-100" style="background: white; color: #667eea; border: none; border-radius: 8px; padding: 8px; font-weight: 600; font-size: 0.75rem;">Contact</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<style>
/* Swiper Container */
.swiper {
    padding: 10px 60px 60px 60px !important;
    overflow: visible !important;
}

.swiper-slide {
    height: auto !important;
}

.swiper-slide .card {
    height: 100% !important;
    display: flex !important;
    flex-direction: column !important;
}

.swiper-slide .card .card-body {
    flex: 1 !important;
    display: flex !important;
    flex-direction: column !important;
}

.swiper-slide .card .btn {
    margin-top: auto !important;
}

/* Navigation Buttons - Hidden */
.swiper-button-next,
.swiper-button-prev {
    display: none !important;
}

/* Pagination */
.swiper-pagination-bullet {
    background: #667eea;
    opacity: 0.5;
}

.swiper-pagination-bullet-active {
    opacity: 1;
    background: #667eea;
}

@media (max-width: 991px) {
    .cab-routes-sidebar {
        position: relative !important;
        top: 0 !important;
        margin-top: 30px;
    }
}

.card {
    border: none !important;
    border-radius: 0px !important; 
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1) !important;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
    overflow: hidden !important;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%) !important;
    position: relative !important;
}

.card:hover {
    transform: translateY(-8px) scale(1.02) !important;
    box-shadow: 0 25px 50px rgba(102, 126, 234, 0.15), 0 0 0 1px rgba(102, 126, 234, 0.1) !important;
}
</style>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
// Wait for everything to load
window.addEventListener('load', function() {
    console.log('Initializing Swipers...');
    
    <?php foreach ($destinations_list as $dest_init): ?>
    // Check if element exists
    var swiperEl = document.querySelector('.tours-swiper-<?php echo $dest_init['id']; ?>');
    if (swiperEl) {
        console.log('Found swiper element for destination <?php echo $dest_init['id']; ?>');
        
        var swiper<?php echo $dest_init['id']; ?> = new Swiper('.tours-swiper-<?php echo $dest_init['id']; ?>', {
            slidesPerView: 1,
            spaceBetween: 20,
            loop: true,
            pagination: {
                el: '.tours-swiper-<?php echo $dest_init['id']; ?> .swiper-pagination',
                clickable: true,
                dynamicBullets: true,
            },
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            breakpoints: {
                768: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 20,
                },
            },
            on: {
                init: function () {
                    console.log('Swiper <?php echo $dest_init['id']; ?> initialized successfully!');
                    console.log('Slides count: ' + this.slides.length);
                    console.log('Current slidesPerView: ' + this.params.slidesPerView);
                },
            },
        });
    } else {
        console.error('Swiper element not found for destination <?php echo $dest_init['id']; ?>');
    }
    <?php endforeach; ?>
});
</script>

