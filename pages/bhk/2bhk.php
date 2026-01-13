<?php
/**
 * BHK Interior Page Template
 */

$slug = basename($_SERVER['PHP_SELF'], '.php');

$bhkInfo = [
    '1bhk' => [
        'title' => '1 BHK Interior Design',
        'description' => 'Complete interior solutions for 1 BHK apartments with smart space utilization and modern designs.',
        'rooms' => ['Living Room', 'Bedroom', 'Kitchen', 'Bathroom'],
        'price' => '3.5 - 6 Lakhs'
    ],
    '2bhk' => [
        'title' => '2 BHK Interior Design',
        'description' => 'Comprehensive interior packages for 2 BHK homes covering all rooms with premium finishes.',
        'rooms' => ['Living Room', 'Master Bedroom', 'Second Bedroom', 'Kitchen', 'Bathrooms'],
        'price' => '6 - 12 Lakhs'
    ]
];

$info = $bhkInfo[$slug] ?? $bhkInfo['2bhk'];

$pageTitle = $info['title'];
$pageDescription = $info['description'];

require_once __DIR__ . '/../../config/database.php';
include __DIR__ . '/../../includes/header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>
            <?php echo $info['title']; ?>
        </h1>
        <p>
            <?php echo $info['description']; ?>
        </p>
        <div class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>">Home</a>
            <span>/</span>
            <a href="<?php echo baseUrl('home-interior-designs.php'); ?>">Interior Designs</a>
            <span>/</span>
            <span>
                <?php echo $info['title']; ?>
            </span>
        </div>
    </div>
</section>

<!-- Package Overview -->
<section class="section">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center;">
            <div>
                <h2>Complete
                    <?php echo strtoupper($slug); ?> Interior Package
                </h2>
                <p style="font-size: 1.1rem; margin-bottom: 25px;">Get your entire
                    <?php echo strtoupper($slug); ?> apartment designed with our all-inclusive package. We cover every
                    room from kitchen to bedroom with premium quality materials and modern designs.
                </p>

                <div
                    style="background: var(--light-gray); padding: 25px; border-radius: var(--radius-lg); margin-bottom: 25px;">
                    <h4 style="color: var(--primary); margin-bottom: 15px;">Package Includes:</h4>
                    <ul style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <?php foreach ($info['rooms'] as $room): ?>
                            <li style="display: flex; align-items: center; gap: 10px;">
                                <i class="fas fa-check-circle" style="color: var(--accent);"></i>
                                <?php echo $room; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div style="display: flex; gap: 20px; align-items: center;">
                    <div>
                        <p style="color: var(--gray); margin: 0; font-size: 0.9rem;">Starting From</p>
                        <h3 style="color: var(--primary); margin: 0;">₹
                            <?php echo $info['price']; ?>
                        </h3>
                    </div>
                    <a href="<?php echo baseUrl('get-estimate.php'); ?>" class="btn btn-primary btn-lg">Get Exact
                        Quote</a>
                </div>
            </div>
            <div>
                <div class="placeholder-image" style="height: 400px; border-radius: var(--radius-lg);">
                    <i class="fas fa-home"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Room-wise Designs -->
<section class="section section-light">
    <div class="container">
        <div class="section-header">
            <h2>Room-wise Design Highlights</h2>
            <p>See what's included in each room</p>
        </div>

        <div class="categories-grid">
            <?php
            $roomDetails = [
                ['name' => 'Living Room', 'icon' => 'couch', 'items' => 'TV Unit, False Ceiling, Shoe Rack'],
                ['name' => 'Kitchen', 'icon' => 'utensils', 'items' => 'Modular Cabinets, Chimney, Accessories'],
                ['name' => 'Bedroom', 'icon' => 'bed', 'items' => 'Wardrobe, Bed, Side Tables'],
                ['name' => 'Bathroom', 'icon' => 'bath', 'items' => 'Vanity, Mirror Cabinet, Accessories']
            ];

            foreach ($roomDetails as $room): ?>
                <div class="category-card">
                    <div class="placeholder-image" style="height: 250px;">
                        <i class="fas fa-<?php echo $room['icon']; ?>"></i>
                    </div>
                    <div class="category-overlay">
                        <h3>
                            <?php echo $room['name']; ?>
                        </h3>
                        <p>
                            <?php echo $room['items']; ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Design Your
                <?php echo strtoupper($slug); ?>?
            </h2>
            <p>Get a customized quote for your apartment. Free consultation included!</p>
            <div class="cta-buttons">
                <a href="<?php echo baseUrl('get-estimate.php'); ?>" class="btn btn-primary btn-lg">Get Free
                    Estimate</a>
                <a href="<?php echo baseUrl('recent-projects.php'); ?>" class="btn btn-white btn-lg">View Projects</a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>