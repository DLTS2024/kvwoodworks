<?php
/**
 * KV Wood Works - Admin Blogs Manager
 * Add/Update blog posts with text content, photos, and ad banners
 */

 require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$pageTitle = 'Update Blogs';

// Blogs file path
$blogsFile = __DIR__ . '/../config/blogs.json';

// Load existing blogs
$blogs = [];
if (file_exists($blogsFile)) {
    $blogs = json_decode(file_get_contents($blogsFile), true) ?? [];
}

// Available tags
$availableTags = ['Kitchen', 'Bedroom', 'Living Room', 'Wardrobe', 'Wooden Works', 'Interior Tips', 'Trends', 'DIY', 'Budget'];

// Handle form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add/Edit blog
    if (isset($_POST['save_blog'])) {
        $blogId = isset($_POST['blog_id']) && !empty($_POST['blog_id']) ? sanitize($_POST['blog_id']) : 'blog_' . time();
        $title = sanitize($_POST['title']);
        $tags = isset($_POST['tags']) ? $_POST['tags'] : [];
        
        // Content blocks (text and photos alternating)
        $contentBlocks = [];
        
        // First text content
        if (!empty($_POST['content_1'])) {
            $contentBlocks[] = ['type' => 'text', 'content' => $_POST['content_1']];
        }
        
        // Handle first photo
        if (isset($_FILES['photo_1']) && $_FILES['photo_1']['error'] === UPLOAD_ERR_OK) {
            $photo = uploadBlogImage($_FILES['photo_1'], $blogId, 1);
            if ($photo) $contentBlocks[] = ['type' => 'image', 'content' => $photo];
        } elseif (!empty($_POST['existing_photo_1'])) {
            $contentBlocks[] = ['type' => 'image', 'content' => $_POST['existing_photo_1']];
        }
        
        // Second text content
        if (!empty($_POST['content_2'])) {
            $contentBlocks[] = ['type' => 'text', 'content' => $_POST['content_2']];
        }
        
        // Handle second photo
        if (isset($_FILES['photo_2']) && $_FILES['photo_2']['error'] === UPLOAD_ERR_OK) {
            $photo = uploadBlogImage($_FILES['photo_2'], $blogId, 2);
            if ($photo) $contentBlocks[] = ['type' => 'image', 'content' => $photo];
        } elseif (!empty($_POST['existing_photo_2'])) {
            $contentBlocks[] = ['type' => 'image', 'content' => $_POST['existing_photo_2']];
        }
        
        // Third text content
        if (!empty($_POST['content_3'])) {
            $contentBlocks[] = ['type' => 'text', 'content' => $_POST['content_3']];
        }
        
        // Show ad banner between content
        $showAdBanner = isset($_POST['show_ad_banner']);
        
        if (empty($title)) {
            $message = 'Please enter a blog title.';
            $messageType = 'error';
        } else {
            $blogs[$blogId] = [
                'title' => $title,
                'tags' => $tags,
                'content_blocks' => $contentBlocks,
                'show_ad_banner' => $showAdBanner,
                'slug' => strtolower(preg_replace('/[^a-z0-9]+/', '-', $title)),
                'updated_at' => date('Y-m-d H:i:s'),
                'created_at' => isset($blogs[$blogId]['created_at']) ? $blogs[$blogId]['created_at'] : date('Y-m-d H:i:s')
            ];
            
            if (file_put_contents($blogsFile, json_encode($blogs, JSON_PRETTY_PRINT))) {
                $message = 'Blog saved successfully!';
                $messageType = 'success';
            } else {
                $message = 'Failed to save blog.';
                $messageType = 'error';
            }
        }
    }
    
    // Delete blog
    if (isset($_POST['delete_blog'])) {
        $blogId = sanitize($_POST['blog_id']);
        if (isset($blogs[$blogId])) {
            unset($blogs[$blogId]);
            file_put_contents($blogsFile, json_encode($blogs, JSON_PRETTY_PRINT));
            $message = 'Blog deleted successfully!';
            $messageType = 'success';
        }
    }
}

function uploadBlogImage($file, $blogId, $index) {
    $uploadDir = __DIR__ . '/../assets/images/blog/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (in_array($file['type'], $allowedTypes)) {
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFilename = $blogId . '-' . $index . '.' . $extension;
        $targetPath = $uploadDir . $newFilename;
        
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return 'assets/images/blog/' . $newFilename;
        }
    }
    return null;
}

// Get blog for editing
$editBlog = null;
$editBlogId = '';
if (isset($_GET['edit'])) {
    $editBlogId = sanitize($_GET['edit']);
    $editBlog = isset($blogs[$editBlogId]) ? $blogs[$editBlogId] : null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #c8956c;
            --dark: #1a1a1a;
            --light-gray: #f5f5f5;
            --white: #fff;
            --success: #22c55e;
            --error: #ef4444;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Outfit', sans-serif;
            background: var(--light-gray);
            min-height: 100vh;
        }
        
        .admin-header {
            background: var(--dark);
            color: var(--white);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-header h1 { font-size: 1.3rem; }
        
        .admin-header a {
            color: var(--white);
            text-decoration: none;
            padding: 8px 16px;
            background: var(--primary);
            border-radius: 6px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 30px;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-success { background: #dcfce7; color: #166534; }
        .alert-error { background: #fee2e2; color: #991b1b; }
        
        .card {
            background: var(--white);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .card h2 {
            margin-bottom: 20px;
            color: var(--dark);
            font-size: 1.2rem;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e5e5e5;
            border-radius: 8px;
            font-size: 1rem;
            font-family: inherit;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }
        
        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }
        
        .tags-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .tag-option {
            cursor: pointer;
        }
        
        .tag-option input { display: none; }
        
        .tag-option span {
            display: inline-block;
            padding: 6px 12px;
            border: 2px solid #e5e5e5;
            border-radius: 20px;
            font-size: 0.85rem;
            transition: all 0.3s;
        }
        
        .tag-option input:checked + span {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        .btn {
            padding: 14px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary { background: var(--primary); color: var(--white); }
        .btn-secondary { background: #e5e5e5; color: var(--dark); }
        .btn-danger { background: var(--error); color: var(--white); padding: 8px 15px; font-size: 0.85rem; }
        
        .content-block {
            background: var(--light-gray);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
        }
        
        .content-block h4 {
            margin-bottom: 15px;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .content-block h4 i { color: var(--primary); }
        
        .photo-upload-inline {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .photo-upload-inline input[type="file"] {
            flex: 1;
        }
        
        .photo-preview {
            width: 80px;
            height: 60px;
            border-radius: 6px;
            object-fit: cover;
        }
        
        .ad-banner-toggle {
            display: flex;
            align-items: center;
            gap: 15px;
            background: #fff3cd;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #ffc107;
        }
        
        .ad-banner-toggle input {
            width: 20px;
            height: 20px;
            accent-color: var(--primary);
        }
        
        /* Blog list */
        .blog-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .blog-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--light-gray);
            padding: 15px 20px;
            border-radius: 10px;
        }
        
        .blog-item-info h4 { margin-bottom: 5px; }
        .blog-item-info p { color: #666; font-size: 0.85rem; margin: 0; }
        
        .blog-item-actions {
            display: flex;
            gap: 10px;
        }
        
        @media (max-width: 768px) {
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <h1><i class="fas fa-blog"></i> Update Blogs</h1>
        <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </header>
    
    <div class="container">
        <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
            <?php echo $message; ?>
        </div>
        <?php endif; ?>
        
        <!-- Blog Form -->
        <div class="card">
            <h2><i class="fas fa-<?php echo $editBlog ? 'edit' : 'plus-circle'; ?>"></i> <?php echo $editBlog ? 'Edit Blog Post' : 'Add New Blog Post'; ?></h2>
            
            <?php if ($editBlog): ?>
            <a href="blogs.php" class="btn btn-secondary" style="margin-bottom: 20px;">
                <i class="fas fa-plus"></i> Add New Blog Instead
            </a>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="blog_id" value="<?php echo $editBlogId; ?>">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Title / Heading</label>
                        <input type="text" name="title" class="form-control" 
                               value="<?php echo $editBlog ? $editBlog['title'] : ''; ?>"
                               placeholder="e.g., 10 Modern Kitchen Design Ideas" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Tags</label>
                        <div class="tags-grid">
                            <?php foreach ($availableTags as $tag): ?>
                            <label class="tag-option">
                                <input type="checkbox" name="tags[]" value="<?php echo $tag; ?>"
                                    <?php echo ($editBlog && in_array($tag, $editBlog['tags'])) ? 'checked' : ''; ?>>
                                <span><?php echo $tag; ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Content Block 1: Text -->
                <div class="content-block">
                    <h4><i class="fas fa-paragraph"></i> Text Content 1</h4>
                    <textarea name="content_1" class="form-control" placeholder="Write your first paragraph here..."><?php 
                        echo $editBlog && isset($editBlog['content_blocks'][0]) && $editBlog['content_blocks'][0]['type'] === 'text' 
                            ? $editBlog['content_blocks'][0]['content'] : ''; 
                    ?></textarea>
                </div>
                
                <!-- Photo 1 -->
                <div class="content-block">
                    <h4><i class="fas fa-image"></i> Photo 1</h4>
                    <div class="photo-upload-inline">
                        <input type="file" name="photo_1" class="form-control" accept="image/*">
                        <?php 
                        $existingPhoto1 = '';
                        if ($editBlog) {
                            foreach ($editBlog['content_blocks'] as $block) {
                                if ($block['type'] === 'image' && empty($existingPhoto1)) {
                                    $existingPhoto1 = $block['content'];
                                    break;
                                }
                            }
                        }
                        if ($existingPhoto1): ?>
                        <img src="<?php echo SITE_URL . '/' . $existingPhoto1; ?>" class="photo-preview" alt="">
                        <input type="hidden" name="existing_photo_1" value="<?php echo $existingPhoto1; ?>">
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Content Block 2: Text -->
                <div class="content-block">
                    <h4><i class="fas fa-paragraph"></i> Text Content 2</h4>
                    <textarea name="content_2" class="form-control" placeholder="Write your second paragraph here..."><?php 
                        $textCount = 0;
                        if ($editBlog) {
                            foreach ($editBlog['content_blocks'] as $block) {
                                if ($block['type'] === 'text') {
                                    $textCount++;
                                    if ($textCount === 2) {
                                        echo $block['content'];
                                        break;
                                    }
                                }
                            }
                        }
                    ?></textarea>
                </div>
                
                <!-- Photo 2 -->
                <div class="content-block">
                    <h4><i class="fas fa-image"></i> Photo 2</h4>
                    <div class="photo-upload-inline">
                        <input type="file" name="photo_2" class="form-control" accept="image/*">
                        <?php 
                        $existingPhoto2 = '';
                        $photoCount = 0;
                        if ($editBlog) {
                            foreach ($editBlog['content_blocks'] as $block) {
                                if ($block['type'] === 'image') {
                                    $photoCount++;
                                    if ($photoCount === 2) {
                                        $existingPhoto2 = $block['content'];
                                        break;
                                    }
                                }
                            }
                        }
                        if ($existingPhoto2): ?>
                        <img src="<?php echo SITE_URL . '/' . $existingPhoto2; ?>" class="photo-preview" alt="">
                        <input type="hidden" name="existing_photo_2" value="<?php echo $existingPhoto2; ?>">
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Content Block 3: Text -->
                <div class="content-block">
                    <h4><i class="fas fa-paragraph"></i> Text Content 3</h4>
                    <textarea name="content_3" class="form-control" placeholder="Write your third paragraph here..."><?php 
                        $textCount = 0;
                        if ($editBlog) {
                            foreach ($editBlog['content_blocks'] as $block) {
                                if ($block['type'] === 'text') {
                                    $textCount++;
                                    if ($textCount === 3) {
                                        echo $block['content'];
                                        break;
                                    }
                                }
                            }
                        }
                    ?></textarea>
                </div>
                
                <!-- Ad Banner Toggle -->
                <div class="form-group">
                    <label class="ad-banner-toggle">
                        <input type="checkbox" name="show_ad_banner" 
                            <?php echo ($editBlog && isset($editBlog['show_ad_banner']) && $editBlog['show_ad_banner']) ? 'checked' : ''; ?>>
                        <div>
                            <strong>Show Ad Banner in Between</strong>
                            <p style="margin: 0; font-size: 0.85rem; color: #666;">Place promotional offer banners between text content (configured in Update Offers)</p>
                        </div>
                    </label>
                </div>
                
                <button type="submit" name="save_blog" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo $editBlog ? 'Update Blog' : 'Publish Blog'; ?>
                </button>
            </form>
        </div>
        
        <!-- Existing Blogs -->
        <div class="card">
            <h2><i class="fas fa-list"></i> Existing Blog Posts (<?php echo count($blogs); ?>)</h2>
            
            <?php if (empty($blogs)): ?>
            <p style="color: #666;">No blog posts yet. Write your first blog above!</p>
            <?php else: ?>
            <div class="blog-list">
                <?php foreach ($blogs as $id => $blog): ?>
                <div class="blog-item">
                    <div class="blog-item-info">
                        <h4><?php echo $blog['title']; ?></h4>
                        <p>
                            <i class="fas fa-tags"></i> <?php echo implode(', ', $blog['tags']); ?> |
                            <i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($blog['created_at'])); ?>
                        </p>
                    </div>
                    <div class="blog-item-actions">
                        <a href="?edit=<?php echo $id; ?>" class="btn btn-secondary" style="padding: 8px 15px; font-size: 0.85rem;">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="blog_id" value="<?php echo $id; ?>">
                            <button type="submit" name="delete_blog" class="btn btn-danger" onclick="return confirm('Delete this blog?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
