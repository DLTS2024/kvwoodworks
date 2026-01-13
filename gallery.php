<?php
/**
 * KV Wood Works - Gallery Page
 * Dynamic gallery that reads images from folders
 */

$pageTitle = 'Gallery';
$pageDescription = 'Browse our complete gallery of home interior designs, modular kitchens, wardrobes, and wooden works.';

include 'includes/header.php';

// Get all category folders from gallery directory
$galleryPath = __DIR__ . '/assets/images/gallery/';
$categories = [];

// Create gallery folder if it doesn't exist
if (!file_exists($galleryPath)) {
    mkdir($galleryPath, 0755, true);
}

// Read all folders in gallery directory
if (is_dir($galleryPath)) {
    $folders = scandir($galleryPath);
    foreach ($folders as $folder) {
        if ($folder !== '.' && $folder !== '..' && is_dir($galleryPath . $folder)) {
            // Count images in this folder
            $images = glob($galleryPath . $folder . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
            if (count($images) > 0) {
                $categories[] = [
                    'name' => ucwords(str_replace(['-', '_'], ' ', $folder)),
                    'slug' => $folder,
                    'count' => count($images),
                    'thumbnail' => $images[0] // First image as thumbnail
                ];
            }
        }
    }
}

// Get selected category from URL
$selectedCategory = isset($_GET['category']) ? sanitize($_GET['category']) : '';
$galleryImages = [];

if ($selectedCategory && is_dir($galleryPath . $selectedCategory)) {
    $images = glob($galleryPath . $selectedCategory . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
    foreach ($images as $image) {
        $galleryImages[] = [
            'path' => 'assets/images/gallery/' . $selectedCategory . '/' . basename($image),
            'name' => pathinfo($image, PATHINFO_FILENAME)
        ];
    }
}
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Our Gallery</h1>
        <p>Browse our collection of beautiful home transformations</p>
        <div class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>">Home</a>
            <span>/</span>
            <?php if ($selectedCategory): ?>
                <a href="<?php echo baseUrl('gallery.php'); ?>">Gallery</a>
                <span>/</span>
                <span><?php echo ucwords(str_replace(['-', '_'], ' ', $selectedCategory)); ?></span>
            <?php else: ?>
                <span>Gallery</span>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if (!$selectedCategory): ?>
    <!-- Category Grid -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2>Browse By Category</h2>
                <p>Select a category to view designs</p>
            </div>

            <?php if (empty($categories)): ?>
                <div
                    style="text-align: center; padding: 60px 20px; background: var(--light-gray); border-radius: var(--radius-lg);">
                    <i class="fas fa-images" style="font-size: 4rem; color: var(--gray); margin-bottom: 20px;"></i>
                    <h3>No Gallery Items Yet</h3>
                    <p style="color: var(--gray);">Gallery under construction. Check back soon!</p>
                </div>
            <?php else: ?>
                <div class="categories-grid">
                    <?php foreach ($categories as $cat): ?>
                        <a href="<?php echo baseUrl('gallery.php?category=' . $cat['slug']); ?>" class="category-card">
                            <?php
                            $thumbPath = str_replace(__DIR__ . '/', '', $cat['thumbnail']);
                            ?>
                            <img src="<?php echo baseUrl($thumbPath); ?>" alt="<?php echo $cat['name']; ?>"
                                style="width: 100%; height: 300px; object-fit: cover;">
                            <div class="category-overlay">
                                <h3><?php echo $cat['name']; ?></h3>
                                <p><?php echo $cat['count']; ?> Photos</p>
                                <span class="category-link">View Gallery <i class="fas fa-arrow-right"></i></span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

<?php else: ?>
    <!-- Image Gallery -->
    <section class="section">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h2><?php echo ucwords(str_replace(['-', '_'], ' ', $selectedCategory)); ?></h2>
                <a href="<?php echo baseUrl('gallery.php'); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Categories
                </a>
            </div>

            <?php if (empty($galleryImages)): ?>
                <div
                    style="text-align: center; padding: 60px 20px; background: var(--light-gray); border-radius: var(--radius-lg);">
                    <i class="fas fa-image" style="font-size: 4rem; color: var(--gray); margin-bottom: 20px;"></i>
                    <h3>No Images Found</h3>
                    <p style="color: var(--gray);">This category has no images yet.</p>
                </div>
            <?php else: ?>
                <div class="gallery-grid">
                    <?php foreach ($galleryImages as $image): ?>
                        <div class="gallery-item" onclick="openLightbox('<?php echo baseUrl($image['path']); ?>')">
                            <img src="<?php echo baseUrl($image['path']); ?>" alt="<?php echo $image['name']; ?>" loading="lazy">
                            <div class="gallery-overlay">
                                <i class="fas fa-expand"></i>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>

<!-- Lightbox Modal -->
<div id="lightbox" class="lightbox" onclick="closeLightbox()">
    <span class="lightbox-close">&times;</span>
    <img id="lightbox-image" src="" alt="">
</div>

<style>
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }

    .gallery-item {
        position: relative;
        border-radius: var(--radius-md);
        overflow: hidden;
        cursor: pointer;
        aspect-ratio: 4/3;
    }

    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .gallery-item:hover img {
        transform: scale(1.05);
    }

    .gallery-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .gallery-item:hover .gallery-overlay {
        opacity: 1;
    }

    .gallery-overlay i {
        color: white;
        font-size: 2rem;
    }

    /* Lightbox */
    .lightbox {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.95);
        z-index: 10000;
        align-items: center;
        justify-content: center;
        padding: 40px;
    }

    .lightbox.active {
        display: flex;
    }

    .lightbox img {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
        border-radius: var(--radius-md);
    }

    .lightbox-close {
        position: absolute;
        top: 20px;
        right: 30px;
        color: white;
        font-size: 3rem;
        cursor: pointer;
    }
</style>

<script>
    function openLightbox(src) {
        document.getElementById('lightbox-image').src = src;
        document.getElementById('lightbox').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        document.getElementById('lightbox').classList.remove('active');
        document.body.style.overflow = '';
    }

    // Close on ESC key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeLightbox();
    });
</script>

<?php include 'includes/footer.php'; ?>