<?php
/**
 * KV Wood Works - Admin Home Interiors Manager
 * Add/Edit design entries with multiple images, description, and features
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$pageTitle = 'Update Home Interiors';

// Get all interior design page categories
$interiorPages = [
    'modular-kitchen' => 'Modular Kitchen',
    'wardrobe' => 'Wardrobe Designs',
    'bedroom' => 'Bedroom',
    'kid-bedroom' => 'Kid Bedroom',
    'living-room' => 'Living Room',
    'pooja-room' => 'Pooja Room',
    'tv-unit' => 'TV Unit',
    'crockery-unit' => 'Crockery Unit',
    'shoe-rack' => 'Shoe Rack',
    'study-table' => 'Study Table',
    'false-ceiling' => 'False Ceiling',
    'space-saving' => 'Space Saving'
];

// Content file path
$contentFile = __DIR__ . '/../config/interior_designs.json';

// Load existing content
$allDesigns = [];
if (file_exists($contentFile)) {
    $allDesigns = json_decode(file_get_contents($contentFile), true) ?? [];
}

// Handle form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add or Edit design
    if (isset($_POST['save_design'])) {
        $category = sanitize($_POST['category']);
        $designTitle = sanitize($_POST['design_title']);
        $description = $_POST['description'];
        $features = sanitize($_POST['features']);
        $editId = isset($_POST['edit_id']) ? sanitize($_POST['edit_id']) : '';

        if (empty($category) || empty($designTitle)) {
            $message = 'Please enter a design title.';
            $messageType = 'error';
        } else {
            // Handle image uploads
            $newImages = [];
            $uploadDir = __DIR__ . '/../assets/images/designs/' . $category . '/';

            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Process uploaded images
            if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $totalFiles = count($_FILES['images']['name']);

                for ($i = 0; $i < $totalFiles; $i++) {
                    if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                        $tmpName = $_FILES['images']['tmp_name'][$i];
                        $fileType = $_FILES['images']['type'][$i];

                        if (in_array($fileType, $allowedTypes)) {
                            $extension = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION);
                            $newFilename = time() . '-' . uniqid() . '-' . $i . '.' . $extension;
                            $targetPath = $uploadDir . $newFilename;

                            if (move_uploaded_file($tmpName, $targetPath)) {
                                $newImages[] = 'assets/images/designs/' . $category . '/' . $newFilename;
                            }
                        }
                    }
                }
            }

            // Get existing images if editing
            $existingImages = [];
            $existingDesign = null;
            if ($editId) {
                // Look for the design in the category
                if (isset($allDesigns[$category]) && isset($allDesigns[$category][$editId])) {
                    $existingDesign = $allDesigns[$category][$editId];
                    $existingImages = $existingDesign['images'] ?? [];
                }
            }

            // Merge images - new ones add to existing
            $allImages = array_merge($existingImages, $newImages);

            // If no images at all and not editing, show error
            if (empty($allImages) && !$editId) {
                $message = 'Please upload at least one image.';
                $messageType = 'error';
            } elseif (empty($allImages) && $editId && empty($existingImages)) {
                $message = 'Please upload at least one image or keep existing images.';
                $messageType = 'error';
            } else {
                // Create or update design
                $designId = $editId ?: 'design_' . time() . '_' . uniqid();

                if (!isset($allDesigns[$category])) {
                    $allDesigns[$category] = [];
                }

                $allDesigns[$category][$designId] = [
                    'title' => $designTitle,
                    'images' => $allImages,
                    'description' => $description,
                    'features' => $features,
                    'created_at' => ($existingDesign && isset($existingDesign['created_at']))
                        ? $existingDesign['created_at']
                        : date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                if (file_put_contents($contentFile, json_encode($allDesigns, JSON_PRETTY_PRINT))) {
                    $message = $editId ? 'Design updated successfully!' : 'Design "' . $designTitle . '" added successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Failed to save design.';
                    $messageType = 'error';
                }
            }
        }
    }

    // Delete design
    if (isset($_POST['delete_design'])) {
        $category = sanitize($_POST['category']);
        $designId = sanitize($_POST['design_id']);

        if (isset($allDesigns[$category][$designId])) {
            // Delete images
            foreach ($allDesigns[$category][$designId]['images'] as $img) {
                $imgPath = __DIR__ . '/../' . $img;
                if (file_exists($imgPath)) {
                    unlink($imgPath);
                }
            }
            unset($allDesigns[$category][$designId]);
            file_put_contents($contentFile, json_encode($allDesigns, JSON_PRETTY_PRINT));
            $message = 'Design deleted successfully!';
            $messageType = 'success';
        }
    }

    // Delete single image from design
    if (isset($_POST['delete_image'])) {
        $category = sanitize($_POST['category']);
        $designId = sanitize($_POST['design_id']);
        $imageIndex = intval($_POST['image_index']);

        if (isset($allDesigns[$category][$designId]['images'][$imageIndex])) {
            $imgPath = __DIR__ . '/../' . $allDesigns[$category][$designId]['images'][$imageIndex];
            if (file_exists($imgPath)) {
                unlink($imgPath);
            }
            array_splice($allDesigns[$category][$designId]['images'], $imageIndex, 1);
            file_put_contents($contentFile, json_encode($allDesigns, JSON_PRETTY_PRINT));

            // Redirect back to edit page
            header("Location: home-interiors.php?category=" . urlencode($category) . "&edit=" . urlencode($designId) . "&deleted=1");
            exit;
        }
    }
}

// Get selected category and edit mode
$selectedCategory = isset($_GET['category']) ? sanitize($_GET['category']) : '';
$editId = isset($_GET['edit']) ? sanitize($_GET['edit']) : '';
$categoryDesigns = isset($allDesigns[$selectedCategory]) ? $allDesigns[$selectedCategory] : [];
$editDesign = ($editId && isset($categoryDesigns[$editId])) ? $categoryDesigns[$editId] : null;

// Check if redirected after image delete
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
            --info: #3b82f6;
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
            font-size: 1.3rem;
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
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
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
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 15px;
        }

        .category-btn {
            padding: 20px;
            background: var(--light-gray);
            border: 2px solid transparent;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            color: var(--dark);
        }

        .category-btn:hover {
            border-color: var(--primary);
            background: rgba(200, 149, 108, 0.1);
        }

        .category-btn i {
            font-size: 1.8rem;
            color: var(--primary);
            margin-bottom: 10px;
            display: block;
        }

        .category-btn .count {
            font-size: 0.75rem;
            margin-top: 8px;
            padding: 4px 10px;
            border-radius: 12px;
            background: var(--primary);
            color: white;
            display: inline-block;
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
            min-height: 120px;
            resize: vertical;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary);
            color: var(--white);
        }

        .btn-secondary {
            background: #e5e5e5;
            color: var(--dark);
        }

        .btn-danger {
            background: var(--error);
            color: var(--white);
            padding: 8px 15px;
            font-size: 0.85rem;
        }

        .btn-success {
            background: var(--success);
            color: var(--white);
        }

        .btn-info {
            background: var(--info);
            color: var(--white);
            padding: 8px 15px;
            font-size: 0.85rem;
        }

        .file-upload-area {
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }

        .file-upload-area:hover,
        .file-upload-area.dragover {
            border-color: var(--primary);
            background: rgba(200, 149, 108, 0.05);
        }

        .file-upload-area input {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .file-upload-area i {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 10px;
        }

        /* Image Preview */
        .image-preview-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .image-preview-item {
            position: relative;
            aspect-ratio: 4/3;
            border-radius: 8px;
            overflow: hidden;
            background: #f0f0f0;
        }

        .image-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .image-preview-item .remove-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 24px;
            height: 24px;
            background: var(--error);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .image-preview-item .new-badge {
            position: absolute;
            bottom: 5px;
            left: 5px;
            background: var(--success);
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.7rem;
        }

        /* Design Cards */
        .designs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .design-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .design-card-image {
            height: 180px;
            position: relative;
        }

        .design-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .design-card-image .image-count {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
        }

        .design-card-content {
            padding: 15px;
        }

        .design-card-content h4 {
            margin-bottom: 10px;
            font-size: 1rem;
        }

        .design-card-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {
            .categories-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .designs-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <header class="admin-header">
        <h1><i class="fas fa-couch"></i> Update Home Interiors</h1>
        <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </header>

    <div class="container">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if (!$selectedCategory): ?>
            <!-- Category Selection -->
            <div class="card">
                <h2><i class="fas fa-th-large"></i> Select a Category to Manage Designs</h2>
                <div class="categories-grid">
                    <?php foreach ($interiorPages as $slug => $name):
                        $designCount = isset($allDesigns[$slug]) ? count($allDesigns[$slug]) : 0;
                        ?>
                        <a href="?category=<?php echo $slug; ?>" class="category-btn">
                            <i class="fas fa-<?php
                            echo match ($slug) {
                                'modular-kitchen' => 'utensils',
                                'wardrobe' => 'door-closed',
                                'bedroom' => 'bed',
                                'kid-bedroom' => 'child',
                                'living-room' => 'couch',
                                'pooja-room' => 'pray',
                                'tv-unit' => 'tv',
                                'crockery-unit' => 'wine-glass-alt',
                                'shoe-rack' => 'shoe-prints',
                                'study-table' => 'book-open',
                                'false-ceiling' => 'lightbulb',
                                'space-saving' => 'expand-arrows-alt',
                                default => 'home'
                            };
                            ?>"></i>
                            <strong><?php echo $name; ?></strong>
                            <span class="count"><?php echo $designCount; ?> Designs</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php else: ?>
            <!-- Add/Edit Design Form -->
            <div class="card">
                <h2>
                    <i class="fas fa-<?php echo $editDesign ? 'edit' : 'plus-circle'; ?>"></i>
                    <?php echo $editDesign ? 'Edit Design' : 'Add New Design'; ?> -
                    <?php echo $interiorPages[$selectedCategory] ?? ucwords(str_replace('-', ' ', $selectedCategory)); ?>
                </h2>

                <div style="margin-bottom: 20px; display: flex; gap: 10px;">
                    <a href="home-interiors.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Categories
                    </a>
                    <?php if ($editDesign): ?>
                        <a href="?category=<?php echo $selectedCategory; ?>" class="btn btn-secondary">
                            <i class="fas fa-plus"></i> Add New Instead
                        </a>
                    <?php endif; ?>
                </div>

                <form method="POST" enctype="multipart/form-data" id="designForm">
                    <input type="hidden" name="save_design" value="1">
                    <input type="hidden" name="category" value="<?php echo $selectedCategory; ?>">
                    <?php if ($editId): ?>
                        <input type="hidden" name="edit_id" value="<?php echo $editId; ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Design Title *</label>
                        <input type="text" name="design_title" class="form-control"
                            value="<?php echo $editDesign['title'] ?? ''; ?>"
                            placeholder="e.g., An Eclectic Living Room With A Traditional Touch" required>
                    </div>

                    <div class="form-group">
                        <label>Upload Images (Multiple - for carousel slider) <?php echo $editDesign ? '' : '*'; ?></label>
                        <div class="file-upload-area" id="dropZone">
                            <i class="fas fa-images"></i>
                            <p><strong>Click or drag to upload multiple images</strong></p>
                            <p style="color: #666; font-size: 0.9rem;">Hold Ctrl/Cmd to select multiple files</p>
                            <input type="file" name="images[]" id="imageInput" multiple accept="image/*" <?php echo $editDesign ? '' : 'required'; ?>>
                        </div>

                        <!-- New images preview (selected but not yet uploaded) -->
                        <div class="image-preview-container" id="newImagesPreview"></div>
                    </div>

                    <div class="form-group">
                        <label>Description (shown in "Explore More" popup)</label>
                        <textarea name="description" class="form-control"
                            placeholder="This compact living room includes a perfect combination of multiple design aesthetics..."><?php echo $editDesign['description'] ?? ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Special Features (one per line - shown as bullet points)</label>
                        <textarea name="features" class="form-control" placeholder="A wall-mounted TV unit with low-lying cabinet
Stone finish back panelling for a rustic look
Multiple shelves next to the TV unit">    <?php echo $editDesign['features'] ?? ''; ?></textarea>
                    </div>

                    <button type="submit" name="save_design" class="btn btn-success">
                        <i class="fas fa-<?php echo $editDesign ? 'save' : 'plus'; ?>"></i>
                        <?php echo $editDesign ? 'Update Design' : 'Add Design'; ?>
                    </button>
                </form>

                <!-- Existing images (OUTSIDE main form to avoid nesting) -->
                <?php if ($editDesign && !empty($editDesign['images'])): ?>
                    <h4 style="margin-top: 20px; margin-bottom: 10px;">Current Images (click X to delete):</h4>
                    <div class="image-preview-container">
                        <?php foreach ($editDesign['images'] as $index => $img): ?>
                            <div class="image-preview-item" id="img-item-<?php echo $index; ?>">
                                <img src="<?php echo SITE_URL . '/' . $img; ?>" alt="">
                                <button type="button" class="remove-btn"
                                    onclick="deleteImage(<?php echo $index; ?>, '<?php echo $selectedCategory; ?>', '<?php echo $editId; ?>')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Hidden form for image deletion -->
                    <form id="deleteImageForm" method="POST" style="display: none;">
                        <input type="hidden" name="delete_image" value="1">
                        <input type="hidden" name="category" id="del_category">
                        <input type="hidden" name="design_id" id="del_design_id">
                        <input type="hidden" name="image_index" id="del_image_index">
                    </form>

                    <script>
                        function deleteImage(index, category, designId) {
                            if (confirm('Delete this image?')) {
                                document.getElementById('del_category').value = category;
                                document.getElementById('del_design_id').value = designId;
                                document.getElementById('del_image_index').value = index;
                                document.getElementById('deleteImageForm').submit();
                            }
                        }
                    </script>
                <?php endif; ?>
            </div>

            <!-- Existing Designs -->
            <div class="card">
                <h2><i class="fas fa-th-large"></i> Existing Designs (<?php echo count($categoryDesigns); ?>)</h2>

                <?php if (empty($categoryDesigns)): ?>
                    <p style="color: #666; padding: 20px; text-align: center;">
                        <i class="fas fa-info-circle"></i> No designs added yet. Add your first design above!
                    </p>
                <?php else: ?>
                    <div class="designs-grid">
                        <?php foreach ($categoryDesigns as $id => $design): ?>
                            <div class="design-card">
                                <div class="design-card-image">
                                    <?php if (!empty($design['images'])): ?>
                                        <img src="<?php echo SITE_URL . '/' . $design['images'][0]; ?>" alt="">
                                        <span class="image-count"><i class="fas fa-images"></i> <?php echo count($design['images']); ?>
                                            images</span>
                                    <?php else: ?>
                                        <div
                                            style="height: 100%; display: flex; align-items: center; justify-content: center; background: #eee;">
                                            <i class="fas fa-image" style="font-size: 3rem; color: #ccc;"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="design-card-content">
                                    <h4><?php echo $design['title']; ?></h4>
                                    <p style="font-size: 0.85rem; color: #666; margin-bottom: 10px;">
                                        <?php echo substr(strip_tags($design['description'] ?? ''), 0, 60); ?>...
                                    </p>
                                    <div class="design-card-actions">
                                        <a href="?category=<?php echo $selectedCategory; ?>&edit=<?php echo $id; ?>"
                                            class="btn btn-info">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form method="POST" style="display: inline; margin: 0;">
                                            <input type="hidden" name="category" value="<?php echo $selectedCategory; ?>">
                                            <input type="hidden" name="design_id" value="<?php echo $id; ?>">
                                            <button type="submit" name="delete_design" class="btn btn-danger"
                                                onclick="return confirm('Delete this design and all its images?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Image preview when selecting files
        const imageInput = document.getElementById('imageInput');
        const previewContainer = document.getElementById('newImagesPreview');
        const dropZone = document.getElementById('dropZone');

        if (imageInput) {
            imageInput.addEventListener('change', function (e) {
                previewContainer.innerHTML = '';
                const files = Array.from(this.files);

                if (files.length > 0) {
                    const label = document.createElement('h4');
                    label.style.marginBottom = '10px';
                    label.style.gridColumn = '1 / -1';
                    label.textContent = `Selected ${files.length} new image(s):`;
                    previewContainer.appendChild(label);
                }

                files.forEach((file, index) => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            const div = document.createElement('div');
                            div.className = 'image-preview-item';
                            div.innerHTML = `
                                <img src="${e.target.result}" alt="Preview">
                                <span class="new-badge">NEW</span>
                            `;
                            previewContainer.appendChild(div);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            });

            // Drag and drop
            dropZone.addEventListener('dragover', function (e) {
                e.preventDefault();
                this.classList.add('dragover');
            });

            dropZone.addEventListener('dragleave', function () {
                this.classList.remove('dragover');
            });

            dropZone.addEventListener('drop', function (e) {
                e.preventDefault();
                this.classList.remove('dragover');
                imageInput.files = e.dataTransfer.files;
                imageInput.dispatchEvent(new Event('change'));
            });
        }
    </script>
</body>

</html>