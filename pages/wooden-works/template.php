<?php
/**
 * Universal Wooden Works Category/Subcategory Template
 * Loads dynamically based on URL structure
 */

require_once __DIR__ . '/../../config/database.php';

// Load wooden categories config
$categoriesFile = __DIR__ . '/../../config/wooden_categories.json';
$categoriesData = [];
if (file_exists($categoriesFile)) {
    $categoriesData = json_decode(file_get_contents($categoriesFile), true);
}
$categories = $categoriesData['categories'] ?? [];

// Parse URL to determine category and subcategory
$requestUri = $_SERVER['REQUEST_URI'];
$pathParts = explode('/', trim(parse_url($requestUri, PHP_URL_PATH), '/'));

// Get the current slug from filename
$currentSlug = basename($_SERVER['PHP_SELF'], '.php');

// Find category and subcategory info
$currentCategory = null;
$currentSubcategory = null;
$pageInfo = null;

foreach ($categories as $catSlug => $cat) {
    // Check if this is a main category page
    if ($catSlug === $currentSlug) {
        $currentCategory = $cat;
        $pageInfo = [
            'title' => $cat['name'],
            'icon' => $cat['icon'],
            'description' => $cat['description'],
            'isMainCategory' => true,
            'categorySlug' => $catSlug
        ];
        break;
    }

    // Check subcategories
    if (isset($cat['subcategories'])) {
        foreach ($cat['subcategories'] as $subSlug => $sub) {
            if ($subSlug === $currentSlug) {
                $currentCategory = $cat;
                $currentSubcategory = $sub;
                $pageInfo = [
                    'title' => $sub['name'],
                    'icon' => $sub['icon'],
                    'description' => $sub['description'],
                    'isMainCategory' => false,
                    'categorySlug' => $catSlug,
                    'categoryName' => $cat['name'],
                    'subcategorySlug' => $subSlug
                ];
                break 2;
            }
        }
    }
}

// Fallback if not found
if (!$pageInfo) {
    $pageInfo = [
        'title' => ucwords(str_replace('-', ' ', $currentSlug)),
        'icon' => 'tree',
        'description' => 'Premium wooden works',
        'isMainCategory' => true,
        'categorySlug' => $currentSlug
    ];
}

$pageTitle = $pageInfo['title'] . ' | Wooden Works';
$pageDescription = $pageInfo['description'];

include __DIR__ . '/../../includes/header.php';

// Load designs from JSON (if exists)
$designsFile = __DIR__ . '/../../config/wooden_designs.json';
$allDesigns = [];
if (file_exists($designsFile)) {
    $allDesigns = json_decode(file_get_contents($designsFile), true) ?? [];
}
$categoryDesigns = isset($allDesigns[$currentSlug]) ? $allDesigns[$currentSlug] : [];
?>

<style>
    /* Design Cards Styles */
    .design-cards-section {
        padding: 60px 0;
    }

    .design-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 30px;
    }

    .design-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .design-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
    }

    .design-carousel {
        position: relative;
        height: 280px;
        overflow: hidden;
    }

    .carousel-slides {
        display: flex;
        height: 100%;
        transition: transform 0.4s ease;
    }

    .carousel-slide {
        min-width: 100%;
        height: 100%;
        cursor: pointer;
    }

    .carousel-slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .carousel-slide:hover img {
        transform: scale(1.02);
    }

    /* Fullscreen Lightbox */
    .lightbox {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.95);
        z-index: 20000;
        display: none;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }

    .lightbox.active {
        display: flex;
    }

    .lightbox-close {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.1);
        border: none;
        border-radius: 50%;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        transition: background 0.3s;
        z-index: 20001;
    }

    .lightbox-close:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .lightbox-image-container {
        max-width: 90%;
        max-height: 80vh;
        position: relative;
    }

    .lightbox-image {
        max-width: 100%;
        max-height: 80vh;
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    }

    .lightbox-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.1);
        border: none;
        border-radius: 50%;
        color: white;
        font-size: 1.2rem;
        cursor: pointer;
        transition: background 0.3s;
    }

    .lightbox-nav:hover {
        background: var(--primary, #c8956c);
    }

    .lightbox-nav.prev {
        left: 20px;
    }

    .lightbox-nav.next {
        right: 20px;
    }

    .lightbox-counter {
        color: white;
        margin-top: 20px;
        font-size: 0.9rem;
        opacity: 0.7;
    }

    .lightbox-title {
        color: white;
        margin-top: 10px;
        font-size: 1.1rem;
        text-align: center;
        max-width: 80%;
    }

    @media (max-width: 768px) {
        .lightbox-nav {
            width: 40px;
            height: 40px;
        }

        .lightbox-nav.prev {
            left: 10px;
        }

        .lightbox-nav.next {
            right: 10px;
        }
    }

    .carousel-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 40px;
        height: 40px;
        background: var(--primary, #c8956c);
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        font-size: 1rem;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.3s;
    }

    .carousel-btn:hover {
        background: #a67b5b;
    }

    .carousel-btn.prev {
        left: 15px;
    }

    .carousel-btn.next {
        right: 15px;
    }

    .design-card-content {
        padding: 20px;
    }

    .design-card-content h3 {
        font-size: 1.1rem;
        color: #333;
        margin-bottom: 15px;
        line-height: 1.4;
    }

    .design-card-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btn-get-quote {
        background: var(--primary, #c8956c);
        color: white;
        padding: 10px 25px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: 500;
        transition: background 0.3s;
    }

    .btn-get-quote:hover {
        background: #a67b5b;
        color: white;
    }

    .explore-more-link {
        color: var(--primary, #c8956c);
        text-decoration: none;
        font-weight: 500;
        cursor: pointer;
        transition: color 0.3s;
    }

    .explore-more-link:hover {
        color: #a67b5b;
    }

    .explore-more-link i {
        margin-left: 5px;
    }

    /* Sidebar Modal */
    .explore-modal {
        position: fixed;
        top: 0;
        right: -450px;
        width: 450px;
        max-width: 100%;
        height: 100%;
        background: white;
        box-shadow: -5px 0 30px rgba(0, 0, 0, 0.2);
        z-index: 10000;
        transition: right 0.4s ease;
        overflow-y: auto;
    }

    .explore-modal.active {
        right: 0;
    }

    .explore-modal-header {
        background: var(--primary, #c8956c);
        color: white;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .explore-modal-header h4 {
        font-size: 1rem;
        font-weight: 500;
    }

    .explore-modal-close {
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
    }

    .explore-modal-content {
        padding: 25px;
    }

    .explore-modal-content h3 {
        font-size: 1.3rem;
        color: #333;
        margin-bottom: 20px;
        line-height: 1.5;
    }

    .explore-modal-content .description {
        color: #666;
        font-size: 0.95rem;
        line-height: 1.7;
        margin-bottom: 25px;
    }

    .explore-modal-content h5 {
        font-size: 1.1rem;
        color: #333;
        margin-bottom: 15px;
    }

    .features-list {
        list-style: none;
        padding: 0;
    }

    .features-list li {
        position: relative;
        padding-left: 25px;
        margin-bottom: 12px;
        color: #666;
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .features-list li::before {
        content: "✓";
        position: absolute;
        left: 0;
        color: var(--primary, #c8956c);
        font-weight: bold;
    }

    .explore-modal-footer {
        position: sticky;
        bottom: 0;
        background: white;
        padding: 20px;
        border-top: 1px solid #eee;
    }

    .explore-modal-footer .btn-get-quote {
        width: 100%;
        text-align: center;
        display: block;
        padding: 15px;
        font-size: 1.1rem;
    }

    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s;
    }

    .modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #666;
    }

    .empty-state i {
        font-size: 4rem;
        color: var(--primary, #c8956c);
        opacity: 0.4;
        margin-bottom: 20px;
    }

    /* Subcategory Links for Main Category Pages */
    .subcategories-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 25px;
        margin-bottom: 50px;
    }

    .subcategory-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s, box-shadow 0.3s;
        text-decoration: none;
        color: inherit;
    }

    .subcategory-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }

    .subcategory-card i {
        font-size: 3rem;
        color: var(--primary, #c8956c);
        margin-bottom: 15px;
    }

    .subcategory-card h3 {
        font-size: 1.1rem;
        margin-bottom: 10px;
        color: #1a1a1a;
    }

    .subcategory-card p {
        font-size: 0.9rem;
        color: #666;
        line-height: 1.5;
    }

    .subcategory-card .view-link {
        display: inline-block;
        margin-top: 15px;
        color: var(--primary, #c8956c);
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .design-cards-grid {
            grid-template-columns: 1fr;
        }

        .explore-modal {
            width: 100%;
            right: -100%;
        }

        .subcategories-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>
            <?php echo $pageInfo['title']; ?>
        </h1>
        <p>
            <?php echo $pageInfo['description']; ?>
        </p>
        <div class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>">Home</a>
            <span>/</span>
            <a href="<?php echo baseUrl('wooden-works.php'); ?>">Wooden Works</a>
            <?php if (!$pageInfo['isMainCategory'] && isset($pageInfo['categoryName'])): ?>
                <span>/</span>
                <a href="<?php echo baseUrl('pages/wooden-works/' . $pageInfo['categorySlug'] . '.php'); ?>">
                    <?php echo $pageInfo['categoryName']; ?>
                </a>
            <?php endif; ?>
            <span>/</span>
            <span>
                <?php echo $pageInfo['title']; ?>
            </span>
        </div>
    </div>
</section>

<!-- Subcategories (for main category pages) -->
<?php if ($pageInfo['isMainCategory'] && $currentCategory && !empty($currentCategory['subcategories'])): ?>
    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2>Explore
                    <?php echo $currentCategory['name']; ?> Types
                </h2>
                <p>Choose from our wide range of
                    <?php echo strtolower($currentCategory['name']); ?>
                </p>
            </div>

            <div class="subcategories-grid">
                <?php foreach ($currentCategory['subcategories'] as $subSlug => $sub): ?>
                    <a href="<?php echo baseUrl('pages/wooden-works/' . $subSlug . '.php'); ?>" class="subcategory-card">
                        <i class="fas fa-<?php echo $sub['icon']; ?>"></i>
                        <h3>
                            <?php echo $sub['name']; ?>
                        </h3>
                        <p>
                            <?php echo $sub['description']; ?>
                        </p>
                        <span class="view-link">View Designs <i class="fas fa-arrow-right"></i></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Design Cards -->
<section class="design-cards-section">
    <div class="container">
        <?php if (empty($categoryDesigns)): ?>
            <div class="empty-state">
                <i class="fas fa-<?php echo $pageInfo['icon']; ?>"></i>
                <h3>Coming Soon!</h3>
                <p>We're adding beautiful
                    <?php echo strtolower($pageInfo['title']); ?> designs. Check back soon!
                </p>
                <p style="margin-top: 10px; color: #999;">Our craftsmen are working on new designs. Contact us for custom
                    orders.</p>
                <a href="<?php echo baseUrl('get-estimate.php'); ?>" class="btn btn-primary"
                    style="margin-top: 20px;">Request Custom Design</a>
            </div>
        <?php else: ?>
            <div class="design-cards-grid">
                <?php foreach ($categoryDesigns as $id => $design): ?>
                    <div class="design-card">
                        <div class="design-carousel" data-design-id="<?php echo $id; ?>">
                            <div class="carousel-slides">
                                <?php foreach ($design['images'] as $index => $image): ?>
                                    <div class="carousel-slide" onclick="openLightbox('<?php echo $id; ?>', <?php echo $index; ?>)">
                                        <img src="<?php echo SITE_URL . '/' . $image; ?>" alt="<?php echo $design['title']; ?>">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($design['images']) > 1): ?>
                                <button class="carousel-btn prev" onclick="moveCarousel('<?php echo $id; ?>', -1)">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button class="carousel-btn next" onclick="moveCarousel('<?php echo $id; ?>', 1)">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="design-card-content">
                            <h3>
                                <?php echo $design['title']; ?>
                            </h3>
                            <div class="design-card-actions">
                                <a href="<?php echo baseUrl('get-estimate.php'); ?>" class="btn-get-quote">Get a Quote</a>
                                <span class="explore-more-link" onclick="openExploreModal('<?php echo $id; ?>')">
                                    Explore more <i class="fas fa-chevron-right"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Explore Modal Sidebar -->
<div class="modal-overlay" id="modalOverlay" onclick="closeExploreModal()"></div>
<div class="explore-modal" id="exploreModal">
    <div class="explore-modal-header">
        <h4>More About This Product</h4>
        <button class="explore-modal-close" onclick="closeExploreModal()">&times;</button>
    </div>
    <div class="explore-modal-content">
        <h3 id="modalTitle"></h3>
        <div class="description" id="modalDescription"></div>
        <h5>Special Features</h5>
        <ul class="features-list" id="modalFeatures"></ul>
    </div>
    <div class="explore-modal-footer">
        <a href="<?php echo baseUrl('get-estimate.php'); ?>" class="btn-get-quote">Get a Quote</a>
    </div>
</div>

<!-- Hidden design data for JS -->
<script>
    const designsData = <?php echo json_encode($categoryDesigns); ?>;

    // Carousel positions
    const carouselPositions = {};

    function moveCarousel(designId, direction) {
        const carousel = document.querySelector(`[data-design-id="${designId}"] .carousel-slides`);
        const slides = carousel.querySelectorAll('.carousel-slide');
        const totalSlides = slides.length;

        if (!carouselPositions[designId]) carouselPositions[designId] = 0;

        carouselPositions[designId] += direction;

        if (carouselPositions[designId] < 0) carouselPositions[designId] = totalSlides - 1;
        if (carouselPositions[designId] >= totalSlides) carouselPositions[designId] = 0;

        carousel.style.transform = `translateX(-${carouselPositions[designId] * 100}%)`;
    }

    function openExploreModal(designId) {
        const design = designsData[designId];
        if (!design) return;

        document.getElementById('modalTitle').textContent = design.title;
        document.getElementById('modalDescription').innerHTML = design.description || 'This beautiful wooden design showcases premium craftsmanship and traditional aesthetics. Crafted with seasoned wood for long-lasting durability.';

        const featuresList = document.getElementById('modalFeatures');
        featuresList.innerHTML = '';

        if (design.features) {
            const features = design.features.split('\n').filter(f => f.trim());
            features.forEach(feature => {
                const li = document.createElement('li');
                li.textContent = feature.trim();
                featuresList.appendChild(li);
            });
        } else {
            // Default features
            const defaultFeatures = ['Premium quality wood', 'Expert craftsmanship', 'Custom sizes available', 'Termite resistant finish'];
            defaultFeatures.forEach(feature => {
                const li = document.createElement('li');
                li.textContent = feature;
                featuresList.appendChild(li);
            });
        }

        document.getElementById('modalOverlay').classList.add('active');
        document.getElementById('exploreModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeExploreModal() {
        document.getElementById('modalOverlay').classList.remove('active');
        document.getElementById('exploreModal').classList.remove('active');
        document.body.style.overflow = '';
    }

    // Close on ESC
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeExploreModal();
            closeLightbox();
        }
        // Arrow keys for lightbox navigation
        if (document.getElementById('lightbox').classList.contains('active')) {
            if (e.key === 'ArrowLeft') changeLightboxImage(-1);
            if (e.key === 'ArrowRight') changeLightboxImage(1);
        }
    });

    // Lightbox functionality
    let currentLightboxDesign = null;
    let currentLightboxIndex = 0;

    function openLightbox(designId, imageIndex) {
        const design = designsData[designId];
        if (!design || !design.images) return;

        currentLightboxDesign = design;
        currentLightboxIndex = imageIndex;

        updateLightboxImage();
        document.getElementById('lightbox').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        document.getElementById('lightbox').classList.remove('active');
        document.body.style.overflow = '';
        currentLightboxDesign = null;
    }

    function changeLightboxImage(direction) {
        if (!currentLightboxDesign) return;

        currentLightboxIndex += direction;
        const totalImages = currentLightboxDesign.images.length;

        if (currentLightboxIndex < 0) currentLightboxIndex = totalImages - 1;
        if (currentLightboxIndex >= totalImages) currentLightboxIndex = 0;

        updateLightboxImage();
    }

    function updateLightboxImage() {
        if (!currentLightboxDesign) return;

        const img = document.getElementById('lightboxImage');
        const counter = document.getElementById('lightboxCounter');
        const title = document.getElementById('lightboxTitle');

        img.src = '<?php echo SITE_URL; ?>/' + currentLightboxDesign.images[currentLightboxIndex];
        counter.textContent = `${currentLightboxIndex + 1} / ${currentLightboxDesign.images.length}`;
        title.textContent = currentLightboxDesign.title;
    }
</script>

<!-- Fullscreen Lightbox -->
<div class="lightbox" id="lightbox" onclick="closeLightbox()">
    <button class="lightbox-close" onclick="closeLightbox()">&times;</button>
    <button class="lightbox-nav prev" onclick="event.stopPropagation(); changeLightboxImage(-1)">
        <i class="fas fa-chevron-left"></i>
    </button>
    <div class="lightbox-image-container" onclick="event.stopPropagation()">
        <img id="lightboxImage" class="lightbox-image" src="" alt="Design Image">
    </div>
    <button class="lightbox-nav next" onclick="event.stopPropagation(); changeLightboxImage(1)">
        <i class="fas fa-chevron-right"></i>
    </button>
    <div class="lightbox-counter" id="lightboxCounter">1 / 1</div>
    <div class="lightbox-title" id="lightboxTitle"></div>
</div>

<!-- CTA -->
<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Order Your
                <?php echo $pageInfo['title']; ?>?
            </h2>
            <p>Get a free consultation and personalized quote from our woodwork experts.</p>
            <div class="cta-buttons">
                <a href="<?php echo baseUrl('get-estimate.php'); ?>" class="btn btn-primary btn-lg">Get Free
                    Estimate</a>
                <a href="<?php echo baseUrl('contact.php'); ?>" class="btn btn-white btn-lg">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>