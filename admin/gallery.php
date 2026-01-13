<?php
/**
 * KV Wood Works - Admin Gallery Manager
 * Upload images to categories (creates folders automatically)
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$pageTitle = 'Gallery Manager - Admin';

// Handle image upload
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
    $category = sanitize($_POST['category']);
    $newCategory = sanitize($_POST['new_category'] ?? '');

    // Use new category if provided
    if (!empty($newCategory)) {
        $category = strtolower(str_replace([' ', '_'], '-', $newCategory));
    }

    if (empty($category)) {
        $message = 'Please select or enter a category name.';
        $messageType = 'error';
    } elseif (!isset($_FILES['images']) || empty($_FILES['images']['name'][0])) {
        $message = 'Please select at least one image to upload.';
        $messageType = 'error';
    } else {
        $galleryPath = __DIR__ . '/../assets/images/gallery/' . $category . '/';

        // Create category folder if it doesn't exist
        if (!file_exists($galleryPath)) {
            mkdir($galleryPath, 0755, true);
        }

        $uploadedCount = 0;
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        // Handle multiple file uploads
        $files = $_FILES['images'];
        $fileCount = count($files['name']);

        for ($i = 0; $i < $fileCount; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $tmpName = $files['tmp_name'][$i];
                $originalName = $files['name'][$i];
                $fileType = $files['type'][$i];

                if (in_array($fileType, $allowedTypes)) {
                    // Generate unique filename
                    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
                    $newFilename = $category . '-' . time() . '-' . ($i + 1) . '.' . $extension;
                    $targetPath = $galleryPath . $newFilename;

                    if (move_uploaded_file($tmpName, $targetPath)) {
                        $uploadedCount++;
                    }
                }
            }
        }

        if ($uploadedCount > 0) {
            $message = "Successfully uploaded $uploadedCount image(s) to '" . ucwords(str_replace('-', ' ', $category)) . "' category!";
            $messageType = 'success';
        } else {
            $message = 'Failed to upload images. Please check file types (JPG, PNG, GIF, WebP only).';
            $messageType = 'error';
        }
    }
}

// Handle image deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $imagePath = __DIR__ . '/../' . sanitize($_POST['image_path']);
    $category = isset($_POST['category']) ? sanitize($_POST['category']) : '';

    if (file_exists($imagePath) && strpos($imagePath, 'gallery') !== false) {
        if (unlink($imagePath)) {
            $message = 'Image deleted successfully!';
            $messageType = 'success';
            // Redirect back to same category
            if ($category) {
                header("Location: gallery.php?manage=" . urlencode($category) . "&deleted=1");
                exit;
            }
        } else {
            $message = 'Failed to delete image.';
            $messageType = 'error';
        }
    } else {
        $message = 'Image file not found.';
        $messageType = 'error';
    }
}

// Get existing categories
$galleryBasePath = __DIR__ . '/../assets/images/gallery/';
$existingCategories = [];

if (!file_exists($galleryBasePath)) {
    mkdir($galleryBasePath, 0755, true);
}

if (is_dir($galleryBasePath)) {
    $folders = scandir($galleryBasePath);
    foreach ($folders as $folder) {
        if ($folder !== '.' && $folder !== '..' && is_dir($galleryBasePath . $folder)) {
            $existingCategories[] = $folder;
        }
    }
}

// Get images from selected category for management
$selectedCategory = isset($_GET['manage']) ? sanitize($_GET['manage']) : '';
$categoryImages = [];

if ($selectedCategory && is_dir($galleryBasePath . $selectedCategory)) {
    $images = glob($galleryBasePath . $selectedCategory . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
    foreach ($images as $image) {
        $categoryImages[] = [
            'path' => 'assets/images/gallery/' . $selectedCategory . '/' . basename($image),
            'name' => basename($image)
        ];
    }
}

// Check for deleted success message
if (isset($_GET['deleted'])) {
    $message = 'Image deleted successfully!';
    $messageType = 'success';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $pageTitle; ?>
    </title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #c8956c;
            --accent: #2d5a4a;
            --dark: #1a1a1a;
            --light-gray: #f5f5f5;
            --gray: #666;
            --white: #fff;
            --success: #22c55e;
            --error: #ef4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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

        .admin-header h1 {
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-header a {
            color: var(--white);
            text-decoration: none;
            padding: 8px 16px;
            background: var(--primary);
            border-radius: 6px;
            transition: opacity 0.3s;
        }

        .admin-header a:hover {
            opacity: 0.9;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .card {
            background: var(--white);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .card h2 {
            margin-bottom: 20px;
            color: var(--dark);
            font-size: 1.3rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
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

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-primary {
            background: var(--primary);
            color: var(--white);
        }

        .btn-primary:hover {
            background: #b8854c;
        }

        .btn-danger {
            background: var(--error);
            color: var(--white);
            padding: 8px 12px;
            font-size: 0.85rem;
        }

        .categories-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .category-item {
            background: var(--light-gray);
            padding: 15px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .category-item a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .image-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            aspect-ratio: 1;
        }

        .image-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .image-item .delete-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background: var(--error);
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .image-item:hover .delete-btn {
            opacity: 1;
        }

        .file-input-wrapper {
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 40px 20px;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.3s;
        }

        .file-input-wrapper:hover {
            border-color: var(--primary);
        }

        .file-input-wrapper input {
            display: none;
        }

        .file-input-wrapper i {
            font-size: 3rem;
            color: var(--gray);
            margin-bottom: 15px;
        }

        .or-divider {
            text-align: center;
            color: var(--gray);
            margin: 10px 0;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <header class="admin-header">
        <h1><i class="fas fa-images"></i> Gallery Manager</h1>
        <div>
            <a href="<?php echo SITE_URL; ?>/gallery.php"><i class="fas fa-eye"></i> View Gallery</a>
            <a href="<?php echo SITE_URL; ?>" style="margin-left: 10px;"><i class="fas fa-home"></i> Back to Site</a>
        </div>
    </header>

    <div class="container">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Upload Form -->
        <div class="card">
            <h2><i class="fas fa-upload"></i> Upload New Images</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label>Select Existing Category</label>
                        <select name="category" class="form-control" id="categorySelect">
                            <option value="">-- Select Category --</option>
                            <?php foreach ($existingCategories as $cat): ?>
                                <option value="<?php echo $cat; ?>">
                                    <?php echo ucwords(str_replace('-', ' ', $cat)); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Or Create New Category</label>
                        <input type="text" name="new_category" class="form-control"
                            placeholder="e.g., TV Unit, Shoe Rack, False Ceiling" id="newCategory">
                        <small style="color: var(--gray);">Leave blank to use selected category above</small>
                    </div>
                </div>

                <div class="form-group">
                    <label>Upload Images</label>
                    <label class="file-input-wrapper" id="dropZone">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p><strong>Click to upload</strong> or drag and drop</p>
                        <p style="color: var(--gray); font-size: 0.9rem;">JPG, PNG, GIF, WebP (Max 10MB each)</p>
                        <input type="file" name="images[]" id="imageInput" multiple accept="image/*">
                    </label>
                    <div id="fileList" style="margin-top: 10px;"></div>
                </div>

                <button type="submit" name="upload" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Upload Images
                </button>
            </form>
        </div>

        <!-- Existing Categories -->
        <div class="card">
            <h2><i class="fas fa-folder-open"></i> Existing Categories</h2>

            <?php if (empty($existingCategories)): ?>
                <p style="color: var(--gray);">No categories yet. Upload images to create categories automatically.</p>
            <?php else: ?>
                <div class="categories-list">
                    <?php foreach ($existingCategories as $cat):
                        $images = glob($galleryBasePath . $cat . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
                        $count = count($images);
                        ?>
                        <div class="category-item">
                            <div>
                                <strong>
                                    <?php echo ucwords(str_replace('-', ' ', $cat)); ?>
                                </strong>
                                <small style="color: var(--gray); display: block;">
                                    <?php echo $count; ?> images
                                </small>
                            </div>
                            <a href="?manage=<?php echo $cat; ?>">Manage <i class="fas fa-arrow-right"></i></a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($selectedCategory && !empty($categoryImages)): ?>
            <!-- Manage Category Images -->
            <div class="card">
                <h2>
                    <i class="fas fa-images"></i>
                    <?php echo ucwords(str_replace('-', ' ', $selectedCategory)); ?>
                    (
                    <?php echo count($categoryImages); ?> images)
                </h2>
                <a href="?" style="color: var(--primary); text-decoration: none; margin-bottom: 20px; display: block;">
                    <i class="fas fa-arrow-left"></i> Back to Upload
                </a>

                <div class="image-grid">
                    <?php foreach ($categoryImages as $image): ?>
                        <div class="image-item">
                            <img src="<?php echo SITE_URL . '/' . $image['path']; ?>" alt="">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="image_path" value="<?php echo $image['path']; ?>">
                                <input type="hidden" name="category" value="<?php echo $selectedCategory; ?>">
                                <button type="submit" name="delete" class="delete-btn"
                                    onclick="return confirm('Delete this image?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // File input preview
        document.getElementById('imageInput').addEventListener('change', function (e) {
            const fileList = document.getElementById('fileList');
            fileList.innerHTML = '';

            if (this.files.length > 0) {
                const list = document.createElement('ul');
                list.style.cssText = 'list-style: none; padding: 10px; background: #f0f0f0; border-radius: 8px;';

                for (let file of this.files) {
                    const li = document.createElement('li');
                    li.innerHTML = `<i class="fas fa-image" style="color: var(--primary);"></i> ${file.name}`;
                    li.style.padding = '5px 0';
                    list.appendChild(li);
                }

                fileList.appendChild(list);
            }
        });

        // Clear new category if existing is selected
        document.getElementById('categorySelect').addEventListener('change', function () {
            if (this.value) {
                document.getElementById('newCategory').value = '';
            }
        });
    </script>
</body>

</html>