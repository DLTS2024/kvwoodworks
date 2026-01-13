<?php
/**
 * KV Wood Works - Home Interior Designs
 * All interior design categories listing
 */

$pageTitle = 'Home Interior Design Services Chennai';
$pageDescription = 'Explore 1000+ home interior designs by KV Wood Works Chennai. Modular kitchen, wardrobe, bedroom, living room, pooja room, kid room & space saving designs. Get FREE 3D design consultation!';
$pageKeywords = 'home interior design chennai, modular kitchen designs, wardrobe designs chennai, bedroom interior, living room design, pooja room interior, kids room design, space saving furniture, 2bhk interior design, 3bhk interior chennai';

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Home Interior Designs</h1>
        <p>Transform every corner of your home with our premium interior solutions</p>
        <div class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>">Home</a>
            <span>/</span>
            <span>Interior Designs</span>
        </div>
    </div>
</section>

<!-- Interior Categories Grid -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Browse By Room</h2>
            <p>Select a category to explore designs tailored for each space</p>
        </div>

        <div class="categories-grid">
            <?php
            $interiorCategories = [
                ['name' => 'Modular Kitchen Designs', 'slug' => 'modular-kitchen', 'icon' => 'utensils', 'image' => 'modular-kitchen.jpg', 'desc' => 'Modern, functional kitchens with smart storage solutions and premium finishes.'],
                ['name' => 'Customize Your Kitchen', 'slug' => 'customize-kitchen', 'icon' => 'magic', 'image' => 'customize-kitchen.jpg', 'desc' => 'Create your dream kitchen with fully customizable layouts and materials.'],
                ['name' => 'Wardrobe Designs', 'slug' => 'wardrobe', 'icon' => 'door-closed', 'image' => 'wardrobe.jpg', 'desc' => 'Stylish and spacious wardrobes with sliding, hinged, and walk-in options.'],
                ['name' => 'Bedroom', 'slug' => 'bedroom', 'icon' => 'bed', 'image' => 'bedroom.jpg', 'desc' => 'Cozy and elegant bedroom interiors for peaceful retreats.'],
                ['name' => 'Living Room', 'slug' => 'living-room', 'icon' => 'couch', 'image' => 'living-room.jpg', 'desc' => 'Stunning living room designs with TV units, sofas, and false ceilings.'],
                ['name' => 'Kid Bedroom', 'slug' => 'kid-bedroom', 'icon' => 'child', 'image' => 'kid-bedroom.jpg', 'desc' => 'Fun, safe, and creative bedroom designs for your little ones.'],
                ['name' => 'Dining Room', 'slug' => 'dining-room', 'icon' => 'chair', 'image' => 'dining-room.jpg', 'desc' => 'Elegant dining spaces perfect for family meals and gatherings.'],
                ['name' => 'Pooja Room', 'slug' => 'pooja-room', 'icon' => 'praying-hands', 'image' => 'pooja-room.jpg', 'desc' => 'Sacred and serene pooja room designs with traditional touches.'],
                ['name' => 'Space Saving', 'slug' => 'space-saving', 'icon' => 'compress-arrows-alt', 'image' => 'space-saving.jpg', 'desc' => 'Smart furniture and designs to maximize small spaces.'],
                ['name' => 'Home Office', 'slug' => 'home-office', 'icon' => 'laptop-house', 'image' => 'home-office.jpg', 'desc' => 'Professional and productive home office setups.'],
                ['name' => 'Bathroom', 'slug' => 'bathroom', 'icon' => 'bath', 'image' => 'bathroom.jpg', 'desc' => 'Modern bathroom designs with premium fixtures and finishes.'],
                ['name' => 'Balcony', 'slug' => 'balcony', 'icon' => 'sun', 'image' => 'balcony.jpg', 'desc' => 'Transform your balcony into a beautiful outdoor retreat.'],
            ];

            foreach ($interiorCategories as $cat):
                $imagePath = 'assets/images/categories/interior/' . $cat['image'];
                $hasImage = file_exists(__DIR__ . '/' . $imagePath);
                ?>
                <a href="pages/interior-design/<?php echo $cat['slug']; ?>.php" class="category-card">
                    <?php if ($hasImage): ?>
                        <img src="<?php echo SITE_URL . '/' . $imagePath; ?>" alt="<?php echo $cat['name']; ?>"
                            style="height: 300px; object-fit: cover;">
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
                        <span class="category-link">Explore Designs <i class="fas fa-arrow-right"></i></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- BHK Section -->
<section class="section section-light">
    <div class="container">
        <div class="section-header">
            <h2>Complete Home Packages</h2>
            <p>All-inclusive interior solutions for your entire home</p>
        </div>

        <div class="categories-grid" style="max-width: 800px; margin: 0 auto;">
            <a href="pages/bhk/1bhk.php" class="category-card">
                <?php if (file_exists(__DIR__ . '/assets/images/categories/interior/1bhk-interior.jpg')): ?>
                    <img src="<?php echo SITE_URL; ?>/assets/images/categories/interior/1bhk-interior.jpg"
                        alt="1 BHK Interior" style="height: 350px; object-fit: cover;">
                <?php else: ?>
                    <div class="placeholder-image" style="height: 350px;">
                        <i class="fas fa-home"></i>
                    </div>
                <?php endif; ?>
                <div class="category-overlay">
                    <h3>1 BHK Interior</h3>
                    <p>Complete interior solutions for 1 BHK apartments with smart space utilization.</p>
                    <span class="category-link">View Packages <i class="fas fa-arrow-right"></i></span>
                </div>
            </a>

            <a href="pages/bhk/2bhk.php" class="category-card">
                <?php if (file_exists(__DIR__ . '/assets/images/categories/interior/2bhk-interior.jpg')): ?>
                    <img src="<?php echo SITE_URL; ?>/assets/images/categories/interior/2bhk-interior.jpg"
                        alt="2 BHK Interior" style="height: 350px; object-fit: cover;">
                <?php else: ?>
                    <div class="placeholder-image" style="height: 350px;">
                        <i class="fas fa-building"></i>
                    </div>
                <?php endif; ?>
                <div class="category-overlay">
                    <h3>2 BHK Interior</h3>
                    <p>Comprehensive interior packages for 2 BHK homes with all rooms covered.</p>
                    <span class="category-link">View Packages <i class="fas fa-arrow-right"></i></span>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2>Need Help Choosing?</h2>
            <p>Our interior design experts are here to guide you. Get a free consultation and personalized
                recommendations.</p>
            <div class="cta-buttons">
                <a href="get-estimate.php" class="btn btn-primary btn-lg">Get Free Consultation</a>
                <a href="contact.php" class="btn btn-white btn-lg">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>