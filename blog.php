<?php
/**
 * KV Wood Works - Blog
 */

$pageTitle = 'Blog - Design Ideas & Tips';
$pageDescription = 'Read our blog for home interior design ideas, tips, and inspiration. Stay updated with the latest trends.';

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Blog</h1>
        <p>Design ideas, tips, and inspiration for your home</p>
        <div class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>">Home</a>
            <span>/</span>
            <span>Blog</span>
        </div>
    </div>
</section>

<!-- Blog Grid -->
<section class="section">
    <div class="container">
        <div class="blog-grid">
            <?php
            $blogs = [
                ['title' => '10 Modular Kitchen Design Ideas for 2025', 'excerpt' => 'Discover the latest trends in modular kitchen designs that are taking homes by storm. From sleek handleless cabinets to smart storage solutions.', 'category' => 'Kitchen', 'date' => 'Jan 5, 2025', 'readTime' => '5 min'],
                ['title' => 'How to Choose the Perfect Wardrobe for Your Bedroom', 'excerpt' => 'A complete guide to selecting the right wardrobe design based on your space, storage needs, and personal style preferences.', 'category' => 'Bedroom', 'date' => 'Jan 3, 2025', 'readTime' => '4 min'],
                ['title' => 'Space-Saving Interior Ideas for Small Homes', 'excerpt' => 'Smart solutions to maximize space in compact apartments. Learn how to make your small home feel spacious and organized.', 'category' => 'Tips', 'date' => 'Dec 28, 2024', 'readTime' => '6 min'],
                ['title' => 'Traditional vs Modern Vasakal Designs', 'excerpt' => 'Explore the difference between traditional wooden vasakal designs and contemporary styles. Find what suits your home best.', 'category' => 'Wooden Works', 'date' => 'Dec 22, 2024', 'readTime' => '4 min'],
                ['title' => 'Living Room Design Trends to Watch in 2025', 'excerpt' => 'From maximalism to sustainable materials, discover the living room trends that will define home interiors this year.', 'category' => 'Living Room', 'date' => 'Dec 18, 2024', 'readTime' => '5 min'],
                ['title' => 'Pooja Room Designs: Blending Tradition with Modern Aesthetics', 'excerpt' => 'Create a sacred yet stylish pooja room with these design ideas that honor tradition while fitting your modern home.', 'category' => 'Pooja Room', 'date' => 'Dec 14, 2024', 'readTime' => '4 min'],
            ];

            foreach ($blogs as $blog): ?>
                <article class="blog-card">
                    <div class="blog-image">
                        <div class="placeholder-image" style="height: 220px;">
                            <i class="fas fa-newspaper"></i>
                        </div>
                    </div>
                    <div class="blog-content">
                        <div class="blog-meta">
                            <span><i class="fas fa-folder"></i>
                                <?php echo $blog['category']; ?>
                            </span>
                            <span><i class="fas fa-calendar"></i>
                                <?php echo $blog['date']; ?>
                            </span>
                            <span><i class="fas fa-clock"></i>
                                <?php echo $blog['readTime']; ?> read
                            </span>
                        </div>
                        <h4>
                            <?php echo $blog['title']; ?>
                        </h4>
                        <p>
                            <?php echo $blog['excerpt']; ?>
                        </p>
                        <a href="#" class="read-more">Read More <i class="fas fa-arrow-right"></i></a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>