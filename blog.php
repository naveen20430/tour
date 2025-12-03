<?php
require_once 'config/config.php';

// Set page variables
$page_title = 'Travel Blog - ' . getSetting('site_name');
$current_page = 'blog';

// Get search parameters
$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';
$page = $_GET['page'] ?? 1;
$per_page = 6;
$offset = ($page - 1) * $per_page;

// Build query
$where_conditions = ['bp.status = "published"'];
$params = [];

if ($category) {
    $where_conditions[] = 'bc.slug = ?';
    $params[] = $category;
}

if ($search) {
    $where_conditions[] = '(bp.title LIKE ? OR bp.content LIKE ? OR bp.excerpt LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$where_clause = implode(' AND ', $where_conditions);

// Get blog posts
$blog_posts = $db->fetchAll("
    SELECT bp.*, bc.name as category_name, bc.slug as category_slug, au.full_name as author_name
    FROM blog_posts bp
    LEFT JOIN blog_categories bc ON bp.category_id = bc.id
    LEFT JOIN admin_users au ON bp.author_id = au.id
    WHERE $where_clause
    ORDER BY bp.published_at DESC, bp.created_at DESC
    LIMIT $per_page OFFSET $offset
", $params);

// Get total count for pagination
$total_posts = $db->fetch("
    SELECT COUNT(*) as total
    FROM blog_posts bp
    LEFT JOIN blog_categories bc ON bp.category_id = bc.id
    WHERE $where_clause
", $params)['total'];

$total_pages = ceil($total_posts / $per_page);

// Get categories for filter
$categories = $db->fetchAll("SELECT * FROM blog_categories WHERE status = 'active' ORDER BY name");

// Get featured posts
$featured_posts = $db->fetchAll("
    SELECT bp.*, bc.name as category_name, bc.slug as category_slug, au.full_name as author_name
    FROM blog_posts bp
    LEFT JOIN blog_categories bc ON bp.category_id = bc.id
    LEFT JOIN admin_users au ON bp.author_id = au.id
    WHERE bp.status = 'published' AND bp.featured = 1
    ORDER BY bp.published_at DESC
    LIMIT 3
");

// Set extra CSS for blog page
$extra_css = '
<style>
    .blog-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        border-radius: 15px;
        overflow: hidden;
        margin-bottom: 30px;
        height: 100%;
    }
    .blog-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }
    .blog-image {
        height: 200px;
        object-fit: cover;
        width: 100%;
    }
    .blog-hero {
        background: #1bbc9b;
        color: white;
        padding: 80px 0;
        text-align: center;
    }
    .featured-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: #ff6b6b;
        color: white;
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 0.8em;
        font-weight: bold;
    }
    .category-badge {
        background: #1bbc9b;
        color: white;
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 0.8em;
        text-decoration: none;
    }
    
    /* Blog-specific responsive improvements */
    @media (max-width: 767px) {
        .blog-hero {
            padding: 40px 0 25px;
        }
        
        .blog-hero h1 {
            font-size: 1.8rem;
        }
        
        .blog-hero p {
            font-size: 1rem;
        }
        
        .blog-image {
            height: 180px;
        }
        
        .search-toggler {
            display: none !important; /* Hide search on mobile for blog page */
        }
    }
    
    @media (max-width: 575px) {
        .blog-hero h1 {
            font-size: 1.5rem;
        }
        
        .blog-hero p {
            font-size: 0.9rem;
        }
        
        .blog-image {
            height: 160px;
        }
        
        .featured-badge,
        .category-badge {
            font-size: 0.65rem;
            padding: 3px 8px;
        }
    }
</style>
';

// Include header
include 'includes/header.php';
?>

    <!-- Blog Hero -->
    <section class="blog-hero">
        <div class="container">
            <h1 class="display-4 fw-bold">Travel Stories & Tips</h1>
            <p class="lead">Discover amazing travel experiences, helpful tips, and inspiring stories from around the world</p>
        </div>
    </section>

    <!-- Search & Filter -->
    <section class="py-4" style="background: #f8f9fa;">
        <div class="container">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Search Posts</label>
                    <input type="text" name="search" class="form-control" placeholder="Search blog posts..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['slug']; ?>" <?php echo $category == $cat['slug'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
                <div class="col-md-3 text-end">
                    <?php if ($search || $category): ?>
                        <a href="blog" class="btn btn-outline-secondary">Clear Filters</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </section>

    <!-- Featured Posts -->
    <?php if (!$search && !$category && !empty($featured_posts)): ?>
    <section class="section-space">
        <div class="container">
            <div class="section-title text-center mb-5">
                <span class="section-title__tagline">Featured Stories</span>
                <h2 class="section-title__title">Must-Read Travel Posts</h2>
            </div>
            
            <div class="row">
                <?php foreach ($featured_posts as $post): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card blog-card shadow-sm h-100">
                            <div class="position-relative">
                                <?php 
                                $image_path = !empty($post['featured_image']) && file_exists($post['featured_image']) 
                                    ? BASE_URL . htmlspecialchars($post['featured_image']) 
                                    : BASE_URL . 'assets/images/blog/default-blog.jpg';
                                ?>
                                <img src="<?php echo $image_path; ?>" 
                                     class="blog-image" alt="<?php echo htmlspecialchars($post['title']); ?>"
                                     onerror="this.src='<?php echo BASE_URL; ?>assets/images/blog/default-blog.jpg'">
                                <span class="featured-badge">Featured</span>
                            </div>
                            
                            <div class="card-body d-flex flex-column">
                                <div class="mb-2">
                                    <?php if ($post['category_name']): ?>
                                        <a href="blog?category=<?php echo $post['category_slug']; ?>" class="category-badge">
                                            <?php echo htmlspecialchars($post['category_name']); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                                
                                <h5 class="card-title">
                                    <a href="blog/<?php echo $post['slug']; ?>" class="text-decoration-none text-dark">
                                        <?php echo htmlspecialchars($post['title']); ?>
                                    </a>
                                </h5>
                                
                                <p class="card-text text-muted flex-grow-1">
                                    <?php echo htmlspecialchars(substr($post['excerpt'], 0, 120)); ?>...
                                </p>
                                
                                <div class="mt-auto">
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($post['author_name']); ?>
                                        <i class="fas fa-calendar ms-3 me-1"></i><?php echo date('M d, Y', strtotime($post['published_at'])); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Blog Posts Grid -->
    <section class="section-space" <?php echo (!$search && !$category && !empty($featured_posts)) ? 'style="padding-top: 0;"' : ''; ?>>
        <div class="container">
            <div class="section-title text-center mb-5">
                <span class="section-title__tagline">
                    <?php if ($category): ?>
                        <?php 
                        $current_category = array_filter($categories, function($cat) use ($category) {
                            return $cat['slug'] === $category;
                        });
                        echo htmlspecialchars(reset($current_category)['name'] ?? 'Category');
                        ?>
                    <?php elseif ($search): ?>
                        Search Results
                    <?php else: ?>
                        Latest Posts
                    <?php endif; ?>
                </span>
                <h2 class="section-title__title">
                    <?php if ($search): ?>
                        Results for "<?php echo htmlspecialchars($search); ?>"
                    <?php else: ?>
                        Travel Blog Posts
                    <?php endif; ?>
                </h2>
            </div>

            <?php if (empty($blog_posts)): ?>
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-search fa-4x text-muted"></i>
                    </div>
                    <h3 class="text-muted">No Posts Found</h3>
                    <p class="text-muted">Try adjusting your search terms or browse all posts.</p>
                    <a href="blog" class="btn btn-primary">View All Posts</a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($blog_posts as $post): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card blog-card shadow-sm h-100">
                                <div class="position-relative">
                                    <?php 
                                    $image_path = !empty($post['featured_image']) && file_exists($post['featured_image']) 
                                        ? BASE_URL . htmlspecialchars($post['featured_image']) 
                                        : BASE_URL . 'assets/images/blog/default-blog.jpg';
                                    ?>
                                    <img src="<?php echo $image_path; ?>" 
                                         class="blog-image" alt="<?php echo htmlspecialchars($post['title']); ?>"
                                         onerror="this.src='<?php echo BASE_URL; ?>assets/images/blog/default-blog.jpg'">
                                    
                                    <?php if ($post['featured']): ?>
                                        <span class="featured-badge">Featured</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="card-body d-flex flex-column">
                                    <div class="mb-2">
                                        <?php if ($post['category_name']): ?>
                                            <a href="blog?category=<?php echo $post['category_slug']; ?>" class="category-badge">
                                                <?php echo htmlspecialchars($post['category_name']); ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <h5 class="card-title">
                                        <a href="blog/<?php echo $post['slug']; ?>" class="text-decoration-none text-dark">
                                            <?php echo htmlspecialchars($post['title']); ?>
                                        </a>
                                    </h5>
                                    
                                    <p class="card-text text-muted flex-grow-1">
                                        <?php echo htmlspecialchars(substr($post['excerpt'], 0, 120)); ?>...
                                    </p>
                                    
                                    <div class="mt-auto">
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($post['author_name']); ?>
                                            <i class="fas fa-calendar ms-3 me-1"></i><?php echo date('M d, Y', strtotime($post['published_at'])); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="row mt-5">
                        <div class="col-12">
                            <nav aria-label="Blog pagination">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page-1; ?><?php echo $category ? '&category='.$category : ''; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>">
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = max(1, $page-2); $i <= min($total_pages, $page+2); $i++): ?>
                                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?><?php echo $category ? '&category='.$category : ''; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page+1; ?><?php echo $category ? '&category='.$category : ''; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>">
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>
