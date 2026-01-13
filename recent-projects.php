<?php
/**
 * KV Wood Works - Recent Projects
 * Dynamic project gallery from admin-managed JSON
 */

$pageTitle = 'Recent Projects';
$pageDescription = 'View our portfolio of completed home interior and wooden works projects across Chennai and Tamil Nadu.';

require_once 'config/database.php';
include 'includes/header.php';

// Load projects from JSON
$projectsFile = __DIR__ . '/config/projects.json';
$projects = [];
if (file_exists($projectsFile)) {
    $projects = json_decode(file_get_contents($projectsFile), true) ?? [];
}

// Get unique tags from all projects for filter buttons
$allTags = [];
foreach ($projects as $project) {
    if (!empty($project['tags'])) {
        $allTags = array_merge($allTags, $project['tags']);
    }
}
$allTags = array_unique($allTags);
?>

<style>
    .projects-filter {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
        margin-bottom: 40px;
    }

    .filter-btn {
        padding: 10px 20px;
        border: 2px solid #e5e5e5;
        background: white;
        border-radius: 25px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s;
    }

    .filter-btn:hover,
    .filter-btn.active {
        background: #c8956c;
        color: white;
        border-color: #c8956c;
    }

    .projects-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 30px;
    }

    .project-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .project-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
    }

    .project-card.hidden {
        display: none;
    }

    .project-image {
        height: 250px;
        position: relative;
        overflow: hidden;
    }

    .project-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s;
    }

    .project-card:hover .project-image img {
        transform: scale(1.05);
    }

    .project-image .placeholder-image {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .project-image .placeholder-image i {
        font-size: 4rem;
        color: #ccc;
    }

    .project-tags-overlay {
        position: absolute;
        top: 10px;
        left: 10px;
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }

    .project-tag-badge {
        background: #c8956c;
        color: white;
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .image-count-badge {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 5px 12px;
        border-radius: 5px;
        font-size: 0.8rem;
    }

    .project-content {
        padding: 20px;
    }

    .project-content h4 {
        font-size: 1.2rem;
        margin-bottom: 10px;
        color: #333;
    }

    .project-location {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .project-location i {
        color: #c8956c;
    }

    .project-description {
        color: #666;
        font-size: 0.9rem;
        line-height: 1.6;
        margin-bottom: 15px;
    }

    .project-actions {
        display: flex;
        gap: 10px;
    }

    .btn-view-project {
        background: #c8956c;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: 500;
        transition: background 0.3s;
    }

    .btn-view-project:hover {
        background: #a87754;
        color: white;
    }
    
    .btn-get-quote-small {
        background: transparent;
        color: #c8956c;
        padding: 10px 15px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: 500;
        border: 2px solid #c8956c;
        transition: all 0.3s;
    }
    
    .btn-get-quote-small:hover {
        background: #c8956c;
        color: white;
    }
    
    .project-link {
        display: block;
        text-decoration: none;
    }
    
    .project-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

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
        .projects-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Recent Projects</h1>
        <p>Explore our portfolio of beautiful home transformations</p>
        <div class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>">Home</a>
            <span>/</span>
            <span>Recent Projects</span>
        </div>
    </div>
</section>

<!-- Projects Gallery -->
<section class="section">
    <div class="container">
        <!-- Filter Buttons -->
        <?php if (!empty($allTags)): ?>
            <div class="projects-filter">
                <button class="filter-btn active" data-filter="all">All Projects</button>
                <?php foreach ($allTags as $tag): ?>
                    <button class="filter-btn"
                        data-filter="<?php echo strtolower(str_replace(' ', '-', $tag)); ?>"><?php echo $tag; ?></button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($projects)): ?>
            <div class="empty-state">
                <i class="fas fa-project-diagram"></i>
                <h3>Projects Coming Soon!</h3>
                <p>We're adding our recent project showcases. Check back soon!</p>
                <a href="<?php echo baseUrl('contact.php'); ?>" class="btn btn-primary" style="margin-top: 20px;">Contact
                    Us</a>
            </div>
        <?php else: ?>
            <div class="projects-grid">
                <?php foreach ($projects as $id => $project):
                    $filterTags = array_map(function ($tag) {
                        return strtolower(str_replace(' ', '-', $tag));
                    }, $project['tags'] ?? []);
                    $dataCategories = implode(' ', $filterTags);
                    ?>
                    <div class="project-card" data-categories="<?php echo $dataCategories; ?>">
                        <a href="<?php echo baseUrl('project.php?id=' . $id); ?>" class="project-link">
                            <div class="project-image">
                                <?php if (!empty($project['images'])): ?>
                                    <img src="<?php echo SITE_URL . '/' . $project['images'][0]; ?>"
                                        alt="<?php echo $project['name']; ?>">
                                    <?php if (count($project['images']) > 1): ?>
                                        <span class="image-count-badge"><i class="fas fa-images"></i>
                                            <?php echo count($project['images']); ?> photos</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="placeholder-image">
                                        <i class="fas fa-home"></i>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($project['tags'])): ?>
                                    <div class="project-tags-overlay">
                                        <?php foreach (array_slice($project['tags'], 0, 2) as $tag): ?>
                                            <span class="project-tag-badge"><?php echo $tag; ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </a>
                        <div class="project-content">
                            <h4><a href="<?php echo baseUrl('project.php?id=' . $id); ?>"
                                    style="color: inherit; text-decoration: none;"><?php echo $project['name']; ?></a></h4>
                            <p class="project-location">
                                <i class="fas fa-map-marker-alt"></i> <?php echo $project['location']; ?>
                            </p>
                            <?php if (!empty($project['description'])): ?>
                                <p class="project-description">
                                    <?php echo substr($project['description'], 0, 120); ?>            <?php echo strlen($project['description']) > 120 ? '...' : ''; ?>
                                </p>
                            <?php endif; ?>
                            <div class="project-actions">
                                <a href="<?php echo baseUrl('project.php?id=' . $id); ?>" class="btn-view-project">View
                                    Project</a>
                                <a href="<?php echo baseUrl('get-estimate.php'); ?>" class="btn-get-quote-small">Get Quote</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA -->
<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2>Want Similar Results?</h2>
            <p>Let us transform your home with the same level of quality and attention to detail.</p>
            <div class="cta-buttons">
                <a href="get-estimate.php" class="btn btn-primary btn-lg">Start Your Project</a>
            </div>
        </div>
    </div>
</section>

<script>
    // Filter functionality
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            // Update active button
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const filter = this.dataset.filter;

            document.querySelectorAll('.project-card').forEach(card => {
                if (filter === 'all') {
                    card.classList.remove('hidden');
                } else {
                    const categories = card.dataset.categories || '';
                    if (categories.includes(filter)) {
                        card.classList.remove('hidden');
                    } else {
                        card.classList.add('hidden');
                    }
                }
            });
        });
    });
</script>

<?php include 'includes/footer.php'; ?>