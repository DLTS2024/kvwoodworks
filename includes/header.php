<?php
/**
 * Header Component for KV Wood Works
 */
require_once __DIR__ . '/../config/database.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Primary SEO Meta Tags -->
    <title>
        <?php echo isset($pageTitle) ? $pageTitle . ' | ' . SITE_NAME . ' Chennai' : SITE_NAME . ' - Best Home Interiors & Wooden Works in Chennai'; ?>
    </title>
    <meta name="title"
        content="<?php echo isset($pageTitle) ? $pageTitle . ' | ' . SITE_NAME . ' Chennai' : SITE_NAME . ' - Best Home Interiors & Wooden Works in Chennai'; ?>">
    <meta name="description"
        content="<?php echo isset($pageDescription) ? $pageDescription : 'KV Wood Works - Chennai\'s trusted home interior designers. Premium modular kitchen, wardrobe, living room designs & custom wooden works. 500+ homes delivered, 5-year warranty. Book FREE consultation!'; ?>">
    <meta name="keywords"
        content="<?php echo isset($pageKeywords) ? $pageKeywords : 'home interiors chennai, modular kitchen chennai, wardrobe designs, living room interiors, wooden works, interior designers maduravoyal, kitchen design chennai, bedroom interiors, pooja room design, vasakal, wooden door, window frames, custom furniture chennai'; ?>">

    <!-- Author & Geo Meta Tags -->
    <meta name="author" content="<?php echo SITE_NAME; ?>">
    <meta name="robots" content="index, follow">
    <meta name="language" content="English">
    <meta name="geo.region" content="IN-TN">
    <meta name="geo.placename" content="Chennai, Maduravoyal">
    <meta name="geo.position" content="13.0827;80.2707">
    <meta name="ICBM" content="13.0827, 80.2707">

    <!-- Canonical URL -->
    <link rel="canonical"
        href="<?php echo isset($canonicalUrl) ? $canonicalUrl : SITE_URL . $_SERVER['REQUEST_URI']; ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo SITE_URL . $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:title"
        content="<?php echo isset($pageTitle) ? $pageTitle . ' | ' . SITE_NAME : SITE_NAME . ' - Best Home Interiors Chennai'; ?>">
    <meta property="og:description"
        content="<?php echo isset($pageDescription) ? $pageDescription : 'Chennai\'s trusted home interior designers. Premium modular kitchen, wardrobe, living room designs & custom wooden works. 500+ homes, 5-year warranty.'; ?>">
    <meta property="og:image"
        content="<?php echo isset($ogImage) ? $ogImage : SITE_URL . '/assets/images/twittercard.png'; ?>">
    <meta property="og:site_name" content="<?php echo SITE_NAME; ?>">
    <meta property="og:locale" content="en_IN">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo SITE_URL . $_SERVER['REQUEST_URI']; ?>">
    <meta property="twitter:title"
        content="<?php echo isset($pageTitle) ? $pageTitle . ' | ' . SITE_NAME : SITE_NAME; ?>">
    <meta property="twitter:description"
        content="<?php echo isset($pageDescription) ? $pageDescription : 'Chennai\'s trusted home interior designers. Premium modular kitchen & wooden works.'; ?>">
    <meta property="twitter:image"
        content="<?php echo isset($ogImage) ? $ogImage : SITE_URL . '/assets/images/twittercard.png'; ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo baseUrl('assets/images/favicon.png'); ?>">
    <link rel="apple-touch-icon" href="<?php echo baseUrl('assets/images/apple-touch-icon.png'); ?>">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo baseUrl('assets/css/style.css'); ?>">

    <!-- Structured Data / JSON-LD -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "name": "<?php echo SITE_NAME; ?>",
        "image": "<?php echo SITE_URL; ?>/assets/images/logo.png",
        "description": "Premium home interiors and wooden works in Chennai. Modular kitchen, wardrobe, bedroom, living room designs and custom wooden works.",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "<?php echo SITE_ADDRESS; ?>",
            "addressLocality": "Chennai",
            "addressRegion": "Tamil Nadu",
            "postalCode": "600095",
            "addressCountry": "IN"
        },
        "telephone": "<?php echo SITE_PHONE; ?>",
        "email": "<?php echo SITE_EMAIL; ?>",
        "url": "<?php echo SITE_URL; ?>",
        "priceRange": "₹₹₹",
        "openingHours": "Mo-Sa 09:00-19:00",
        "sameAs": [
            "<?php echo SITE_INSTAGRAM; ?>"
        ],
        "areaServed": {
            "@type": "City",
            "name": "Chennai"
        },
        "serviceType": ["Home Interior Design", "Modular Kitchen", "Wardrobe Design", "Wooden Works", "Custom Furniture"]
    }
    </script>
</head>

<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="top-bar-content">
                <div class="top-bar-left">
                    <a href="tel:<?php echo SITE_PHONE; ?>"><i class="fas fa-phone"></i>
                        <?php echo SITE_PHONE; ?>
                    </a>
                    <a href="mailto:<?php echo SITE_EMAIL; ?>"><i class="fas fa-envelope"></i>
                        <?php echo SITE_EMAIL; ?>
                    </a>
                </div>
                <div class="top-bar-right">
                    <a href="<?php echo SITE_INSTAGRAM; ?>" target="_blank" aria-label="Instagram"><i
                            class="fab fa-instagram"></i></a>
                    <a href="https://wa.me/<?php echo SITE_WHATSAPP; ?>" target="_blank" aria-label="WhatsApp"><i
                            class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="main-header" id="mainHeader">
        <div class="container">
            <nav class="navbar">
                <!-- Logo -->
                <a href="<?php echo SITE_URL; ?>" class="logo">
                    <img src="<?php echo baseUrl('assets/images/logos/kv-woodworks-logo.png'); ?>"
                        alt="KV Wood Works Logo" class="logo-img">
                </a>

                <!-- Desktop Navigation -->
                <ul class="nav-menu" id="navMenu">
                    <li><a href="<?php echo baseUrl(); ?>">Home</a></li>

                    <li class="has-dropdown desktop-only-menu">
                        <a href="<?php echo baseUrl('home-interior-designs.php'); ?>">Home Interior Designs <i
                                class="fas fa-chevron-down"></i></a>
                        <div class="mega-dropdown">
                            <div class="mega-dropdown-inner">
                                <div class="mega-col">
                                    <h4>Kitchen</h4>
                                    <ul>
                                        <li><a
                                                href="<?php echo baseUrl('pages/interior-design/modular-kitchen.php'); ?>">Modular
                                                Kitchen</a></li>
                                        <li><a
                                                href="<?php echo baseUrl('pages/interior-design/customize-kitchen.php'); ?>">Customize
                                                Your Kitchen</a></li>
                                    </ul>
                                </div>
                                <div class="mega-col">
                                    <h4>Rooms</h4>
                                    <ul>
                                        <li><a
                                                href="<?php echo baseUrl('pages/interior-design/bedroom.php'); ?>">Bedroom</a>
                                        </li>
                                        <li><a href="<?php echo baseUrl('pages/interior-design/living-room.php'); ?>">Living
                                                Room</a></li>
                                        <li><a href="<?php echo baseUrl('pages/interior-design/kid-bedroom.php'); ?>">Kid
                                                Bedroom</a></li>
                                        <li><a href="<?php echo baseUrl('pages/interior-design/dining-room.php'); ?>">Dining
                                                Room</a></li>
                                        <li><a href="<?php echo baseUrl('pages/interior-design/pooja-room.php'); ?>">Pooja
                                                Room</a></li>
                                    </ul>
                                </div>
                                <div class="mega-col">
                                    <h4>Living Spaces</h4>
                                    <ul>
                                        <li><a href="<?php echo baseUrl('pages/interior-design/wardrobe.php'); ?>">Wardrobe
                                                Designs</a></li>
                                        <li><a href="<?php echo baseUrl('pages/interior-design/home-office.php'); ?>">Home
                                                Office</a></li>
                                        <li><a
                                                href="<?php echo baseUrl('pages/interior-design/bathroom.php'); ?>">Bathroom</a>
                                        </li>
                                        <li><a
                                                href="<?php echo baseUrl('pages/interior-design/balcony.php'); ?>">Balcony</a>
                                        </li>
                                        <li><a href="<?php echo baseUrl('pages/interior-design/space-saving.php'); ?>">Space
                                                Saving</a></li>
                                    </ul>
                                </div>
                                <div class="mega-col">
                                    <h4>By BHK</h4>
                                    <ul>
                                        <li><a href="<?php echo baseUrl('pages/bhk/1bhk.php'); ?>">1 BHK Interior</a>
                                        </li>
                                        <li><a href="<?php echo baseUrl('pages/bhk/2bhk.php'); ?>">2 BHK Interior</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </li>

                    <li class="has-dropdown desktop-only-menu">
                        <a href="<?php echo baseUrl('wooden-works.php'); ?>">Wooden Works <i
                                class="fas fa-chevron-down"></i></a>
                        <div class="mega-dropdown">
                            <div class="mega-dropdown-inner">
                                <div class="mega-col">
                                    <h4>Vasakal Frame</h4>
                                    <ul>
                                        <li><a href="<?php echo baseUrl('pages/wooden-works/main-vasakal.php'); ?>">Main
                                                Vasakal</a></li>
                                        <li><a
                                                href="<?php echo baseUrl('pages/wooden-works/pooja-room-vasakal.php'); ?>">Pooja
                                                Room Vasakal</a></li>
                                        <li><a href="<?php echo baseUrl('pages/wooden-works/bedroom-vasakal.php'); ?>">Bedroom
                                                Vasakal</a></li>
                                        <li><a
                                                href="<?php echo baseUrl('pages/wooden-works/wpvc-bathroom-vasakal.php'); ?>">WPVC
                                                Bathroom Vasakal</a></li>
                                        <li><a
                                                href="<?php echo baseUrl('pages/wooden-works/wpvc-balcony-vasakal.php'); ?>">WPVC
                                                Balcony Vasakal</a></li>
                                        <li><a
                                                href="<?php echo baseUrl('pages/wooden-works/french-window-vasakal.php'); ?>">French
                                                Window Vasakal</a></li>
                                    </ul>
                                </div>
                                <div class="mega-col">
                                    <h4>Doors</h4>
                                    <ul>
                                        <li><a href="<?php echo baseUrl('pages/wooden-works/main-door.php'); ?>">Main
                                                Door</a></li>
                                        <li><a href="<?php echo baseUrl('pages/wooden-works/main-door-safety.php'); ?>">Main
                                                Door + Safety Door</a></li>
                                        <li><a href="<?php echo baseUrl('pages/wooden-works/pooja-room-door.php'); ?>">Pooja
                                                Room Door</a></li>
                                        <li><a href="<?php echo baseUrl('pages/wooden-works/bedroom-door.php'); ?>">Bedroom
                                                Door</a></li>
                                        <li><a
                                                href="<?php echo baseUrl('pages/wooden-works/wpvc-bathroom-door.php'); ?>">WPVC
                                                Bathroom Door</a></li>
                                        <li><a
                                                href="<?php echo baseUrl('pages/wooden-works/pvc-bathroom-door.php'); ?>">PVC
                                                Bathroom Door</a></li>
                                        <li><a href="<?php echo baseUrl('pages/wooden-works/balcony-door.php'); ?>">Balcony
                                                Door</a></li>
                                        <li><a
                                                href="<?php echo baseUrl('pages/wooden-works/french-window-door.php'); ?>">French
                                                Window Door</a></li>
                                        <li><a href="<?php echo baseUrl('pages/wooden-works/double-door.php'); ?>">Double
                                                Door</a></li>
                                    </ul>
                                </div>
                                <div class="mega-col">
                                    <h4>Windows & Staircase</h4>
                                    <ul>
                                        <li><a href="<?php echo baseUrl('pages/wooden-works/window.php'); ?>">Window</a>
                                        </li>
                                        <li><a
                                                href="<?php echo baseUrl('pages/wooden-works/double-door-window.php'); ?>">Double
                                                Door Window</a></li>
                                        <li><a href="<?php echo baseUrl('pages/wooden-works/ottu-sakkai.php'); ?>">Vasakal
                                                Ottu Sakkai</a></li>
                                        <li style="margin-top: 15px;"><strong
                                                style="color: var(--primary);">Staircase</strong></li>
                                        <li><a href="<?php echo baseUrl('pages/wooden-works/round-staircase.php'); ?>">Wooden
                                                Round Staircase</a></li>
                                        <li><a
                                                href="<?php echo baseUrl('pages/wooden-works/straight-staircase.php'); ?>">Wooden
                                                Straight Staircase</a></li>
                                    </ul>
                                </div>
                                <div class="mega-col">
                                    <h4>Furniture</h4>
                                    <ul>
                                        <li><a href="<?php echo baseUrl('pages/wooden-works/sofa-set.php'); ?>">Sofa
                                                Set</a></li>
                                        <li><a href="<?php echo baseUrl('pages/wooden-works/diwan.php'); ?>">Diwan</a>
                                        </li>
                                        <li><a href="<?php echo baseUrl('pages/wooden-works/kattil-bed.php'); ?>">Kattil
                                                (Bed)</a></li>
                                        <li><a href="<?php echo baseUrl('pages/wooden-works/dining-table.php'); ?>">Dining
                                                Table with Chairs</a></li>
                                        <li><a href="<?php echo baseUrl('pages/wooden-works/chair.php'); ?>">Chair</a>
                                        </li>
                                        <li><a href="<?php echo baseUrl('pages/wooden-works/wardrobe.php'); ?>">Bero /
                                                Wardrobe</a></li>
                                        <li><a href="<?php echo baseUrl('pages/wooden-works/pooja-mandapam.php'); ?>">Pooja
                                                Mandapam</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </li>

                    <li><a href="<?php echo baseUrl('recent-projects.php'); ?>">Recent Projects</a></li>
                    <li><a href="<?php echo baseUrl('gallery.php'); ?>">Gallery</a></li>
                    <li><a href="<?php echo baseUrl('reviews.php'); ?>">Reviews</a></li>

                    <li class="has-dropdown">
                        <a href="#">More <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown">
                            <li><a href="<?php echo baseUrl('about.php'); ?>">About Us</a></li>
                            <li><a href="<?php echo baseUrl('about.php#why-us'); ?>">Why KV Wood Works</a></li>
                            <li><a href="<?php echo baseUrl('guides.php'); ?>">Design Guides</a></li>
                            <li><a href="<?php echo baseUrl('blog.php'); ?>">Blog</a></li>
                            <li><a href="<?php echo baseUrl('faqs.php'); ?>">FAQs</a></li>
                            <li><a href="<?php echo baseUrl('contact.php'); ?>">Contact Us</a></li>
                        </ul>
                    </li>
                </ul>

                <!-- CTA Button -->
                <a href="<?php echo baseUrl('get-estimate.php'); ?>" class="btn btn-primary nav-cta">
                    Get Free Estimate
                </a>

                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </nav>
        </div>
    </header>

    <!-- Mobile Navigation Overlay -->
    <div class="mobile-nav-overlay" id="mobileNavOverlay"></div>

    <?php
    // Include promotional banners component
    include_once __DIR__ . '/banners.php';
    // Show header banner
    show_banner('header');
    ?>

    <!-- Main Content Wrapper -->
    <main class="main-content">