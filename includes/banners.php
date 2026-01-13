<?php
/**
 * KV Wood Works - Promotional Banner Component
 * Include this file to display promotional banners on any page
 * Usage: include 'includes/banners.php'; then call show_banner('header'), show_banner('middle'), or show_banner('footer')
 */

// Load banner settings
$bannersFile = __DIR__ . '/../config/banners.json';
$GLOBALS['bannerSettings'] = [];
if (file_exists($bannersFile)) {
    $GLOBALS['bannerSettings'] = json_decode(file_get_contents($bannersFile), true) ?? [];
}

/**
 * Display a promotional banner
 * @param string $type - 'header', 'middle', or 'footer'
 */
function show_banner($type = 'middle')
{
    $banners = $GLOBALS['bannerSettings'];
    $key = $type . '_banner';

    if (!isset($banners[$key]) || !$banners[$key]['enabled']) {
        return;
    }

    $banner = $banners[$key];
    $link = baseUrl($banner['link'] ?? 'get-estimate.php');

    // Check if image exists
    $hasImage = !empty($banner['image']) && file_exists(__DIR__ . '/../' . $banner['image']);

    if ($type === 'header') {
        echo '<div class="promo-banner promo-banner-header">';
        echo '<a href="' . $link . '">';
        if ($hasImage) {
            echo '<img src="' . SITE_URL . '/' . $banner['image'] . '" alt="' . ($banner['alt_text'] ?? 'Special Offer') . '">';
        } else {
            echo '<div class="promo-text-only"><i class="fas fa-gift"></i> Special Offers Available! <span>Click here to learn more</span> <i class="fas fa-arrow-right"></i></div>';
        }
        echo '</a>';
        echo '</div>';
    }

    if ($type === 'middle') {
        echo '<section class="promo-banner-section">';
        echo '<div class="container">';
        echo '<a href="' . $link . '" class="promo-banner-middle">';

        if ($hasImage) {
            echo '<img src="' . SITE_URL . '/' . $banner['image'] . '" alt="' . ($banner['alt_text'] ?? 'Limited Time Offer') . '">';
        } else {
            // Text-based banner like DesignCafe
            echo '<div class="promo-content">';
            echo '<div class="promo-left">';
            echo '<div class="promo-badge"><i class="fas fa-star"></i></div>';
            if (!empty($banner['title'])) {
                echo '<h3>' . $banner['title'] . '</h3>';
            }
            echo '</div>';
            echo '<div class="promo-center">';
            if (!empty($banner['subtitle'])) {
                echo '<div class="promo-offer">' . $banner['subtitle'] . '</div>';
            }
            if (!empty($banner['description'])) {
                echo '<p>' . $banner['description'] . '</p>';
            }
            echo '</div>';
            echo '<div class="promo-right">';
            echo '<span class="promo-btn">' . ($banner['button_text'] ?? 'Book Free Consultation') . '</span>';
            if (!empty($banner['valid_until'])) {
                echo '<small>Hurry, Book Before ' . $banner['valid_until'] . '</small>';
            }
            echo '</div>';
            echo '</div>';
        }

        echo '</a>';
        echo '</div>';
        echo '</section>';
    }

    if ($type === 'footer') {
        echo '<div class="promo-banner promo-banner-footer">';
        echo '<div class="container">';
        echo '<a href="' . $link . '">';
        if ($hasImage) {
            echo '<img src="' . SITE_URL . '/' . $banner['image'] . '" alt="' . ($banner['alt_text'] ?? 'Exclusive Offer') . '">';
        } else {
            echo '<div class="promo-text-only"><i class="fas fa-percentage"></i> Don\'t Miss Our Exclusive Offers! <span>Get a Free Quote Today</span> <i class="fas fa-arrow-right"></i></div>';
        }
        echo '</a>';
        echo '</div>';
        echo '</div>';
    }
}

/**
 * Check if a banner type is enabled
 * @param string $type - 'header', 'middle', 'footer', or 'guide'
 * @return bool
 */
function hasBanner($type = 'middle')
{
    $banners = $GLOBALS['bannerSettings'];
    $key = $type . '_banner';
    return isset($banners[$key]) && $banners[$key]['enabled'];
}

/**
 * Display guide page banner (used inside guide articles)
 */
function show_guide_banner()
{
    $banners = $GLOBALS['bannerSettings'];

    if (!isset($banners['guide_banner']) || !$banners['guide_banner']['enabled']) {
        return false;
    }

    $banner = $banners['guide_banner'];
    $link = baseUrl($banner['link'] ?? 'get-estimate.php');
    $size = $banner['size'] ?? 'standard';
    $bgColor = $banner['background_color'] ?? '#1a1a1a';

    // Size heights
    $heights = [
        'compact' => '80px',
        'standard' => '120px',
        'large' => '180px'
    ];
    $height = $heights[$size] ?? '120px';

    // Check if image exists
    $hasImage = !empty($banner['image']) && file_exists(__DIR__ . '/../' . $banner['image']);

    echo '<div class="guide-promo-banner guide-promo-' . $size . '" style="--banner-height: ' . $height . '; --banner-bg: ' . $bgColor . ';">';
    echo '<a href="' . $link . '">';

    if ($hasImage) {
        echo '<img src="' . SITE_URL . '/' . $banner['image'] . '" alt="' . ($banner['alt_text'] ?? 'Design Consultation') . '">';
    } else {
        // Text-based banner
        echo '<div class="guide-promo-content">';
        echo '<div class="guide-promo-left">';
        if (!empty($banner['title'])) {
            echo '<h4>' . $banner['title'] . '</h4>';
        }
        if (!empty($banner['description'])) {
            echo '<p>' . $banner['description'] . '</p>';
        }
        echo '</div>';
        echo '<div class="guide-promo-right">';
        if (!empty($banner['subtitle'])) {
            echo '<span class="guide-promo-highlight">' . $banner['subtitle'] . '</span>';
        }
        echo '<span class="guide-promo-btn">' . ($banner['button_text'] ?? 'Get Free Quote') . '</span>';
        echo '</div>';
        echo '</div>';
    }

    echo '</a>';
    echo '</div>';

    return true;
}
?>

<style>
    /* Promotional Banner Styles */
    .promo-banner {
        width: 100%;
        overflow: hidden;
    }

    .promo-banner a {
        display: block;
    }

    .promo-banner img {
        width: 100%;
        height: auto;
        display: block;
    }

    .promo-banner-header {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    }

    .promo-banner-footer {
        background: linear-gradient(135deg, #c8956c 0%, #a67b5b 100%);
        padding: 0;
    }

    .promo-text-only {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
        padding: 12px 20px;
        color: #fff;
        font-weight: 500;
        font-size: 1rem;
    }

    .promo-text-only i:first-child {
        font-size: 1.2rem;
    }

    .promo-text-only span {
        color: #ffd700;
    }

    /* Middle Banner - DesignCafe Style */
    .promo-banner-section {
        padding: 30px 0;
        background: #f8f4f0;
    }

    .promo-banner-middle {
        display: block;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .promo-banner-middle:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
    }

    .promo-banner-middle img {
        width: 100%;
        height: auto;
        display: block;
    }

    .promo-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 25px 40px;
        background: linear-gradient(135deg, #8B0000 0%, #B22222 50%, #CD5C5C 100%);
        color: #fff;
        gap: 30px;
    }

    .promo-left {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .promo-badge {
        width: 60px;
        height: 60px;
        background: #ffd700;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #8B0000;
    }

    .promo-left h3 {
        font-size: 1.3rem;
        margin: 0;
    }

    .promo-center {
        text-align: center;
        flex: 1;
    }

    .promo-offer {
        font-size: 2rem;
        font-weight: 700;
        color: #ffd700;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        border: 3px solid #ffd700;
        padding: 8px 25px;
        border-radius: 8px;
        display: inline-block;
        margin-bottom: 8px;
    }

    .promo-center p {
        margin: 0;
        font-size: 1.1rem;
        color: #fff;
    }

    .promo-right {
        text-align: center;
    }

    .promo-btn {
        display: inline-block;
        background: #c8956c;
        color: #fff;
        padding: 12px 25px;
        border-radius: 8px;
        font-weight: 600;
        margin-bottom: 8px;
        transition: background 0.3s;
    }

    .promo-btn:hover {
        background: #a67b5b;
    }

    .promo-right small {
        display: block;
        font-size: 0.85rem;
        opacity: 0.9;
    }

    @media (max-width: 900px) {
        .promo-content {
            flex-direction: column;
            text-align: center;
            padding: 20px;
        }

        .promo-left,
        .promo-right {
            text-align: center;
        }

        .promo-offer {
            font-size: 1.5rem;
        }
    }

    /* Guide Page Banner Styles */
    .guide-promo-banner {
        margin: 40px 0;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .guide-promo-banner:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
    }

    .guide-promo-banner a {
        display: block;
        text-decoration: none;
    }

    .guide-promo-banner img {
        width: 100%;
        height: var(--banner-height, 120px);
        object-fit: cover;
        display: block;
    }

    .guide-promo-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 30px;
        background: var(--banner-bg, #1a1a1a);
        color: #fff;
        min-height: var(--banner-height, 120px);
        gap: 20px;
    }

    .guide-promo-left h4 {
        font-size: 1.3rem;
        margin: 0 0 5px;
    }

    .guide-promo-left p {
        margin: 0;
        opacity: 0.85;
        font-size: 0.95rem;
    }

    .guide-promo-right {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .guide-promo-highlight {
        background: #ffd700;
        color: #1a1a1a;
        padding: 8px 15px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .guide-promo-btn {
        background: var(--primary, #c8956c);
        color: #fff;
        padding: 12px 25px;
        border-radius: 8px;
        font-weight: 600;
        transition: background 0.3s;
    }

    .guide-promo-btn:hover {
        background: #a67b5b;
    }

    /* Size variations */
    .guide-promo-compact .guide-promo-content {
        padding: 15px 25px;
    }

    .guide-promo-compact .guide-promo-left h4 {
        font-size: 1.1rem;
    }

    .guide-promo-large .guide-promo-content {
        padding: 30px 40px;
    }

    .guide-promo-large .guide-promo-left h4 {
        font-size: 1.5rem;
    }

    @media (max-width: 768px) {
        .guide-promo-content {
            flex-direction: column;
            text-align: center;
            padding: 20px;
        }

        .guide-promo-right {
            flex-direction: column;
        }

        .guide-promo-banner {
            margin: 25px 0;
        }
    }
</style>