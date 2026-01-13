<?php
/**
 * KV Wood Works - Wooden Works
 * Custom wooden works services
 */

$pageTitle = 'Wooden Works - Vasakal, Window, Staircase Chennai';
$pageDescription = 'Premium wooden works in Chennai - Traditional Vasakal (door frames), custom windows, Janal designs & elegant wooden staircases. Teak wood, Sal wood experts. Get FREE quote!';
$pageKeywords = 'wooden works chennai, vasakal design, door frame design chennai, wooden window design, janal design, wooden staircase chennai, teak wood furniture, custom wooden works, traditional door frames, wooden door designs';

include 'includes/header.php';

// Define all wooden works categories with their subcategories
$woodenCategories = [
    'vasakal' => [
        'title' => 'Vasakal Frame',
        'icon' => 'door-open',
        'items' => [
            ['name' => 'Main Vasakal', 'slug' => 'main-vasakal', 'image' => 'vasakal.jpg', 'icon' => 'door-open', 'desc' => 'Traditional main entrance vasakal frames'],
            ['name' => 'Pooja Room Vasakal', 'slug' => 'pooja-room-vasakal', 'image' => 'pooja-room-vasakal.jpg', 'icon' => 'pray', 'desc' => 'Sacred pooja room vasakal designs'],
            ['name' => 'Bedroom Vasakal', 'slug' => 'bedroom-vasakal', 'image' => 'bedroom-door.jpg', 'icon' => 'bed', 'desc' => 'Elegant bedroom vasakal frames'],
            ['name' => 'WPVC Bathroom Vasakal', 'slug' => 'wpvc-bathroom-vasakal', 'image' => 'wpvc-bathroom-vasakal.jpg', 'icon' => 'bath', 'desc' => 'Waterproof WPVC vasakal for bathrooms'],
            ['name' => 'WPVC Balcony Vasakal', 'slug' => 'wpvc-balcony-vasakal', 'image' => 'wpvc-balcony-vasakal.jpg', 'icon' => 'building', 'desc' => 'Weather-resistant balcony vasakal'],
            ['name' => 'French Window Vasakal', 'slug' => 'french-window-vasakal', 'image' => 'french-window-door.jpg', 'icon' => 'window-maximize', 'desc' => 'Stylish French window vasakal frames'],
        ]
    ],
    'doors' => [
        'title' => 'Doors',
        'icon' => 'door-closed',
        'items' => [
            ['name' => 'Main Door', 'slug' => 'main-door', 'image' => 'main-door.jpg', 'icon' => 'door-open', 'desc' => 'Grand main entrance doors'],
            ['name' => 'Main Door + Safety', 'slug' => 'main-door-safety', 'image' => 'main-door-+-safety.jpg', 'icon' => 'shield-alt', 'desc' => 'Secure main door with safety door'],
            ['name' => 'Pooja Room Door', 'slug' => 'pooja-room-door', 'image' => 'pooja-room-door.jpg', 'icon' => 'pray', 'desc' => 'Sacred pooja room doors'],
            ['name' => 'Bedroom Door', 'slug' => 'bedroom-door', 'image' => 'bedroom-door.jpg', 'icon' => 'bed', 'desc' => 'Elegant bedroom doors'],
            ['name' => 'WPVC Bathroom Door', 'slug' => 'wpvc-bathroom-door', 'image' => 'wpvc-bathroom-door.jpg', 'icon' => 'bath', 'desc' => 'Waterproof WPVC bathroom doors'],
            ['name' => 'PVC Bathroom Door', 'slug' => 'pvc-bathroom-door', 'image' => 'pvc-bathroom-door.jpg', 'icon' => 'bath', 'desc' => 'Affordable PVC bathroom doors'],
            ['name' => 'Balcony Door', 'slug' => 'balcony-door', 'image' => 'balcony-door.jpg', 'icon' => 'building', 'desc' => 'Weather-resistant balcony doors'],
            ['name' => 'French Window Door', 'slug' => 'french-window-door', 'image' => 'french-window-door.jpg', 'icon' => 'window-maximize', 'desc' => 'Elegant French-style doors'],
            ['name' => 'Double Door', 'slug' => 'double-door', 'image' => 'double-door.jpg', 'icon' => 'columns', 'desc' => 'Grand double doors'],
        ]
    ],
    'windows' => [
        'title' => 'Windows',
        'icon' => 'window-maximize',
        'items' => [
            ['name' => 'Window', 'slug' => 'window', 'image' => 'window.jpg', 'icon' => 'window-maximize', 'desc' => 'Classic wooden windows'],
            ['name' => 'Double Door Window', 'slug' => 'double-door-window', 'image' => 'double-door-window.jpg', 'icon' => 'columns', 'desc' => 'Large double-door windows'],
            ['name' => 'Vasakal Ottu Sakkai', 'slug' => 'ottu-sakkai', 'image' => 'vasakal-ottu-sakkai.jpg', 'icon' => 'palette', 'desc' => 'Traditional Ottu Sakkai designs'],
        ]
    ],
    'staircase' => [
        'title' => 'Staircase',
        'icon' => 'stairs',
        'items' => [
            ['name' => 'Round Staircase', 'slug' => 'round-staircase', 'image' => 'staircase.jpg', 'icon' => 'sync', 'desc' => 'Elegant spiral staircases'],
            ['name' => 'Straight Staircase', 'slug' => 'straight-staircase', 'image' => 'stright-staircase.jpg', 'icon' => 'arrow-up', 'desc' => 'Classic straight staircases'],
        ]
    ],
    'furniture' => [
        'title' => 'Furniture',
        'icon' => 'couch',
        'items' => [
            ['name' => 'Sofa Set', 'slug' => 'sofa-set', 'image' => 'sofa-set.jpg', 'icon' => 'couch', 'desc' => 'Premium wooden sofa sets'],
            ['name' => 'Diwan', 'slug' => 'diwan', 'image' => 'diwan.jpg', 'icon' => 'bed', 'desc' => 'Traditional diwan cots'],
            ['name' => 'Kattil (Bed)', 'slug' => 'kattil-bed', 'image' => 'kattil-bed.jpg', 'icon' => 'bed', 'desc' => 'Traditional kattil wooden beds'],
            ['name' => 'Dining Table', 'slug' => 'dining-table', 'image' => 'dining-table.jpg', 'icon' => 'utensils', 'desc' => 'Dining sets with chairs'],
            ['name' => 'Chair', 'slug' => 'chair', 'image' => 'furniture.jpg', 'icon' => 'chair', 'desc' => 'Handcrafted wooden chairs'],
            ['name' => 'Bero / Wardrobe', 'slug' => 'wardrobe', 'image' => 'furniture.jpg', 'icon' => 'archive', 'desc' => 'Spacious wooden wardrobes'],
            ['name' => 'Pooja Mandapam', 'slug' => 'pooja-mandapam', 'image' => 'pooja-mandapam.jpg', 'icon' => 'pray', 'desc' => 'Sacred pooja mandapam'],
        ]
    ],
];
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Wooden Works</h1>
        <p>Exquisite craftsmanship in traditional and modern wooden works</p>
        <div class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>">Home</a>
            <span>/</span>
            <span>Wooden Works</span>
        </div>
    </div>
</section>

<!-- Wooden Works Categories -->
<?php foreach ($woodenCategories as $categoryKey => $category): ?>
    <section
        class="section <?php echo ($categoryKey === 'doors' || $categoryKey === 'staircase') ? 'section-light' : ''; ?>">
        <div class="container">
            <div class="section-header">
                <h2><i class="fas fa-<?php echo $category['icon']; ?>"
                        style="color: var(--primary); margin-right: 10px;"></i><?php echo $category['title']; ?></h2>
                <p>Explore our collection of premium <?php echo strtolower($category['title']); ?></p>
            </div>

            <div class="categories-grid">
                <?php foreach ($category['items'] as $item):
                    $imagePath = 'assets/images/categories/wooden-works/' . ($item['image'] ?? $item['slug'] . '.jpg');
                    $hasImage = file_exists(__DIR__ . '/' . $imagePath);
                    ?>
                    <a href="pages/wooden-works/<?php echo $item['slug']; ?>.php" class="category-card">
                        <?php if ($hasImage): ?>
                            <img src="<?php echo SITE_URL . '/' . $imagePath; ?>" alt="<?php echo $item['name']; ?>"
                                style="height: 220px; object-fit: cover;">
                        <?php else: ?>
                            <div class="placeholder-image" style="height: 220px;">
                                <i class="fas fa-<?php echo $item['icon']; ?>"></i>
                            </div>
                        <?php endif; ?>
                        <div class="category-overlay">
                            <h3><?php echo $item['name']; ?></h3>
                            <p><?php echo $item['desc']; ?></p>
                            <span class="category-link">Explore Designs <i class="fas fa-arrow-right"></i></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endforeach; ?>

<!-- Why Our Wooden Works -->
<section class="section section-light">
    <div class="container">
        <div class="section-header">
            <h2>Why Choose Our Wooden Works?</h2>
            <p>Quality craftsmanship that stands the test of time</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-tree"></i>
                </div>
                <h4>Premium Wood</h4>
                <p>We use only the finest quality seasoned wood - Teak, Sal, and other hardwoods for lasting durability.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-paint-brush"></i>
                </div>
                <h4>Traditional Craftsmanship</h4>
                <p>Our skilled artisans bring generations of expertise in traditional carving and joinery techniques.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-ruler-combined"></i>
                </div>
                <h4>Custom Designs</h4>
                <p>From traditional to contemporary, we create custom designs tailored to your home's architecture.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h4>Termite Treatment</h4>
                <p>All wooden works undergo thorough anti-termite treatment for long-lasting protection.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2>Looking for Custom Wooden Works?</h2>
            <p>Share your requirements and let our craftsmen create something beautiful for your home.</p>
            <div class="cta-buttons">
                <a href="get-estimate.php" class="btn btn-primary btn-lg">Get Free Quote</a>
                <a href="contact.php" class="btn btn-white btn-lg">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>