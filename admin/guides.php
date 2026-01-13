<?php
/**
 * KV Wood Works - Admin Guides Management
 * Manage design guides content
 */

 require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$pageTitle = 'Manage Guides';

// Load guides data
$guidesFile = __DIR__ . '/../config/guides.json';
$uploadDir = __DIR__ . '/../assets/images/guides/';

// Create upload directory if not exists
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$guidesData = [];
if (file_exists($guidesFile)) {
    $guidesData = json_decode(file_get_contents($guidesFile), true) ?? ['categories' => [], 'guides' => []];
} else {
    $guidesData = ['categories' => [], 'guides' => []];
}

$categories = $guidesData['categories'] ?? [];
$guides = $guidesData['guides'] ?? [];

$message = '';
$messageType = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Add/Edit Guide
    if (isset($_POST['save_guide'])) {
        $guideId = sanitize($_POST['guide_id'] ?? '');
        $title = sanitize($_POST['title'] ?? '');
        $slug = sanitize($_POST['slug'] ?? '');
        $category = sanitize($_POST['category'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $readTime = sanitize($_POST['read_time'] ?? '5 min');
        $featured = isset($_POST['featured']) ? true : false;
        $enabled = isset($_POST['enabled']) ? true : false;
        
        if ($title && $slug && $category) {
            // Handle thumbnail upload
            $thumbnail = '';
            if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $fileType = $_FILES['thumbnail']['type'];
                
                if (in_array($fileType, $allowedTypes)) {
                    $extension = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
                    $newFilename = $slug . '-thumb-' . time() . '.' . $extension;
                    $targetPath = $uploadDir . $newFilename;
                    
                    if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $targetPath)) {
                        $thumbnail = 'assets/images/guides/' . $newFilename;
                    }
                }
            }
            
            // Preserve existing thumbnail if no new upload
            $existingIndex = array_search($guideId, array_column($guides, 'id'));
            if (empty($thumbnail) && $existingIndex !== false && isset($guides[$existingIndex]['thumbnail'])) {
                $thumbnail = $guides[$existingIndex]['thumbnail'];
            }
            
            $guideData = [
                'id' => $guideId ?: strtolower(str_replace(' ', '-', $title)),
                'title' => $title,
                'slug' => $slug,
                'category' => $category,
                'description' => $description,
                'read_time' => $readTime,
                'featured' => $featured,
                'enabled' => $enabled,
                'thumbnail' => $thumbnail,
                'updated' => date('Y-m-d'),
                'sections' => []
            ];
            
            // Check if editing existing guide
            if ($existingIndex !== false) {
                // Preserve existing sections
                $guideData['sections'] = $guides[$existingIndex]['sections'] ?? [];
                $guides[$existingIndex] = $guideData;
                $message = 'Guide updated successfully!';
            } else {
                $guides[] = $guideData;
                $message = 'Guide added successfully!';
            }
            
            $guidesData['guides'] = $guides;
            file_put_contents($guidesFile, json_encode($guidesData, JSON_PRETTY_PRINT));
            $messageType = 'success';
        }
    }
    
    // Delete Guide
    if (isset($_POST['delete_guide'])) {
        $guideId = sanitize($_POST['guide_id'] ?? '');
        $guides = array_filter($guides, fn($g) => $g['id'] !== $guideId);
        $guides = array_values($guides);
        $guidesData['guides'] = $guides;
        file_put_contents($guidesFile, json_encode($guidesData, JSON_PRETTY_PRINT));
        $message = 'Guide deleted successfully!';
        $messageType = 'success';
    }
    
    // Save Section
    if (isset($_POST['save_section'])) {
        $guideId = sanitize($_POST['guide_id'] ?? '');
        $sectionIndex = isset($_POST['section_index']) ? (int)$_POST['section_index'] : -1;
        $sectionTitle = sanitize($_POST['section_title'] ?? '');
        $sectionColors = sanitize($_POST['section_colors'] ?? '');
        $sectionContent = $_POST['section_content'] ?? '';
        $sectionTip = sanitize($_POST['section_tip'] ?? '');
        
        // Find guide
        foreach ($guides as $idx => $guide) {
            if ($guide['id'] === $guideId) {
                $colorsArray = array_map('trim', explode(',', $sectionColors));
                
                // Handle image upload
                $sectionImage = '';
                if (isset($_FILES['section_image']) && $_FILES['section_image']['error'] === UPLOAD_ERR_OK) {
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    $fileType = $_FILES['section_image']['type'];
                    
                    if (in_array($fileType, $allowedTypes)) {
                        $extension = pathinfo($_FILES['section_image']['name'], PATHINFO_EXTENSION);
                        $newFilename = $guideId . '-section-' . time() . '.' . $extension;
                        $targetPath = $uploadDir . $newFilename;
                        
                        if (move_uploaded_file($_FILES['section_image']['tmp_name'], $targetPath)) {
                            $sectionImage = 'assets/images/guides/' . $newFilename;
                        }
                    }
                }
                
                // Preserve existing image if no new upload
                if (empty($sectionImage) && $sectionIndex >= 0 && isset($guides[$idx]['sections'][$sectionIndex]['image'])) {
                    $sectionImage = $guides[$idx]['sections'][$sectionIndex]['image'];
                }
                
                $sectionData = [
                    'title' => $sectionTitle,
                    'colors' => $colorsArray,
                    'image' => $sectionImage,
                    'content' => $sectionContent,
                    'tip' => $sectionTip
                ];
                
                if ($sectionIndex >= 0 && isset($guides[$idx]['sections'][$sectionIndex])) {
                    $guides[$idx]['sections'][$sectionIndex] = $sectionData;
                    $message = 'Section updated!';
                } else {
                    $guides[$idx]['sections'][] = $sectionData;
                    $message = 'Section added!';
                }
                
                $guides[$idx]['updated'] = date('Y-m-d');
                break;
            }
        }
        
        $guidesData['guides'] = $guides;
        file_put_contents($guidesFile, json_encode($guidesData, JSON_PRETTY_PRINT));
        $messageType = 'success';
    }
    
    // Delete Section
    if (isset($_POST['delete_section'])) {
        $guideId = sanitize($_POST['guide_id'] ?? '');
        $sectionIndex = (int)$_POST['section_index'];
        
        foreach ($guides as $idx => $guide) {
            if ($guide['id'] === $guideId) {
                array_splice($guides[$idx]['sections'], $sectionIndex, 1);
                $guides[$idx]['updated'] = date('Y-m-d');
                break;
            }
        }
        
        $guidesData['guides'] = $guides;
        file_put_contents($guidesFile, json_encode($guidesData, JSON_PRETTY_PRINT));
        $message = 'Section deleted!';
        $messageType = 'success';
    }
    
    // Reload data
    $guidesData = json_decode(file_get_contents($guidesFile), true);
    $guides = $guidesData['guides'] ?? [];
}

// Get guide for editing
$editGuide = null;
if (isset($_GET['edit'])) {
    $editId = $_GET['edit'];
    foreach ($guides as $g) {
        if ($g['id'] === $editId) {
            $editGuide = $g;
            break;
        }
    }
}

// Get guide for section management
$manageGuide = null;
if (isset($_GET['sections'])) {
    $manageId = $_GET['sections'];
    foreach ($guides as $g) {
        if ($g['id'] === $manageId) {
            $manageGuide = $g;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> | Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #c8956c;
            --primary-dark: #a87754;
            --dark: #1a1a1a;
            --light-gray: #f5f5f5;
            --success: #22c55e;
            --error: #ef4444;
            --warning: #f59e0b;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Outfit', sans-serif;
            background: #f0f0f0;
            min-height: 100vh;
        }
        
        .admin-header {
            background: linear-gradient(135deg, #1a1a1a, #2d2d2d);
            color: #fff;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-header h1 { font-size: 1.5rem; }
        .admin-header h1 span { color: var(--primary); }
        
        .admin-nav a {
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            margin-left: 10px;
            transition: background 0.3s;
        }
        
        .admin-nav a:hover { background: rgba(255,255,255,0.1); }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-secondary { background: #666; color: #fff; }
        .btn-danger { background: var(--error); color: #fff; }
        .btn-success { background: var(--success); color: #fff; }
        .btn-sm { padding: 8px 16px; font-size: 0.9rem; }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-success { background: #dcfce7; color: #166534; }
        .alert-error { background: #fee2e2; color: #991b1b; }
        
        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 25px;
        }
        
        .card-header {
            background: var(--light-gray);
            padding: 20px 25px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-header h3 { font-size: 1.2rem; }
        
        .card-body { padding: 25px; }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e5e5;
            border-radius: 8px;
            font-size: 1rem;
            font-family: inherit;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .form-row-3 {
            grid-template-columns: repeat(3, 1fr);
        }
        
        .checkbox-group {
            display: flex;
            gap: 25px;
        }
        
        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .checkbox-item input[type="checkbox"] {
            width: 20px;
            height: 20px;
            accent-color: var(--primary);
        }
        
        /* Guides Table */
        .guides-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .guides-table th,
        .guides-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .guides-table th {
            background: var(--light-gray);
            font-weight: 600;
        }
        
        .guides-table tr:hover {
            background: #fafafa;
        }
        
        .category-badge {
            display: inline-block;
            padding: 5px 12px;
            background: rgba(200,149,108,0.15);
            color: var(--primary);
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-enabled { background: #dcfce7; color: #166534; }
        .status-disabled { background: #fee2e2; color: #991b1b; }
        
        .actions {
            display: flex;
            gap: 10px;
        }
        
        /* Sections List */
        .sections-list {
            list-style: none;
        }
        
        .section-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background: var(--light-gray);
            border-radius: 10px;
            margin-bottom: 10px;
        }
        
        .section-item h4 {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .color-dots {
            display: flex;
            gap: 5px;
        }
        
        .color-dot {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        /* Image Upload Box */
        .image-upload-box {
            border: 2px dashed #ccc;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: #fafafa;
            min-height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        .image-upload-box:hover {
            border-color: var(--primary);
            background: rgba(200, 149, 108, 0.05);
        }
        
        .image-upload-box input[type="file"] {
            display: none;
        }
        
        .upload-placeholder {
            color: #888;
        }
        
        .upload-placeholder i {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 10px;
        }
        
        .upload-placeholder p {
            margin: 0 0 5px;
            font-weight: 500;
        }
        
        .upload-placeholder small {
            color: #aaa;
        }
        
        .image-preview {
            max-width: 100%;
            max-height: 200px;
            border-radius: 8px;
            object-fit: contain;
        }
        
        /* Section thumbnail */
        .section-thumbnail {
            width: 60px;
            height: 40px;
            object-fit: cover;
            border-radius: 6px;
            margin-right: 10px;
            border: 2px solid #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.15);
        }
        
        .section-no-image {
            width: 60px;
            height: 40px;
            background: var(--light-gray);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
        }
        
        .section-no-image i {
            color: #ccc;
            font-size: 1.2rem;
        }
        
        @media (max-width: 768px) {
            .form-row, .form-row-3 { grid-template-columns: 1fr; }
            .container { padding: 15px; }
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <h1><i class="fas fa-book"></i> Manage <span>Guides</span></h1>
        <nav class="admin-nav">
            <a href="index.php"><i class="fas fa-arrow-left"></i> Dashboard</a>
            <a href="<?php echo SITE_URL; ?>/guides.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Guides</a>
        </nav>
    </header>
    
    <div class="container">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($manageGuide): ?>
            <!-- Manage Sections View -->
            <div class="page-header">
                <div>
                    <a href="guides.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
                    <h2 style="margin-top: 10px;">Manage Sections: <?php echo $manageGuide['title']; ?></h2>
                </div>
            </div>
            
            <!-- Add Section Form -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-plus"></i> Add New Section</h3>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="guide_id" value="<?php echo $manageGuide['id']; ?>">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Section Title</label>
                                <input type="text" name="section_title" class="form-control" placeholder="e.g., Cerulean Blue And White" required>
                            </div>
                            <div class="form-group">
                                <label>Colors (comma-separated hex codes)</label>
                                <input type="text" name="section_colors" class="form-control" placeholder="#007BA7, #FFFFFF">
                            </div>
                        </div>
                        
                        <!-- Image Upload -->
                        <div class="form-group">
                            <label>Section Image</label>
                            <div class="image-upload-box" onclick="document.getElementById('section_image').click();">
                                <input type="file" name="section_image" id="section_image" accept="image/*" onchange="previewImage(this)">
                                <div class="upload-placeholder" id="upload-placeholder">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>Click to upload image</p>
                                    <small>Recommended: 800x400px (JPG, PNG, WebP)</small>
                                </div>
                                <img id="image-preview" class="image-preview" style="display: none;">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Content</label>
                            <textarea name="section_content" class="form-control" rows="4" placeholder="Main content for this section..."></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Pro Tip (optional)</label>
                            <input type="text" name="section_tip" class="form-control" placeholder="Expert tip for this section...">
                        </div>
                        
                        <button type="submit" name="save_section" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Section
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Existing Sections -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-list"></i> Current Sections (<?php echo count($manageGuide['sections']); ?>)</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($manageGuide['sections'])): ?>
                        <p style="color: #666; text-align: center; padding: 30px;">No sections yet. Add your first section above!</p>
                    <?php else: ?>
                        <ul class="sections-list">
                            <?php foreach ($manageGuide['sections'] as $idx => $section): ?>
                                <li class="section-item">
                                    <h4>
                                        <span style="color: #999;"><?php echo $idx + 1; ?>.</span>
                                        <?php if (!empty($section['image'])): ?>
                                            <img src="<?php echo SITE_URL . '/' . $section['image']; ?>" alt="" class="section-thumbnail">
                                        <?php else: ?>
                                            <div class="section-no-image"><i class="fas fa-image"></i></div>
                                        <?php endif; ?>
                                        <div class="color-dots">
                                            <?php foreach ($section['colors'] as $color): ?>
                                                <span class="color-dot" style="background: <?php echo $color; ?>;"></span>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php echo $section['title']; ?>
                                        <?php if (!empty($section['tip'])): ?>
                                            <i class="fas fa-lightbulb" style="color: var(--warning);" title="Has Pro Tip"></i>
                                        <?php endif; ?>
                                    </h4>
                                    <div class="actions">
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this section?');">
                                            <input type="hidden" name="guide_id" value="<?php echo $manageGuide['id']; ?>">
                                            <input type="hidden" name="section_index" value="<?php echo $idx; ?>">
                                            <button type="submit" name="delete_section" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
            
        <?php elseif ($editGuide || isset($_GET['add'])): ?>
            <!-- Add/Edit Guide Form -->
            <div class="page-header">
                <h2><?php echo $editGuide ? 'Edit Guide' : 'Add New Guide'; ?></h2>
                <a href="guides.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="guide_id" value="<?php echo $editGuide['id'] ?? ''; ?>">
                        
                        <div class="form-group">
                            <label>Guide Title</label>
                            <input type="text" name="title" class="form-control" 
                                value="<?php echo $editGuide['title'] ?? ''; ?>" 
                                placeholder="e.g., A Guide To Kids Room Colour Combinations" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>URL Slug</label>
                                <input type="text" name="slug" class="form-control" 
                                    value="<?php echo $editGuide['slug'] ?? ''; ?>" 
                                    placeholder="e.g., kids-room-colours" required>
                            </div>
                            <div class="form-group">
                                <label>Category</label>
                                <select name="category" class="form-control" required>
                                    <option value="">Select Category</option>
                                    <?php 
                                    // Group categories
                                    $groupedCategories = [];
                                    foreach ($categories as $cat) {
                                        $group = $cat['group'] ?? 'Other';
                                        if (!isset($groupedCategories[$group])) {
                                            $groupedCategories[$group] = [];
                                        }
                                        $groupedCategories[$group][] = $cat;
                                    }
                                    
                                    foreach ($groupedCategories as $group => $cats): ?>
                                        <optgroup label="<?php echo $group; ?>">
                                            <?php foreach ($cats as $cat): ?>
                                                <option value="<?php echo $cat['id']; ?>" 
                                                    <?php echo ($editGuide['category'] ?? '') === $cat['id'] ? 'selected' : ''; ?>>
                                                    <?php echo $cat['name']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3" 
                                placeholder="Brief description for the guide card..."><?php echo $editGuide['description'] ?? ''; ?></textarea>
                        </div>
                        
                        <!-- Thumbnail Upload -->
                        <div class="form-group">
                            <label>Guide Thumbnail (shown in listing)</label>
                            <div class="form-row">
                                <div>
                                    <div class="image-upload-box" onclick="document.getElementById('thumbnail').click();" style="min-height: 120px;">
                                        <input type="file" name="thumbnail" id="thumbnail" accept="image/*" onchange="previewThumbnail(this)">
                                        <?php if (!empty($editGuide['thumbnail'])): ?>
                                            <img id="thumb-preview" src="<?php echo SITE_URL . '/' . $editGuide['thumbnail']; ?>" class="image-preview" style="max-height: 150px;">
                                            <div class="upload-placeholder" id="thumb-placeholder" style="display: none;">
                                                <i class="fas fa-cloud-upload-alt"></i>
                                                <p>Click to change thumbnail</p>
                                                <small>400x300px (JPG, PNG, WebP)</small>
                                            </div>
                                        <?php else: ?>
                                            <img id="thumb-preview" class="image-preview" style="display: none; max-height: 150px;">
                                            <div class="upload-placeholder" id="thumb-placeholder">
                                                <i class="fas fa-cloud-upload-alt"></i>
                                                <p>Click to upload thumbnail</p>
                                                <small>400x300px (JPG, PNG, WebP)</small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; color: #666;">
                                    <i class="fas fa-info-circle" style="margin-right: 10px;"></i>
                                    <small>This image appears as the preview card on the guides listing page.</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Read Time</label>
                                <input type="text" name="read_time" class="form-control" 
                                    value="<?php echo $editGuide['read_time'] ?? '5 min'; ?>" 
                                    placeholder="e.g., 8 min">
                            </div>
                            <div class="form-group">
                                <label>Options</label>
                                <div class="checkbox-group" style="margin-top: 10px;">
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="enabled" 
                                            <?php echo ($editGuide['enabled'] ?? true) ? 'checked' : ''; ?>>
                                        <span>Enabled</span>
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="featured" 
                                            <?php echo ($editGuide['featured'] ?? false) ? 'checked' : ''; ?>>
                                        <span>Featured</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" name="save_guide" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?php echo $editGuide ? 'Update Guide' : 'Add Guide'; ?>
                        </button>
                    </form>
                </div>
            </div>
            
        <?php else: ?>
            <!-- Guides List View -->
            <div class="page-header">
                <h2>All Guides (<?php echo count($guides); ?>)</h2>
                <a href="?add=1" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Guide</a>
            </div>
            
            <div class="card">
                <div class="card-body" style="padding: 0;">
                    <table class="guides-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Sections</th>
                                <th>Status</th>
                                <th>Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($guides)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 40px; color: #666;">
                                        No guides yet. Click "Add New Guide" to create your first guide.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($guides as $guide): ?>
                                    <?php 
                                    $catName = '';
                                    foreach ($categories as $c) {
                                        if ($c['id'] === $guide['category']) {
                                            $catName = $c['name'];
                                            break;
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo $guide['title']; ?></strong>
                                            <?php if ($guide['featured']): ?>
                                                <i class="fas fa-star" style="color: var(--warning); margin-left: 5px;" title="Featured"></i>
                                            <?php endif; ?>
                                            <br>
                                            <small style="color: #666;">/guides/<?php echo $guide['slug']; ?>.php</small>
                                        </td>
                                        <td><span class="category-badge"><?php echo $catName; ?></span></td>
                                        <td><?php echo count($guide['sections']); ?> sections</td>
                                        <td>
                                            <span class="status-badge <?php echo $guide['enabled'] ? 'status-enabled' : 'status-disabled'; ?>">
                                                <?php echo $guide['enabled'] ? 'Active' : 'Disabled'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo $guide['updated']; ?></td>
                                        <td>
                                            <div class="actions">
                                                <a href="?sections=<?php echo $guide['id']; ?>" class="btn btn-success btn-sm" title="Manage Sections">
                                                    <i class="fas fa-list"></i>
                                                </a>
                                                <a href="?edit=<?php echo $guide['id']; ?>" class="btn btn-primary btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this guide?');">
                                                    <input type="hidden" name="guide_id" value="<?php echo $guide['id']; ?>">
                                                    <button type="submit" name="delete_guide" class="btn btn-danger btn-sm" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        function previewImage(input) {
            const preview = document.getElementById('image-preview');
            const placeholder = document.getElementById('upload-placeholder');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                };
                
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        function previewThumbnail(input) {
            const preview = document.getElementById('thumb-preview');
            const placeholder = document.getElementById('thumb-placeholder');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    if (placeholder) placeholder.style.display = 'none';
                };
                
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
