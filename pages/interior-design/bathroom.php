<?php
/**
 * Interior Design Category Template
 * Professional design cards with carousel and explore modal
 */

// Get page info from URL
$slug = basename($_SERVER['PHP_SELF'], '.php');

$categories = [
    'modular-kitchen' => ['title' => 'Modular Kitchen Designs', 'icon' => 'utensils'],
    'customize-kitchen' => ['title' => 'Customize Your Kitchen', 'icon' => 'magic'],
    'wardrobe' => ['title' => 'Wardrobe Designs', 'icon' => 'door-closed'],
    'bedroom' => ['title' => 'Bedroom Interior Designs', 'icon' => 'bed'],
    'living-room' => ['title' => 'Living Room Designs', 'icon' => 'couch'],
    'kid-bedroom' => ['title' => 'Kid Bedroom Designs', 'icon' => 'child'],
    'dining-room' => ['title' => 'Dining Room Designs', 'icon' => 'utensils'],
    'pooja-room' => ['title' => 'Pooja Room Designs', 'icon' => 'praying-hands'],
    'space-saving' => ['title' => 'Space Saving Designs', 'icon' => 'compress-arrows-alt'],
    'home-office' => ['title' => 'Home Office Designs', 'icon' => 'laptop-house'],
    'bathroom' => ['title' => 'Bathroom Designs', 'icon' => 'bath'],
    'balcony' => ['title' => 'Balcony Designs', 'icon' => 'sun'],
    'tv-unit' => ['title' => 'TV Unit Designs', 'icon' => 'tv'],
    'crockery-unit' => ['title' => 'Crockery Unit Designs', 'icon' => 'wine-glass-alt'],
    'shoe-rack' => ['title' => 'Shoe Rack Designs', 'icon' => 'shoe-prints'],
    'study-table' => ['title' => 'Study Table Designs', 'icon' => 'book-open'],
    'false-ceiling' => ['title' => 'False Ceiling Designs', 'icon' => 'lightbulb']
];

$categoryInfo = $categories[$slug] ?? ['title' => ucwords(str_replace('-', ' ', $slug)), 'icon' => 'home'];
$pageTitle = $categoryInfo['title'];
$pageDescription = 'Explore our stunning ' . strtolower($categoryInfo['title']) . ' for your home.';

require_once __DIR__ . '/../../config/database.php';
include __DIR__ . '/../../includes/header.php';

// Load designs from JSON
$designsFile = __DIR__ . '/../../config/interior_designs.json';
$allDesigns = [];
if (file_exists($designsFile)) {
    $allDesigns = json_decode(file_get_contents($designsFile), true) ?? [];
}
$categoryDesigns = isset($allDesigns[$slug]) ? $allDesigns[$slug] : [];
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
        background: #00a5a5;
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

    .carousel-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 40px;
        height: 40px;
        background: #00a5a5;
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
        background: #008888;
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
        background: #00a5a5;
        color: white;
        padding: 10px 25px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: 500;
        transition: background 0.3s;
    }

    .btn-get-quote:hover {
        background: #008888;
        color: white;
    }

    .explore-more-link {
        color: #00a5a5;
        text-decoration: none;
        font-weight: 500;
        cursor: pointer;
        transition: color 0.3s;
    }

    .explore-more-link:hover {
        color: #008888;
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
        background: #00a5a5;
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
        color: #00a5a5;
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
        color: #00a5a5;
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .features-list li::before {
        content: "●";
        position: absolute;
        left: 0;
        color: #00a5a5;
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
        color: #ddd;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .design-cards-grid {
            grid-template-columns: 1fr;
        }

        .explore-modal {
            width: 100%;
            right: -100%;
        }
    }
</style>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1><?php echo $categoryInfo['title']; ?></h1>
        <p>Explore our stunning <?php echo strtolower($categoryInfo['title']); ?> for your home</p>
        <div class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>">Home</a>
            <span>/</span>
            <a href="<?php echo baseUrl('home-interior-designs.php'); ?>">Interior Designs</a>
            <span>/</span>
            <span><?php echo $categoryInfo['title']; ?></span>
        </div>
    </div>
</section>

<!-- Design Cards -->
<section class="design-cards-section">
    <div class="container">
        <?php if (empty($categoryDesigns)): ?>
            <div class="empty-state">
                <i class="fas fa-<?php echo $categoryInfo['icon']; ?>"></i>
                <h3>Coming Soon!</h3>
                <p>We're adding beautiful <?php echo strtolower($categoryInfo['title']); ?> designs. Check back soon!</p>
                <a href="<?php echo baseUrl('contact.php'); ?>" class="btn btn-primary" style="margin-top: 20px;">Contact
                    Us</a>
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
                            <h3><?php echo $design['title']; ?></h3>
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
        document.getElementById('modalDescription').innerHTML = design.description || 'This beautiful design showcases premium craftsmanship and modern aesthetics.';

        const featuresList = document.getElementById('modalFeatures');
        featuresList.innerHTML = '';

        if (design.features) {
            const features = design.features.split('\n').filter(f => f.trim());
            features.forEach(feature => {
                const li = document.createElement('li');
                li.textContent = feature.trim();
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
            <h2>Ready to Design Your <?php echo str_replace(' Designs', '', $categoryInfo['title']); ?>?</h2>
            <p>Get a free consultation and personalized quote from our design experts.</p>
            <div class="cta-buttons">
                <a href="<?php echo baseUrl('get-estimate.php'); ?>" class="btn btn-primary btn-lg">Get Free
                    Estimate</a>
                <a href="<?php echo baseUrl('contact.php'); ?>" class="btn btn-white btn-lg">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>