<?php
/**
 * KV Wood Works - Individual Project Page
 * Dynamic project detail page with image gallery
 */

require_once 'config/database.php';

// Get project ID from URL
$projectId = isset($_GET['id']) ? sanitize($_GET['id']) : '';

// Load projects from JSON
$projectsFile = __DIR__ . '/config/projects.json';
$projects = [];
if (file_exists($projectsFile)) {
    $projects = json_decode(file_get_contents($projectsFile), true) ?? [];
}

// Get the specific project
$project = isset($projects[$projectId]) ? $projects[$projectId] : null;

if (!$project) {
    // Redirect to projects page if project not found
    header('Location: recent-projects.php');
    exit;
}

$pageTitle = $project['name'] . ' | Project';
$pageDescription = $project['description'] ?? 'View details of our ' . $project['name'] . ' project.';

include 'includes/header.php';
?>

<style>
    .project-detail-section {
        padding: 60px 0;
    }

    .project-detail-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 40px;
    }

    /* Main Image Gallery */
    .project-gallery {
        position: relative;
    }

    .main-image {
        width: 100%;
        height: 450px;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 15px;
    }

    .main-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .thumbnail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 10px;
    }

    .thumbnail {
        height: 80px;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
        border: 3px solid transparent;
        transition: all 0.3s;
    }

    .thumbnail:hover,
    .thumbnail.active {
        border-color: #c8956c;
    }

    .thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Project Info */
    .project-info {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .project-info h1 {
        font-size: 1.8rem;
        color: #333;
        margin-bottom: 15px;
    }

    .project-location {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #666;
        font-size: 1.1rem;
        margin-bottom: 20px;
    }

    .project-location i {
        color: #c8956c;
    }

    .project-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 25px;
    }

    .project-tag {
        background: linear-gradient(135deg, #c8956c 0%, #a87754 100%);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .project-description {
        color: #555;
        line-height: 1.8;
        margin-bottom: 30px;
        font-size: 1.05rem;
    }

    .project-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 30px;
    }

    .stat-item {
        background: #f8f8f8;
        padding: 15px;
        border-radius: 8px;
        text-align: center;
    }

    .stat-item i {
        font-size: 1.5rem;
        color: #c8956c;
        margin-bottom: 8px;
    }

    .stat-item h4 {
        font-size: 1.2rem;
        color: #333;
        margin-bottom: 5px;
    }

    .stat-item p {
        font-size: 0.85rem;
        color: #666;
    }

    .project-cta {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .btn-quote {
        background: linear-gradient(135deg, #c8956c 0%, #a87754 100%);
        color: white;
        padding: 15px 30px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        text-align: center;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .btn-quote:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(200, 149, 108, 0.4);
        color: white;
    }

    .btn-whatsapp {
        background: #25D366;
        color: white;
        padding: 15px 30px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: transform 0.3s;
    }

    .btn-whatsapp:hover {
        transform: translateY(-2px);
        color: white;
    }

    /* Lightbox */
    .lightbox {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.95);
        z-index: 10000;
        display: none;
        align-items: center;
        justify-content: center;
    }

    .lightbox.active {
        display: flex;
    }

    .lightbox-content {
        max-width: 90%;
        max-height: 90%;
    }

    .lightbox-content img {
        max-width: 100%;
        max-height: 90vh;
        object-fit: contain;
    }

    .lightbox-close {
        position: absolute;
        top: 20px;
        right: 30px;
        font-size: 2rem;
        color: white;
        cursor: pointer;
        background: none;
        border: none;
    }

    .lightbox-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        font-size: 2rem;
        color: white;
        cursor: pointer;
        background: rgba(255, 255, 255, 0.1);
        border: none;
        padding: 20px;
        border-radius: 5px;
    }

    .lightbox-nav.prev {
        left: 20px;
    }

    .lightbox-nav.next {
        right: 20px;
    }

    /* Back Link */
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #c8956c;
        text-decoration: none;
        font-weight: 500;
        margin-bottom: 30px;
    }

    .back-link:hover {
        color: #a87754;
    }

    @media (max-width: 968px) {
        .project-detail-grid {
            grid-template-columns: 1fr;
        }

        .main-image {
            height: 300px;
        }
    }

    /* Project Details Section */
    .project-details-section {
        padding: 60px 0;
        background: #f9f9f9;
    }

    .details-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        margin-bottom: 30px;
    }

    .details-card-header {
        background: linear-gradient(135deg, #c8956c 0%, #a87754 100%);
        color: white;
        padding: 20px 25px;
    }

    .details-card-header h3 {
        font-size: 1.3rem;
        font-weight: 600;
    }

    .details-card-body {
        padding: 25px;
    }

    .property-table {
        width: 100%;
        border-collapse: collapse;
    }

    .property-table th,
    .property-table td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #eee;
    }

    .property-table th {
        color: #666;
        font-weight: 500;
        width: 30%;
        background: #fafafa;
    }

    .property-table td {
        color: #333;
        font-weight: 500;
    }

    .brief-text {
        color: #555;
        line-height: 1.8;
        font-size: 1rem;
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
    }
</style>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1><?php echo $project['name']; ?></h1>
        <p>Project Showcase</p>
        <div class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>">Home</a>
            <span>/</span>
            <a href="<?php echo baseUrl('recent-projects.php'); ?>">Projects</a>
            <span>/</span>
            <span><?php echo $project['name']; ?></span>
        </div>
    </div>
</section>

<!-- Project Detail -->
<section class="project-detail-section">
    <div class="container">
        <a href="<?php echo baseUrl('recent-projects.php'); ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to All Projects
        </a>

        <div class="project-detail-grid">
            <!-- Image Gallery -->
            <div class="project-gallery">
                <div class="main-image" onclick="openLightbox(0)">
                    <?php if (!empty($project['images'])): ?>
                        <img src="<?php echo SITE_URL . '/' . $project['images'][0]; ?>"
                            alt="<?php echo $project['name']; ?>" id="mainImage">
                    <?php else: ?>
                        <div
                            style="width: 100%; height: 100%; background: #f5f5f5; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-image" style="font-size: 4rem; color: #ccc;"></i>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($project['images']) && count($project['images']) > 1): ?>
                    <div class="thumbnail-grid">
                        <?php foreach ($project['images'] as $index => $image): ?>
                            <div class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>"
                                onclick="changeImage(<?php echo $index; ?>, '<?php echo SITE_URL . '/' . $image; ?>')">
                                <img src="<?php echo SITE_URL . '/' . $image; ?>" alt="Thumbnail <?php echo $index + 1; ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Project Info -->
            <div class="project-info">
                <h1><?php echo $project['name']; ?></h1>

                <p class="project-location">
                    <i class="fas fa-map-marker-alt"></i>
                    <?php echo $project['location']; ?>
                </p>

                <?php if (!empty($project['tags'])): ?>
                    <div class="project-tags">
                        <?php foreach ($project['tags'] as $tag): ?>
                            <span class="project-tag"><?php echo $tag; ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($project['description'])): ?>
                    <p class="project-description"><?php echo nl2br($project['description']); ?></p>
                <?php endif; ?>

                <div class="project-stats">
                    <div class="stat-item">
                        <i class="fas fa-images"></i>
                        <h4><?php echo count($project['images'] ?? []); ?></h4>
                        <p>Photos</p>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-tags"></i>
                        <h4><?php echo count($project['tags'] ?? []); ?></h4>
                        <p>Categories</p>
                    </div>
                </div>

                <div class="project-cta">
                    <a href="<?php echo baseUrl('get-estimate.php'); ?>" class="btn-quote">
                        <i class="fas fa-calculator"></i> Get Similar Project Quote
                    </a>
                    <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', SITE_WHATSAPP); ?>?text=Hi, I'm interested in a project similar to '<?php echo urlencode($project['name']); ?>'"
                        class="btn-whatsapp">
                        <i class="fab fa-whatsapp"></i> Chat on WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Project Details Section -->
<?php if (!empty($project['property_area']) || !empty($project['property_type']) || !empty($project['client_brief']) || !empty($project['design_solution'])): ?>
<section class="project-details-section">
    <div class="container">
        <!-- Project Details Card -->
        <?php if (!empty($project['property_area']) || !empty($project['property_type'])): ?>
        <div class="details-card">
            <div class="details-card-header">
                <h3><i class="fas fa-info-circle"></i> Project Details</h3>
            </div>
            <div class="details-card-body">
                <table class="property-table">
                    <tr>
                        <th>Project Name</th>
                        <td><?php echo $project['name']; ?></td>
                    </tr>
                    <?php if (!empty($project['property_area'])): ?>
                    <tr>
                        <th>Property Area</th>
                        <td><?php echo $project['property_area']; ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($project['property_type'])): ?>
                    <tr>
                        <th>Property Type</th>
                        <td><?php echo $project['property_type']; ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <th>Location</th>
                        <td><?php echo $project['location']; ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="details-grid">
            <!-- Client Brief -->
            <?php if (!empty($project['client_brief'])): ?>
            <div class="details-card">
                <div class="details-card-header">
                    <h3><i class="fas fa-user-tie"></i> Client Brief</h3>
                </div>
                <div class="details-card-body">
                    <p class="brief-text"><?php echo nl2br($project['client_brief']); ?></p>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Design Solution -->
            <?php if (!empty($project['design_solution'])): ?>
            <div class="details-card">
                <div class="details-card-header">
                    <h3><i class="fas fa-lightbulb"></i> Design Solution</h3>
                </div>
                <div class="details-card-body">
                    <p class="brief-text"><?php echo nl2br($project['design_solution']); ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Lightbox for Full Image View -->
<div class="lightbox" id="lightbox">
    <button class="lightbox-close" onclick="closeLightbox()">&times;</button>
    <button class="lightbox-nav prev" onclick="navigateLightbox(-1)"><i class="fas fa-chevron-left"></i></button>
    <div class="lightbox-content">
        <img src="" alt="Full view" id="lightboxImage">
    </div>
    <button class="lightbox-nav next" onclick="navigateLightbox(1)"><i class="fas fa-chevron-right"></i></button>
</div>

<script>
    const images = <?php echo json_encode($project['images'] ?? []); ?>;
    let currentImageIndex = 0;

    function changeImage(index, src) {
        document.getElementById('mainImage').src = src;
        currentImageIndex = index;

        // Update active thumbnail
        document.querySelectorAll('.thumbnail').forEach((thumb, i) => {
            thumb.classList.toggle('active', i === index);
        });
    }

    function openLightbox(index) {
        currentImageIndex = index;
        const lightbox = document.getElementById('lightbox');
        const lightboxImage = document.getElementById('lightboxImage');

        if (images.length > 0) {
            lightboxImage.src = '<?php echo SITE_URL; ?>/' + images[currentImageIndex];
            lightbox.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeLightbox() {
        document.getElementById('lightbox').classList.remove('active');
        document.body.style.overflow = '';
    }

    function navigateLightbox(direction) {
        currentImageIndex += direction;
        if (currentImageIndex < 0) currentImageIndex = images.length - 1;
        if (currentImageIndex >= images.length) currentImageIndex = 0;

        document.getElementById('lightboxImage').src = '<?php echo SITE_URL; ?>/' + images[currentImageIndex];
    }

    // Close lightbox on ESC
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') navigateLightbox(-1);
        if (e.key === 'ArrowRight') navigateLightbox(1);
    });

    // Close lightbox when clicking outside image
    document.getElementById('lightbox').addEventListener('click', function (e) {
        if (e.target === this) closeLightbox();
    });
</script>

<!-- CTA -->
<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2>Want a Similar Project?</h2>
            <p>Let us transform your home with the same level of quality and attention to detail.</p>
            <div class="cta-buttons">
                <a href="get-estimate.php" class="btn btn-primary btn-lg">Get Free Estimate</a>
                <a href="contact.php" class="btn btn-white btn-lg">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>