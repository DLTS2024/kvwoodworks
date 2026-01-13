<?php
/**
 * KV Wood Works - Home Interior Guides
 * Dynamically loads guides from config/guides.json
 */

$pageTitle = 'Home Interior Design Guides';
$pageDescription = 'Expert home interior design guides by KV Wood Works. Get tips for modular kitchen, bedroom, living room, wooden works, pooja room & more. Free design advice!';
$pageKeywords = 'home interior guides, kitchen design tips, bedroom design ideas, living room guide, wooden works guide, modular kitchen guide, wardrobe design tips, pooja room ideas';

// Load guides data from JSON
$guidesFile = __DIR__ . '/config/guides.json';
$guidesData = [];
$categories = [];
$guides = [];

if (file_exists($guidesFile)) {
    $guidesData = json_decode(file_get_contents($guidesFile), true) ?? [];
    $categories = $guidesData['categories'] ?? [];
    $guides = $guidesData['guides'] ?? [];
}

// Filter only enabled guides
$guides = array_filter($guides, fn($g) => $g['enabled'] ?? true);

// Group guides by category
$guidesByCategory = [];
foreach ($guides as $guide) {
    $cat = $guide['category'];
    if (!isset($guidesByCategory[$cat])) {
        $guidesByCategory[$cat] = [];
    }
    $guidesByCategory[$cat][] = $guide;
}

// Get category info by ID
function getCategoryInfo($catId, $categories)
{
    foreach ($categories as $cat) {
        if ($cat['id'] === $catId) {
            return $cat;
        }
    }
    return ['id' => $catId, 'name' => ucfirst($catId), 'icon' => 'folder'];
}

include 'includes/header.php';
?>

<style>
    /* Guides Page Styles */
    .guides-hero {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        color: #fff;
        padding: 80px 0;
        text-align: center;
    }

    .guides-hero h1 {
        font-size: 3rem;
        margin-bottom: 15px;
    }

    .guides-hero p {
        font-size: 1.2rem;
        color: rgba(255, 255, 255, 0.8);
        max-width: 600px;
        margin: 0 auto 30px;
    }

    .guide-categories {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 15px;
        margin-top: 40px;
    }

    .guide-category-btn {
        padding: 12px 25px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #fff;
        border-radius: 30px;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
    }

    .guide-category-btn:hover,
    .guide-category-btn.active {
        background: var(--primary);
        border-color: var(--primary);
    }

    /* Guide Section */
    .guide-section {
        padding: 60px 0;
        border-bottom: 1px solid #eee;
    }

    .guide-section:last-child {
        border-bottom: none;
    }

    .guide-section-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 30px;
    }

    .guide-section-icon {
        width: 50px;
        height: 50px;
        background: var(--gradient-primary);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.3rem;
    }

    .guide-section-header h2 {
        font-size: 1.8rem;
        margin: 0;
    }

    /* Guide Cards Grid */
    .guides-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 25px;
    }

    .guide-card {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s;
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .guide-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }

    .guide-card-image {
        height: 200px;
        background: linear-gradient(135deg, #f5f5f5, #eee);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .guide-card-image i {
        font-size: 3rem;
        color: var(--primary);
        opacity: 0.5;
    }

    .guide-card-image img.guide-thumb {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .guide-card-image .color-preview {
        display: flex;
        gap: 8px;
    }

    .guide-card-image .color-dot {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 3px solid #fff;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .guide-card-content {
        padding: 25px;
    }

    .guide-card-tag {
        display: inline-block;
        padding: 5px 12px;
        background: rgba(200, 149, 108, 0.15);
        color: var(--primary);
        font-size: 0.8rem;
        font-weight: 600;
        border-radius: 20px;
        margin-bottom: 12px;
    }

    .guide-card h3 {
        font-size: 1.2rem;
        margin-bottom: 10px;
        line-height: 1.4;
    }

    .guide-card p {
        font-size: 0.95rem;
        color: #666;
        margin-bottom: 15px;
        line-height: 1.6;
    }

    .guide-card-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .guide-card-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--primary);
        font-weight: 600;
        font-size: 0.95rem;
    }

    .guide-card-link i {
        transition: transform 0.3s;
    }

    .guide-card:hover .guide-card-link i {
        transform: translateX(5px);
    }

    .guide-read-time {
        font-size: 0.85rem;
        color: #999;
    }

    .guide-read-time i {
        margin-right: 5px;
    }

    /* Quick Tips Section */
    .quick-tips {
        background: linear-gradient(135deg, #f8f4f0, #fff);
        padding: 60px 0;
    }

    .tips-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
    }

    .tip-card {
        background: #fff;
        padding: 30px;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        text-align: center;
    }

    .tip-number {
        width: 50px;
        height: 50px;
        background: var(--primary);
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 auto 20px;
    }

    .tip-card h4 {
        margin-bottom: 10px;
    }

    .no-guides {
        text-align: center;
        padding: 40px;
        color: #666;
    }

    @media (max-width: 768px) {
        .guides-hero h1 {
            font-size: 2rem;
        }

        .guides-grid {
            grid-template-columns: 1fr;
        }

        .guide-section-header h2 {
            font-size: 1.4rem;
        }
    }
</style>

<!-- Guides Hero Section -->
<section class="guides-hero">
    <div class="container">
        <h1>Home Interior Design Guides</h1>
        <p>Expert tips, ideas, and inspiration to help you design your dream home. From modular kitchens to wooden
            works, we've got you covered.</p>

        <div class="guide-categories">
            <?php foreach ($categories as $cat): ?>
                <?php if (isset($guidesByCategory[$cat['id']]) && count($guidesByCategory[$cat['id']]) > 0): ?>
                    <a href="#<?php echo $cat['id']; ?>" class="guide-category-btn"><?php echo $cat['name']; ?></a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php
// Loop through categories and display guides
$sectionIndex = 0;
foreach ($categories as $cat):
    if (!isset($guidesByCategory[$cat['id']]) || count($guidesByCategory[$cat['id']]) === 0)
        continue;
    $catGuides = $guidesByCategory[$cat['id']];
    $sectionIndex++;
    ?>
    <!-- <?php echo $cat['name']; ?> Guides -->
    <section class="guide-section <?php echo $sectionIndex % 2 === 0 ? 'section-light' : ''; ?>"
        id="<?php echo $cat['id']; ?>">
        <div class="container">
            <div class="guide-section-header">
                <div class="guide-section-icon">
                    <i class="fas fa-<?php echo $cat['icon']; ?>"></i>
                </div>
                <h2><?php echo $cat['name']; ?> Design Guides</h2>
            </div>

            <div class="guides-grid">
                <?php foreach ($catGuides as $guide): ?>
                    <a href="guides/<?php echo $guide['slug']; ?>.php" class="guide-card">
                        <div class="guide-card-image">
                            <?php if (!empty($guide['thumbnail'])): ?>
                                <!-- Show thumbnail if uploaded -->
                                <img src="<?php echo SITE_URL . '/' . $guide['thumbnail']; ?>"
                                    alt="<?php echo htmlspecialchars($guide['title']); ?>" class="guide-thumb">
                            <?php elseif (!empty($guide['sections']) && count($guide['sections']) > 0): ?>
                                <?php
                                // Show color preview if guide has sections with colors
                                $colors = [];
                                foreach ($guide['sections'] as $sec) {
                                    if (!empty($sec['colors'])) {
                                        foreach ($sec['colors'] as $c) {
                                            $colors[] = $c;
                                            if (count($colors) >= 4)
                                                break 2;
                                        }
                                    }
                                }
                                ?>
                                <?php if (count($colors) > 0): ?>
                                    <div class="color-preview">
                                        <?php foreach ($colors as $color): ?>
                                            <span class="color-dot" style="background: <?php echo $color; ?>;"></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <i class="fas fa-<?php echo $cat['icon']; ?>"></i>
                                <?php endif; ?>
                            <?php else: ?>
                                <i class="fas fa-<?php echo $cat['icon']; ?>"></i>
                            <?php endif; ?>
                        </div>
                        <div class="guide-card-content">
                            <span class="guide-card-tag"><?php echo $cat['name']; ?></span>
                            <h3><?php echo $guide['title']; ?></h3>
                            <p><?php echo $guide['description']; ?></p>
                            <div class="guide-card-meta">
                                <span class="guide-card-link">Read Guide <i class="fas fa-arrow-right"></i></span>
                                <span class="guide-read-time"><i class="fas fa-clock"></i>
                                    <?php echo $guide['read_time']; ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endforeach; ?>

<?php if (empty($guides)): ?>
    <section class="section">
        <div class="container">
            <div class="no-guides">
                <i class="fas fa-book-open"
                    style="font-size: 4rem; color: var(--primary); opacity: 0.3; margin-bottom: 20px;"></i>
                <h3>No Guides Available</h3>
                <p>Check back soon for expert interior design tips and guides!</p>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Quick Tips Section -->
<section class="quick-tips">
    <div class="container">
        <div class="section-header">
            <h2>Quick Interior Design Tips</h2>
            <p>Expert advice to help you make better design decisions</p>
        </div>

        <div class="tips-grid">
            <div class="tip-card">
                <div class="tip-number">1</div>
                <h4>Plan Before You Start</h4>
                <p>Always create a floor plan and visualize furniture placement before purchasing.</p>
            </div>

            <div class="tip-card">
                <div class="tip-number">2</div>
                <h4>Invest in Quality</h4>
                <p>Choose BWR/BWP grade plywood and branded hardware for long-lasting furniture.</p>
            </div>

            <div class="tip-card">
                <div class="tip-number">3</div>
                <h4>Natural Light First</h4>
                <p>Maximize natural light before planning artificial lighting for energy savings.</p>
            </div>

            <div class="tip-card">
                <div class="tip-number">4</div>
                <h4>Storage is Key</h4>
                <p>Plan for sufficient storage in every room to keep spaces clutter-free.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2>Need Personalized Design Advice?</h2>
            <p>Our expert interior designers are ready to help you create your dream home. Get a free consultation
                today!</p>
            <div class="cta-buttons">
                <a href="get-estimate.php" class="btn btn-primary btn-lg">Get Free Consultation</a>
                <a href="contact.php" class="btn btn-white btn-lg">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>