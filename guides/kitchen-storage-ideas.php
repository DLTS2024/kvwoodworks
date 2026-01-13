<?php
/**
 * KV Wood Works - Dynamic Guide Article Template
 * Reads content from guides.json and displays with banners every 3 sections
 */

// Get guide slug from URL
$guideSlug = basename($_SERVER['PHP_SELF'], '.php');

// Load guides data
$guidesFile = __DIR__ . '/../config/guides.json';
$guidesData = [];
$guide = null;
$category = null;

if (file_exists($guidesFile)) {
    $guidesData = json_decode(file_get_contents($guidesFile), true) ?? [];
    $categories = $guidesData['categories'] ?? [];
    $guides = $guidesData['guides'] ?? [];

    // Find the current guide
    foreach ($guides as $g) {
        if ($g['slug'] === $guideSlug) {
            $guide = $g;
            break;
        }
    }

    // Find category info
    if ($guide) {
        foreach ($categories as $cat) {
            if ($cat['id'] === $guide['category']) {
                $category = $cat;
                break;
            }
        }
    }
}

// Redirect if guide not found
if (!$guide) {
    header('Location: ' . SITE_URL . '/guides.php');
    exit;
}

$pageTitle = $guide['title'];
$pageDescription = $guide['description'];
$pageKeywords = ($category['name'] ?? '') . ', interior design guide, ' . strtolower($guide['title']);

include '../includes/header.php';
?>

<style>
    /* Guide Article Styles */
    .guide-article-header {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        color: #fff;
        padding: 80px 0 60px;
    }

    .guide-breadcrumb {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        font-size: 0.9rem;
    }

    .guide-breadcrumb a {
        color: var(--primary);
        text-decoration: none;
    }

    .guide-breadcrumb span {
        color: rgba(255, 255, 255, 0.5);
    }

    .guide-article-header h1 {
        font-size: 2.8rem;
        margin-bottom: 20px;
        max-width: 800px;
    }

    .guide-meta {
        display: flex;
        gap: 25px;
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.95rem;
    }

    .guide-meta i {
        color: var(--primary);
        margin-right: 8px;
    }

    /* Article Content */
    .guide-content {
        padding: 60px 0;
    }

    .guide-layout {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 50px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .guide-main {
        max-width: 800px;
    }

    .guide-intro {
        font-size: 1.15rem;
        line-height: 1.8;
        color: #444;
        margin-bottom: 40px;
        padding-bottom: 30px;
        border-bottom: 1px solid #eee;
    }

    .guide-section {
        margin-bottom: 50px;
    }

    .guide-section h2 {
        font-size: 1.6rem;
        color: #1a1a1a;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .colour-swatch {
        display: inline-flex;
        gap: 5px;
    }

    .colour-swatch span {
        width: 25px;
        height: 25px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .guide-section p {
        font-size: 1.05rem;
        line-height: 1.8;
        color: #555;
        margin-bottom: 20px;
    }

    .guide-image {
        width: 100%;
        height: 350px;
        background: linear-gradient(135deg, #f5f5f5, #eee);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 25px 0;
    }

    .guide-image i {
        font-size: 4rem;
        color: var(--primary);
        opacity: 0.4;
    }

    .guide-image-actual {
        width: 100%;
        height: auto;
        max-height: 450px;
        object-fit: cover;
        border-radius: 16px;
        margin: 25px 0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .guide-tip {
        background: linear-gradient(135deg, rgba(200, 149, 108, 0.1), rgba(200, 149, 108, 0.05));
        border-left: 4px solid var(--primary);
        padding: 20px 25px;
        border-radius: 0 12px 12px 0;
        margin: 25px 0;
    }

    .guide-tip h4 {
        color: var(--primary);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Middle Banner in Guide */
    .guide-middle-banner {
        margin: 40px 0;
        padding: 40px;
        background: linear-gradient(135deg, #1a1a1a, #2d2d2d);
        border-radius: 16px;
        text-align: center;
        color: #fff;
    }

    .guide-middle-banner h3 {
        font-size: 1.5rem;
        margin-bottom: 15px;
    }

    .guide-middle-banner p {
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 20px;
        font-size: 1rem;
    }

    .guide-middle-banner .btn {
        background: var(--primary);
        color: #fff;
        padding: 12px 30px;
        border-radius: 30px;
        text-decoration: none;
        display: inline-block;
        font-weight: 600;
        transition: all 0.3s;
    }

    .guide-middle-banner .btn:hover {
        background: #a67b5b;
        transform: translateY(-2px);
    }

    /* Sidebar */
    .guide-sidebar {
        position: sticky;
        top: 100px;
        height: fit-content;
    }

    .sidebar-card {
        background: #fff;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        margin-bottom: 25px;
    }

    .sidebar-card h4 {
        font-size: 1.1rem;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }

    .toc-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .toc-list li {
        margin-bottom: 12px;
    }

    .toc-list a {
        color: #555;
        text-decoration: none;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: color 0.3s;
    }

    .toc-list a:hover {
        color: var(--primary);
    }

    .toc-list a::before {
        content: '';
        width: 6px;
        height: 6px;
        background: var(--primary);
        border-radius: 50%;
    }

    .sidebar-cta {
        background: linear-gradient(135deg, var(--primary), #a67b5b);
        color: #fff;
        text-align: center;
    }

    .sidebar-cta h4 {
        color: #fff;
        border-bottom-color: rgba(255, 255, 255, 0.2);
    }

    .sidebar-cta p {
        font-size: 0.95rem;
        margin-bottom: 20px;
        opacity: 0.9;
    }

    .sidebar-cta .btn {
        width: 100%;
        background: #fff;
        color: var(--primary);
    }

    @media (max-width: 992px) {
        .guide-layout {
            grid-template-columns: 1fr;
        }

        .guide-sidebar {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .guide-article-header h1 {
            font-size: 2rem;
        }

        .guide-meta {
            flex-direction: column;
            gap: 10px;
        }

        .guide-middle-banner {
            padding: 25px;
        }
    }
</style>

<!-- Guide Article Header -->
<section class="guide-article-header">
    <div class="container">
        <div class="guide-breadcrumb">
            <a href="<?php echo baseUrl(); ?>">Home</a>
            <span>/</span>
            <a href="<?php echo baseUrl('guides.php'); ?>">Guides</a>
            <span>/</span>
            <span><?php echo $category['name'] ?? 'Guide'; ?></span>
        </div>

        <h1><?php echo htmlspecialchars($guide['title']); ?></h1>

        <div class="guide-meta">
            <span><i class="fas fa-clock"></i> <?php echo $guide['read_time'] ?? '5 min'; ?> read</span>
            <span><i class="fas fa-calendar"></i> Updated:
                <?php echo date('F Y', strtotime($guide['updated'])); ?></span>
            <span><i class="fas fa-tag"></i> <?php echo $category['name'] ?? 'Interior Design'; ?></span>
        </div>
    </div>
</section>

<!-- Guide Content -->
<section class="guide-content">
    <div class="container">
        <div class="guide-layout">
            <!-- Main Content -->
            <article class="guide-main">
                <div class="guide-intro">
                    <p><?php echo nl2br(htmlspecialchars($guide['description'])); ?></p>
                </div>

                <?php
                $sections = $guide['sections'] ?? [];
                $sectionCount = count($sections);

                foreach ($sections as $index => $section):
                    $sectionId = strtolower(str_replace(' ', '-', preg_replace('/[^a-zA-Z0-9 ]/', '', $section['title'])));
                    ?>
                    <!-- Section: <?php echo $section['title']; ?> -->
                    <div class="guide-section" id="<?php echo $sectionId; ?>">
                        <h2>
                            <?php if (!empty($section['colors'])): ?>
                                <div class="colour-swatch">
                                    <?php foreach ($section['colors'] as $color): ?>
                                        <span
                                            style="background: <?php echo $color; ?>;<?php echo strtoupper($color) === '#FFFFFF' ? ' border: 1px solid #ddd;' : ''; ?>"></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <?php echo htmlspecialchars($section['title']); ?>
                        </h2>

                        <?php if (!empty($section['image'])): ?>
                            <img src="<?php echo SITE_URL . '/' . $section['image']; ?>"
                                alt="<?php echo htmlspecialchars($section['title']); ?>" class="guide-image-actual">
                        <?php else: ?>
                            <div class="guide-image">
                                <i class="fas fa-<?php echo $category['icon'] ?? 'palette'; ?>"></i>
                            </div>
                        <?php endif; ?>

                        <?php
                        // Split content by newlines and create paragraphs
                        $paragraphs = explode("\n", $section['content']);
                        foreach ($paragraphs as $para):
                            $para = trim($para);
                            if (!empty($para)):
                                ?>
                                <p><?php echo htmlspecialchars($para); ?></p>
                                <?php
                            endif;
                        endforeach;
                        ?>

                        <?php if (!empty($section['tip'])): ?>
                            <div class="guide-tip">
                                <h4><i class="fas fa-lightbulb"></i> Pro Tip</h4>
                                <p><?php echo htmlspecialchars($section['tip']); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php
                    // Show guide banner after every 3 sections (but not after the last section)
                    if (($index + 1) % 3 === 0 && ($index + 1) < $sectionCount):
                        ?>
                        <!-- Guide Page Banner -->
                        <?php if (!show_guide_banner()): ?>
                            <!-- Default CTA Banner if no guide banner configured -->
                            <div class="guide-middle-banner">
                                <h3>Transform Your Home with KV Wood Works</h3>
                                <p>Get expert interior design consultation and bring your dream home to life!</p>
                                <a href="<?php echo baseUrl('get-estimate.php'); ?>" class="btn">Get Free Consultation</a>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                <?php endforeach; ?>

                <?php if (empty($sections)): ?>
                    <div style="text-align: center; padding: 60px 0; color: #666;">
                        <i class="fas fa-file-alt"
                            style="font-size: 4rem; color: var(--primary); opacity: 0.3; margin-bottom: 20px;"></i>
                        <h3>Content Coming Soon</h3>
                        <p>This guide is being prepared. Check back soon!</p>
                    </div>
                <?php endif; ?>

            </article>

            <!-- Sidebar -->
            <aside class="guide-sidebar">
                <?php if (!empty($sections)): ?>
                    <div class="sidebar-card">
                        <h4>Table of Contents</h4>
                        <ul class="toc-list">
                            <?php foreach ($sections as $section):
                                $sectionId = strtolower(str_replace(' ', '-', preg_replace('/[^a-zA-Z0-9 ]/', '', $section['title'])));
                                ?>
                                <li><a href="#<?php echo $sectionId; ?>"><?php echo htmlspecialchars($section['title']); ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="sidebar-card sidebar-cta">
                    <h4>Need Help Designing?</h4>
                    <p>Get expert advice from our design team.</p>
                    <a href="<?php echo baseUrl('get-estimate.php'); ?>" class="btn">Get Free Consultation</a>
                </div>

                <div class="sidebar-card">
                    <h4>Related Guides</h4>
                    <ul class="toc-list">
                        <?php
                        // Show other guides from same category
                        $relatedCount = 0;
                        foreach ($guides as $g):
                            if ($g['id'] !== $guide['id'] && $g['enabled'] && $relatedCount < 3):
                                $relatedCount++;
                                ?>
                                <li><a href="<?php echo $g['slug']; ?>.php"><?php echo htmlspecialchars($g['title']); ?></a>
                                </li>
                                <?php
                            endif;
                        endforeach;
                        ?>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Transform Your Space?</h2>
            <p>Our expert designers can help you create the perfect interiors. Get a free consultation today!</p>
            <div class="cta-buttons">
                <a href="<?php echo baseUrl('get-estimate.php'); ?>" class="btn btn-primary btn-lg">Get Free
                    Consultation</a>
                <a href="<?php echo baseUrl('contact.php'); ?>" class="btn btn-white btn-lg">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>