<?php
/**
 * KV Wood Works - Home Page
 * Premium Home Interiors & Wooden Works
 */

// --- LAUNCH SYSTEM TRAP ---
session_start(); // Ensure session is started to check admin login
$launchConfig = __DIR__ . '/config/launch.json';
if (file_exists($launchConfig)) {
    $launchData = json_decode(file_get_contents($launchConfig), true);
    $status = $launchData['status'] ?? 'coming_soon';

    // Show Coming Soon IF: Status is 'coming_soon' AND User is NOT Admin
    if ($status === 'coming_soon' && (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true)) {
        include 'coming-soon.php';
        exit;
    }
}
// --------------------------

$pageTitle = 'Best Home Interior Designers in Chennai';
$pageDescription = 'KV Wood Works - Chennai\'s #1 home interior design company. Premium modular kitchen, wardrobe, bedroom, living room designs & custom wooden works in Maduravoyal. 500+ projects, 10+ years experience, 5-year warranty. Book FREE consultation today!';
$pageKeywords = 'home interior designers chennai, modular kitchen chennai price, wardrobe designs chennai, interior design maduravoyal, living room interior chennai, bedroom interior design, kitchen cabinets chennai, wooden furniture chennai, vasakal design, pooja room designs, TV unit designs, false ceiling chennai';

include 'includes/header.php';

// Fetch featured categories from database (with fallback)
try {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM categories WHERE is_featured = 1 ORDER BY display_order LIMIT 6");
    $categories = $stmt->fetchAll();

    $stmt = $db->query("SELECT * FROM projects WHERE is_featured = 1 ORDER BY created_at DESC LIMIT 4");
    $projects = $stmt->fetchAll();

    $stmt = $db->query("SELECT * FROM reviews WHERE is_featured = 1 ORDER BY created_at DESC LIMIT 3");
    $reviews = $stmt->fetchAll();
} catch (Exception $e) {
    $categories = [];
    $projects = [];
    $reviews = [];
}
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Transform Your Home Into A <span>Masterpiece</span></h1>
                <p>Premium home interiors and exquisite wooden works crafted with passion. From modular kitchens to
                    custom staircases, we bring your vision to life.</p>
                <div class="hero-buttons">
                    <a href="get-estimate.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-calculator"></i> Get Free Estimate
                    </a>
                    <a href="recent-projects.php" class="btn btn-secondary btn-lg">
                        <i class="fas fa-images"></i> View Our Work
                    </a>
                </div>
                <div class="hero-stats">
                    <div class="stat-item">
                        <h3><span class="counter" data-target="500">0</span>+</h3>
                        <p>Projects Completed</p>
                    </div>
                    <div class="stat-item">
                        <h3><span class="counter" data-target="30">0</span>+</h3>
                        <p>Years Experience</p>
                    </div>
                    <div class="stat-item">
                        <h3><span class="counter" data-target="100">0</span>%</h3>
                        <p>Client Satisfaction</p>
                    </div>
                </div>
            </div>
            <div class="hero-image">
                <div class="hero-image-wrapper">
                    <?php
                    // Get all hero banner images
                    $bannerDir = __DIR__ . '/assets/images/banners/';
                    $bannerImages = [];
                    if (is_dir($bannerDir)) {
                        $files = glob($bannerDir . 'hero-banner-*.{jpg,jpeg,png,webp}', GLOB_BRACE);
                        foreach ($files as $file) {
                            $bannerImages[] = 'assets/images/banners/' . basename($file);
                        }
                    }
                    sort($bannerImages);
                    ?>

                    <?php if (!empty($bannerImages)): ?>
                        <div class="hero-slider" id="heroSlider">
                            <?php foreach ($bannerImages as $index => $banner): ?>
                                <img src="<?php echo SITE_URL . '/' . $banner; ?>"
                                    alt="KV Wood Works Interior Design <?php echo $index + 1; ?>"
                                    class="hero-slide <?php echo $index === 0 ? 'active' : ''; ?>"
                                    style="width: 100%; height: 500px; object-fit: cover; border-radius: 20px;">
                            <?php endforeach; ?>
                        </div>
                        <style>
                            .hero-slider {
                                position: relative;
                                width: 100%;
                                height: 500px;
                            }

                            .hero-slide {
                                position: absolute;
                                top: 0;
                                left: 0;
                                opacity: 0;
                                transition: opacity 0.8s ease-in-out;
                            }

                            .hero-slide.active {
                                opacity: 1;
                            }
                        </style>
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                const slides = document.querySelectorAll('.hero-slide');
                                let currentSlide = 0;

                                if (slides.length > 1) {
                                    setInterval(function () {
                                        slides[currentSlide].classList.remove('active');
                                        currentSlide = (currentSlide + 1) % slides.length;
                                        slides[currentSlide].classList.add('active');
                                    }, 5000); // 5 seconds
                                }
                            });
                        </script>
                    <?php else: ?>
                        <div class="placeholder-image" style="height: 500px;">
                            <i class="fas fa-home"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="hero-badge">
                    <div class="hero-badge-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="hero-badge-text">
                        <h4>5 Years</h4>
                        <p>Warranty Guaranteed</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Explore Our Interior Designs</h2>
            <p>From stunning modular kitchens to elegant living rooms, discover designs that transform your space</p>
        </div>

        <div class="categories-grid">
            <?php
            $staticCategories = [
                ['name' => 'Modular Kitchen', 'slug' => 'modular-kitchen', 'image' => 'modular-kitchen.jpg', 'icon' => 'utensils', 'desc' => 'Modern & functional kitchens'],
                ['name' => 'Wardrobe Designs', 'slug' => 'wardrobe', 'image' => 'wardrobe.jpg', 'icon' => 'door-closed', 'desc' => 'Stylish storage solutions'],
                ['name' => 'Living Room', 'slug' => 'living-room', 'image' => 'living-room.jpg', 'icon' => 'couch', 'desc' => 'Elegant living spaces'],
                ['name' => 'Bedroom', 'slug' => 'bedroom', 'image' => 'bedroom.jpg', 'icon' => 'bed', 'desc' => 'Cozy bedroom interiors'],
                ['name' => 'Space Saving', 'slug' => 'space-saving', 'image' => 'space-saving.jpg', 'icon' => 'compress-arrows-alt', 'desc' => 'Smart space solutions'],
                ['name' => 'Wooden Works', 'slug' => 'wooden-works', 'image' => 'wooden-works.jpg', 'icon' => 'tree', 'desc' => 'Custom wooden crafts'],
            ];

            foreach ($staticCategories as $cat):
                $imagePath = 'assets/images/categories/interior/' . $cat['image'];
                $hasImage = file_exists(__DIR__ . '/' . $imagePath);
                ?>
                <a href="pages/interior-design/<?php echo $cat['slug']; ?>.php" class="category-card">
                    <?php if ($hasImage): ?>
                        <img src="<?php echo SITE_URL . '/' . $imagePath; ?>" alt="<?php echo $cat['name']; ?>"
                            style="width: 100%; height: 300px; object-fit: cover;">
                    <?php else: ?>
                        <div class="placeholder-image" style="height: 300px;">
                            <i class="fas fa-<?php echo $cat['icon']; ?>"></i>
                        </div>
                    <?php endif; ?>
                    <div class="category-overlay">
                        <h3>
                            <?php echo $cat['name']; ?>
                        </h3>
                        <p>
                            <?php echo $cat['desc']; ?>
                        </p>
                        <span class="category-link">Explore <i class="fas fa-arrow-right"></i></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <a href="home-interior-designs.php" class="btn btn-secondary">
                View All Designs <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
<?php show_banner('middle'); ?>
<!-- Explore Our Wooden Works Section -->
<section class="section section-dark">
    <div class="container">
        <div class="section-header">
            <h2 style="color: #fff;">Explore Our Exquisite Wooden Works</h2>
            <p style="color: rgba(255,255,255,0.8);">Handcrafted traditional and modern wooden designs for your home</p>
        </div>

        <div class="wooden-works-grid">
            <?php
            $woodenItems = [
                ['name' => 'Vasakal (Main Door)', 'slug' => 'vasakal', 'image' => 'vasakal.jpg', 'icon' => 'door-open', 'desc' => 'Handcrafted traditional main doors with intricate designs and premium teak wood finishing.'],
                ['name' => 'Window / Janal', 'slug' => 'window-janal', 'image' => 'window.jpg', 'icon' => 'border-all', 'desc' => 'Beautiful wooden windows with traditional janal patterns and modern finishing options.'],
                ['name' => 'Wooden Staircase', 'slug' => 'wooden-staircase', 'image' => 'staircase.jpg', 'icon' => 'stairs', 'desc' => 'Elegant spiral and straight staircases with premium wood railings and finishing.'],
            ];

            foreach ($woodenItems as $item):
                $imagePath = 'assets/images/categories/wooden-works/' . $item['image'];
                $hasImage = file_exists(__DIR__ . '/' . $imagePath);
                ?>
                <a href="pages/wooden-works/<?php echo $item['slug']; ?>.php" class="wooden-card">
                    <div class="wooden-card-image">
                        <?php if ($hasImage): ?>
                            <img src="<?php echo SITE_URL . '/' . $imagePath; ?>" alt="<?php echo $item['name']; ?>"
                                style="width: 100%; height: 280px; object-fit: cover;">
                        <?php else: ?>
                            <div class="placeholder-image" style="height: 280px;">
                                <i class="fas fa-<?php echo $item['icon']; ?>"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="wooden-card-content">
                        <h3><?php echo $item['name']; ?></h3>
                        <p><?php echo $item['desc']; ?></p>
                        <span class="explore-link">Explore <i class="fas fa-arrow-right"></i></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <a href="wooden-works.php" class="btn btn-primary">
                View All Wooden Works <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- Branded Materials Section -->
<section class="section brands-section">
    <div class="container">
        <div class="section-header">
            <h2>We Use Only Branded Materials</h2>
            <p>Premium quality materials from trusted global suppliers for lasting durability</p>
        </div>

        <?php
        $brandsConfig = json_decode(file_get_contents(__DIR__ . '/config/brands.json'), true);
        $allBrands = $brandsConfig['brands'] ?? [];
        $enabledBrands = array_filter($allBrands, fn($b) => $b['enabled']);
        ?>

        <div class="brands-grid">
            <?php foreach ($enabledBrands as $brand): ?>
                <div class="brand-item">
                    <div class="brand-logo">
                        <?php if (!empty($brand['image']) && file_exists(__DIR__ . '/assets/images/brands/' . $brand['image'])): ?>
                            <img src="<?php echo SITE_URL; ?>/assets/images/brands/<?php echo $brand['image']; ?>"
                                alt="<?php echo htmlspecialchars($brand['name']); ?>" class="brand-logo-img">
                        <?php else: ?>
                            <span class="brand-text"><?php echo htmlspecialchars($brand['name']); ?></span>
                        <?php endif; ?>
                    </div>
                    <p><?php echo htmlspecialchars($brand['description']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="brands-features">
            <div class="brand-feature">
                <i class="fas fa-award"></i>
                <h4>BWR/BWP Grade</h4>
                <p>Water-resistant plywood for durability</p>
            </div>
            <div class="brand-feature">
                <i class="fas fa-shield-alt"></i>
                <h4>10 Year Warranty</h4>
                <p>Manufacturer warranty on materials</p>
            </div>
            <div class="brand-feature">
                <i class="fas fa-leaf"></i>
                <h4>Eco-Friendly</h4>
                <p>Low emission, safe for families</p>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="section section-light">
    <div class="container">
        <div class="section-header">
            <h2>Why Choose KV Wood Works?</h2>
            <p>We deliver excellence in every project with our commitment to quality and craftsmanship</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-award"></i>
                </div>
                <h4>Premium Quality</h4>
                <p>We use only the finest materials - BWR/BWP grade plywood, branded hardware, and premium laminates for
                    lasting durability.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-pencil-ruler"></i>
                </div>
                <h4>Custom Designs</h4>
                <p>Every home is unique. Our expert designers create personalized solutions tailored to your taste and
                    space requirements.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h4>On-Time Delivery</h4>
                <p>We respect your time. Our streamlined process ensures your project is completed within the promised
                    timeline.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h4>5-Year Warranty</h4>
                <p>Complete peace of mind with our comprehensive 5-year warranty covering all modular furniture and
                    wooden works.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-rupee-sign"></i>
                </div>
                <h4>Transparent Pricing</h4>
                <p>No hidden costs. Get detailed quotations upfront with flexible payment options and easy EMI plans
                    available.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h4>Expert Support</h4>
                <p>Dedicated project managers and skilled craftsmen ensure smooth execution from design to installation.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Projects Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Our Recent Projects</h2>
            <p>Take a look at some of our latest home transformations</p>
        </div>

        <?php
        // Load projects from config - ONLY from admin panel
        $projectsConfigFile = __DIR__ . '/config/projects.json';
        $allProjects = [];
        if (file_exists($projectsConfigFile)) {
            $allProjects = json_decode(file_get_contents($projectsConfigFile), true) ?? [];
        }

        // Get the 4 most recent projects
        $displayProjects = array_slice(array_values($allProjects), 0, 4);
        ?>

        <?php if (!empty($displayProjects)): ?>
            <div class="projects-grid">
                <?php foreach ($displayProjects as $project):
                    $projectName = $project['name'] ?? $project['title'] ?? 'Project';
                    $location = $project['location'] ?? '';
                    $type = $project['property_type'] ?? ($project['tags'][0] ?? 'Interior');
                    $hasImage = !empty($project['images']);
                    $firstImage = $hasImage ? $project['images'][0] : '';
                    ?>
                    <div class="project-card">
                        <div class="project-image">
                            <?php if ($hasImage): ?>
                                <img src="<?php echo SITE_URL . '/' . $firstImage; ?>"
                                    alt="<?php echo htmlspecialchars($projectName); ?>"
                                    style="width: 100%; height: 280px; object-fit: cover;">
                            <?php else: ?>
                                <div class="placeholder-image" style="height: 280px;">
                                    <i class="fas fa-home"></i>
                                </div>
                            <?php endif; ?>
                            <span class="project-badge">
                                <?php echo htmlspecialchars($type); ?>
                            </span>
                        </div>
                        <div class="project-content">
                            <h4>
                                <?php echo htmlspecialchars($projectName); ?>
                            </h4>
                            <div class="project-meta">
                                <span><i class="fas fa-map-marker-alt"></i>
                                    <?php echo htmlspecialchars($location); ?>
                                </span>
                                <span><i class="fas fa-calendar"></i> <?php echo date('Y'); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="text-align: center; margin-top: 40px;">
                <a href="recent-projects.php" class="btn btn-primary">
                    View All Projects <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        <?php else: ?>
            <!-- No projects yet - Coming Soon -->
            <div style="text-align: center; padding: 60px 20px;">
                <i class="fas fa-hammer"
                    style="font-size: 4rem; color: var(--primary); opacity: 0.3; margin-bottom: 20px;"></i>
                <h3 style="color: #666;">Projects Coming Soon!</h3>
                <p style="color: #999; margin-bottom: 25px;">We're adding our latest project showcases. Check back soon!</p>
                <a href="contact.php" class="btn btn-primary">Contact Us</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Process Section -->
<section class="section section-dark">
    <div class="container">
        <div class="section-header">
            <h2 style="color: #fff;">How It Works</h2>
            <p style="color: rgba(255,255,255,0.8);">Our simple 4-step process to transform your home</p>
        </div>

        <div class="features-grid">
            <div class="feature-card"
                style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                <div class="feature-icon">
                    <span style="font-weight: 700; font-size: 1.5rem;">1</span>
                </div>
                <h4 style="color: #fff;">Book Consultation</h4>
                <p style="color: rgba(255,255,255,0.7);">Schedule a free consultation. Share your requirements and get
                    expert advice.</p>
            </div>

            <div class="feature-card"
                style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                <div class="feature-icon">
                    <span style="font-weight: 700; font-size: 1.5rem;">2</span>
                </div>
                <h4 style="color: #fff;">Design & Quote</h4>
                <p style="color: rgba(255,255,255,0.7);">Receive personalized 3D designs and transparent pricing for
                    your project.</p>
            </div>

            <div class="feature-card"
                style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                <div class="feature-icon">
                    <span style="font-weight: 700; font-size: 1.5rem;">3</span>
                </div>
                <h4 style="color: #fff;">Production</h4>
                <p style="color: rgba(255,255,255,0.7);">Your furniture is crafted with precision at our workshop using
                    premium materials.</p>
            </div>

            <div class="feature-card"
                style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                <div class="feature-icon">
                    <span style="font-weight: 700; font-size: 1.5rem;">4</span>
                </div>
                <h4 style="color: #fff;">Installation</h4>
                <p style="color: rgba(255,255,255,0.7);">Professional installation at your home. Quality checks and
                    handover.</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>What Our Customers Say</h2>
            <p>Real stories from happy homeowners who trusted us with their dream homes</p>
        </div>

        <?php
        // Reviews based on Google Maps - 4.9 stars, 57 reviews
        $staticReviews = [
            ['name' => 'Karthik Rajan', 'location' => 'Maduravoyal, Chennai', 'rating' => 5, 'text' => 'Excellent carpenter service in Chennai. KV Wood Works did an amazing modular kitchen for us. The quality of work is outstanding and they completed the project on time. Highly recommended!', 'type' => 'Modular Kitchen'],
            ['name' => 'Lakshmi Venkatesh', 'location' => 'Ambattur, Chennai', 'rating' => 5, 'text' => 'Very professional team. Got our bedroom wardrobe and TV unit done by KV Wood Works. The finishing is superb and they use only branded plywood. Best carpenters in Maduravoyal!', 'type' => 'Wardrobe & TV Unit'],
            ['name' => 'Arun Prakash', 'location' => 'Porur, Chennai', 'rating' => 5, 'text' => 'We got our complete 2BHK interior done by KV Wood Works. From kitchen cabinets to wardrobes, everything is perfect. The team is very cooperative and delivers quality work. Thank you KV Wood Works!', 'type' => 'Complete Interior'],
        ];
        ?>

        <div class="testimonials-slider">
            <?php foreach ($staticReviews as $review): ?>
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar">
                            <?php echo strtoupper(substr($review['name'], 0, 1)); ?>
                        </div>
                        <div class="testimonial-info">
                            <h4>
                                <?php echo $review['name']; ?>
                            </h4>
                            <p><i class="fas fa-map-marker-alt"></i>
                                <?php echo $review['location']; ?> •
                                <?php echo $review['type']; ?>
                            </p>
                        </div>
                    </div>
                    <div class="testimonial-rating">
                        <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                            <i class="fas fa-star"></i>
                        <?php endfor; ?>
                    </div>
                    <p class="testimonial-text">"
                        <?php echo $review['text']; ?>"
                    </p>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <a href="reviews.php" class="btn btn-secondary">
                Read All Reviews <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Transform Your Home?</h2>
            <p>Get a free consultation and estimate for your dream home interior. Our experts are ready to help you
                create something beautiful.</p>
            <div class="cta-buttons">
                <a href="get-estimate.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-calculator"></i> Get Free Estimate
                </a>
                <a href="tel:<?php echo SITE_PHONE; ?>" class="btn btn-white btn-lg">
                    <i class="fas fa-phone"></i> Call Now
                </a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>