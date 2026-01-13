<?php
/**
 * KV Wood Works - Main Admin Panel
 * Central dashboard for managing all website content
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$pageTitle = 'Admin Dashboard';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> | KV Wood Works</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #c8956c;
            --primary-dark: #a87754;
            --accent: #2d5a4a;
            --dark: #1a1a1a;
            --light-gray: #f5f5f5;
            --gray: #666;
            --white: #fff;
            --success: #22c55e;
            --warning: #f59e0b;
            --error: #ef4444;
            --info: #3b82f6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            min-height: 100vh;
            color: var(--white);
        }

        .admin-header {
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .admin-logo-icon {
            width: 50px;
            height: 50px;
            background: var(--primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .admin-logo h1 {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .admin-logo h1 span {
            color: var(--primary);
        }

        .admin-nav a {
            color: var(--white);
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s;
            margin-left: 10px;
        }

        .admin-nav a:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .admin-nav a.btn-primary {
            background: var(--primary);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px;
        }

        .welcome-section {
            text-align: center;
            margin-bottom: 50px;
        }

        .welcome-section h2 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .welcome-section p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.1rem;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }

        .dashboard-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 30px;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: var(--white);
            display: block;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary);
        }

        .card-icon {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .card-icon.gallery {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .card-icon.projects {
            background: linear-gradient(135deg, #f093fb, #f5576c);
        }

        .card-icon.wooden {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
        }

        .card-icon.offers {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
        }

        .card-icon.interiors {
            background: linear-gradient(135deg, #fa709a, #fee140);
        }

        .card-icon.blogs {
            background: linear-gradient(135deg, #a8edea, #fed6e3);
            color: #333;
        }

        .card-icon.popup {
            background: linear-gradient(135deg, #ff6b6b, #ffa500);
        }

        .card-icon.brands {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .card-icon.leads {
            background: linear-gradient(135deg, #22c55e, #16a34a);
        }

        .dashboard-card h3 {
            font-size: 1.4rem;
            margin-bottom: 10px;
        }

        .dashboard-card p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.95rem;
            margin-bottom: 20px;
        }

        .card-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary);
            font-weight: 600;
        }

        .card-action i {
            transition: transform 0.3s;
        }

        .dashboard-card:hover .card-action i {
            transform: translateX(5px);
        }

        .quick-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 25px;
            text-align: center;
        }

        .stat-card .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
        }

        .stat-card .stat-label {
            color: rgba(255, 255, 255, 0.6);
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            .quick-stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <header class="admin-header">
        <div class="admin-logo">
            <div class="admin-logo-icon">
                <i class="fas fa-cog"></i>
            </div>
            <h1>KV <span>Admin Panel</span></h1>
        </div>
        <nav class="admin-nav">
            <a href="<?php echo SITE_URL; ?>" target="_blank"><i class="fas fa-external-link-alt"></i> View Site</a>
            <a href="<?php echo SITE_URL; ?>/gallery.php" target="_blank"><i class="fas fa-images"></i> Gallery</a>
        </nav>
    </header>

    <div class="container">
        <div class="welcome-section">
            <h2>Welcome to Admin Dashboard</h2>
            <p>Manage your website content, galleries, offers, and more from one place</p>
        </div>

        <!-- Quick Stats -->
        <?php
        $galleryPath = __DIR__ . '/../assets/images/gallery/';
        $categoryCount = 0;
        $imageCount = 0;

        if (is_dir($galleryPath)) {
            $folders = array_filter(scandir($galleryPath), fn($f) => $f !== '.' && $f !== '..' && is_dir($galleryPath . $f));
            $categoryCount = count($folders);
            foreach ($folders as $folder) {
                $images = glob($galleryPath . $folder . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
                $imageCount += count($images);
            }
        }
        ?>
        <div class="quick-stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $categoryCount; ?></div>
                <div class="stat-label">Gallery Categories</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $imageCount; ?></div>
                <div class="stat-label">Total Images</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">12</div>
                <div class="stat-label">Interior Designs</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">3</div>
                <div class="stat-label">Wooden Works</div>
            </div>
        </div>

        <!-- Dashboard Cards -->
        <div class="dashboard-grid">
            <!-- Update Gallery -->
            <a href="gallery.php" class="dashboard-card">
                <div class="card-icon gallery">
                    <i class="fas fa-images"></i>
                </div>
                <h3>Update Gallery</h3>
                <p>Upload new images, create categories, and manage your project gallery.</p>
                <span class="card-action">Manage Gallery <i class="fas fa-arrow-right"></i></span>
            </a>

            <!-- Update Projects -->
            <a href="projects.php" class="dashboard-card">
                <div class="card-icon projects">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <h3>Update Projects</h3>
                <p>Add new projects, update descriptions, and showcase your recent work.</p>
                <span class="card-action">Manage Projects <i class="fas fa-arrow-right"></i></span>
            </a>

            <!-- Update Wooden Works -->
            <a href="wooden-works.php" class="dashboard-card">
                <div class="card-icon wooden">
                    <i class="fas fa-tree"></i>
                </div>
                <h3>Update Wooden Works</h3>
                <p>Manage Vasakal, Window/Janal, and Staircase page images and content.</p>
                <span class="card-action">Manage Wooden Works <i class="fas fa-arrow-right"></i></span>
            </a>

            <!-- Update Offers / Banners -->
            <a href="banners.php" class="dashboard-card">
                <div class="card-icon offers">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <h3>Offer Banners</h3>
                <p>Manage promotional banners that appear on every page of the website.</p>
                <span class="card-action">Manage Banners <i class="fas fa-arrow-right"></i></span>
            </a>

            <!-- Update Home Interiors -->
            <a href="home-interiors.php" class="dashboard-card">
                <div class="card-icon interiors">
                    <i class="fas fa-couch"></i>
                </div>
                <h3>Update Home Interiors</h3>
                <p>Manage kitchen, bedroom, living room, and other interior designs.</p>
                <span class="card-action">Manage Interiors <i class="fas fa-arrow-right"></i></span>
            </a>

            <!-- Update Guides -->
            <a href="guides.php" class="dashboard-card">
                <div class="card-icon blogs">
                    <i class="fas fa-book-open"></i>
                </div>
                <h3>Design Guides</h3>
                <p>Manage interior design guides, tips, and expert content.</p>
                <span class="card-action">Manage Guides <i class="fas fa-arrow-right"></i></span>
            </a>

            <!-- Popup Settings -->
            <a href="popup.php" class="dashboard-card">
                <div class="card-icon popup">
                    <i class="fas fa-window-restore"></i>
                </div>
                <h3>Popup Settings</h3>
                <p>Manage the consultation popup form that appears on all pages.</p>
                <span class="card-action">Manage Popup <i class="fas fa-arrow-right"></i></span>
            </a>

            <!-- Leads & Inquiries -->
            <a href="leads.php" class="dashboard-card">
                <div class="card-icon leads">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Leads & Inquiries</h3>
                <p>View contact form, estimate requests, and popup form submissions.</p>
                <span class="card-action">View Leads <i class="fas fa-arrow-right"></i></span>
            </a>

            <!-- Branded Materials -->
            <a href="brands.php" class="dashboard-card">
                <div class="card-icon brands">
                    <i class="fas fa-award"></i>
                </div>
                <h3>Branded Materials</h3>
                <p>Update brand logos like Hettich, Hafele, Century, etc.</p>
                <span class="card-action">Manage Brands <i class="fas fa-arrow-right"></i></span>
            </a>
        </div>
    </div>
</body>

</html>