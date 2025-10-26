<!-- Destinations & Cab Facilities Section -->
<section class="destinations-cab-section section-space" style="background: #f8f9fa; padding: 60px 0;">
    <div class="container">
        <div class="row">
            <!-- 70% - Destinations & Tours -->
            <div class="col-lg-8 col-md-12 mb-4">
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
                            <?php echo htmlspecialchars($dest['name']); ?>, <?php echo htmlspecialchars($dest['country']); ?>
                        </h1>
                    </div>
                    
                    <!-- Tours Grid -->
                    <div class="row">
                        <?php foreach ($destination_tours as $tour): ?>
                        <div class="col-md-4 col-sm-6 mb-4">
                            <div class="card h-100" style="border: none; border-radius: 20px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: all 0.3s ease; position: relative;">
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
                                    <a href="<?php echo BASE_URL; ?>tour-details.php?id=<?php echo $tour['id']; ?>" class="btn w-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 12px; padding: 12px; font-weight: 600; transition: all 0.3s ease;">
                                        Explore Details
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
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
            
            <!-- 30% - Cab Routes Sidebar -->
            <div class="col-lg-4 col-md-12">
                <div class="cab-routes-sidebar" style="position: sticky; top: 80px;">
                    <div class="section-header mb-4">
                        <h3 style="font-size: 1.8rem; font-weight: 700; color: #333; margin-bottom: 10px;">
                            <i class="fas fa-route" style="color: #667eea;"></i> Cab Routes
                        </h3>
                        <p style="color: #6c757d; font-size: 0.9rem;">Book cabs for popular routes</p>
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
                    <div class="card mb-3" style="border: none; border-radius: 15px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); transition: all 0.3s ease; overflow: hidden;" onmouseover="this.style.transform='translateX(5px)'; this.style.boxShadow='0 5px 20px rgba(102, 126, 234, 0.2)'" onmouseout="this.style.transform='translateX(0)'; this.style.boxShadow='0 3px 10px rgba(0,0,0,0.1)'">
                        <!-- Header with gradient -->
                        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 12px 15px; color: white;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                                <span class="badge" style="background: rgba(255,255,255,0.2); padding: 4px 8px; border-radius: 8px; font-size: 0.7rem;"><?php echo htmlspecialchars($route['from_location']); ?></span>
                                <i class="fas fa-arrow-right" style="font-size: 0.7rem;"></i>
                                <span class="badge" style="background: rgba(255,255,255,0.2); padding: 4px 8px; border-radius: 8px; font-size: 0.7rem;"><?php echo htmlspecialchars($route['to_location']); ?></span>
                            </div>
                            <h6 style="margin: 0; font-size: 0.95rem; font-weight: 600;"><?php echo htmlspecialchars($route['route_name']); ?></h6>
                        </div>
                        
                        <!-- Body -->
                        <div class="card-body" style="padding: 15px;">
                            <!-- Route Info -->
                            <div style="display: flex; gap: 12px; margin-bottom: 10px; font-size: 0.8rem; color: #6c757d;">
                                <?php if ($route['distance_km'] > 0): ?>
                                <div>
                                    <i class="fas fa-road" style="color: #667eea;"></i>
                                    <span style="font-weight: 600;"><?php echo $route['distance_km']; ?> km</span>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($route['estimated_duration'])): ?>
                                <div>
                                    <i class="fas fa-clock" style="color: #667eea;"></i>
                                    <span style="font-weight: 600;"><?php echo htmlspecialchars($route['estimated_duration']); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Pricing -->
                            <?php if ($starting_price > 0): ?>
                            <div style="background: #f0f4ff; padding: 10px; border-radius: 10px; margin-bottom: 10px; text-align: center;">
                                <div style="font-size: 0.7rem; color: #667eea; text-transform: uppercase; font-weight: 600;">Starting From</div>
                                <div style="font-size: 1.4rem; font-weight: 700; color: #667eea;"><?php echo formatPriceINR($starting_price); ?></div>
                            </div>
                            <?php endif; ?>
                            
                            <a href="<?php echo BASE_URL; ?>cab-route-details.php?route_id=<?php echo $route['id']; ?>" class="btn w-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 10px; padding: 8px; font-weight: 600; font-size: 0.85rem;">
                                <i class="fas fa-eye"></i> View & Book
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <!-- Help Card -->
                    <div class="card" style="border: none; border-radius: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);">
                        <div class="card-body text-center" style="padding: 30px 20px;">
                            <i class="fas fa-headset fa-3x mb-3"></i>
                            <h5 style="font-weight: 700; margin-bottom: 10px;">Need Help?</h5>
                            <p style="font-size: 0.9rem; margin-bottom: 20px; opacity: 0.9;">Contact our 24/7 support team</p>
                            <a href="<?php echo navUrl('contact'); ?>" class="btn w-100" style="background: white; color: #667eea; border: none; border-radius: 10px; padding: 12px; font-weight: 600;">Contact Us</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
@media (max-width: 991px) {
    .cab-routes-sidebar {
        position: relative !important;
        top: 0 !important;
        margin-top: 30px;
    }
}
</style>
