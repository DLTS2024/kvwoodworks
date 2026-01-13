<?php
/**
 * KV Wood Works - Admin Projects Manager
 * Add/Edit/Delete completed project showcases with multi-image support
 */

 require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$pageTitle = 'Update Projects';

// Projects file path
$projectsFile = __DIR__ . '/../config/projects.json';

// Load existing projects
$projects = [];
if (file_exists($projectsFile)) {
    $projects = json_decode(file_get_contents($projectsFile), true) ?? [];
}

// Available tags
$availableTags = ['Door', 'TV Unit', 'Kitchen', 'Wardrobe', 'Bedroom', 'Living Room', 'Pooja Room', 'Staircase', 'Window'];

// Handle form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add or Edit project
    if (isset($_POST['save_project'])) {
        $projectName = sanitize($_POST['project_name']);
        $location = sanitize($_POST['location']);
        $description = sanitize($_POST['description']);
        $propertyArea = sanitize($_POST['property_area'] ?? '');
        $propertyType = sanitize($_POST['property_type'] ?? '');
        $clientBrief = $_POST['client_brief'] ?? '';
        $designSolution = $_POST['design_solution'] ?? '';
        $tags = isset($_POST['tags']) ? $_POST['tags'] : [];
        $editId = isset($_POST['edit_id']) ? sanitize($_POST['edit_id']) : '';

        if (empty($projectName) || empty($location)) {
            $message = 'Please enter project name and location.';
            $messageType = 'error';
        } else {
            // Handle image uploads
            $newImages = [];
            $uploadDir = __DIR__ . '/../assets/images/projects/';

            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $totalFiles = count($_FILES['images']['name']);

                for ($i = 0; $i < $totalFiles; $i++) {
                    if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                        $tmpName = $_FILES['images']['tmp_name'][$i];
                        $fileType = $_FILES['images']['type'][$i];

                        if (in_array($fileType, $allowedTypes)) {
                            $extension = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION);
                            $newFilename = 'project-' . time() . '-' . uniqid() . '-' . $i . '.' . $extension;
                            $targetPath = $uploadDir . $newFilename;

                            if (move_uploaded_file($tmpName, $targetPath)) {
                                $newImages[] = 'assets/images/projects/' . $newFilename;
                            }
                        }
                    }
                }
            }

            // Get existing images if editing
            $existingImages = [];
            $existingProject = null;
            if ($editId && isset($projects[$editId])) {
                $existingProject = $projects[$editId];
                $existingImages = $existingProject['images'] ?? [];
            }

            // Merge images - new ones add to existing
            $allImages = array_merge($existingImages, $newImages);

            // Create or update project
            $projectId = $editId ?: 'proj_' . time() . '_' . uniqid();

            $projects[$projectId] = [
                'name' => $projectName,
                'location' => $location,
                'description' => $description,
                'property_area' => $propertyArea,
                'property_type' => $propertyType,
                'client_brief' => $clientBrief,
                'design_solution' => $designSolution,
                'tags' => $tags,
                'images' => $allImages,
                'created_at' => ($existingProject && isset($existingProject['created_at']))
                    ? $existingProject['created_at']
                    : date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if (file_put_contents($projectsFile, json_encode($projects, JSON_PRETTY_PRINT))) {
                $message = $editId ? 'Project updated successfully!' : 'Project added successfully!';
                $messageType = 'success';
                // Clear edit mode after save
                if ($editId) {
                    header("Location: projects.php?saved=1");
                    exit;
                }
            } else {
                $message = 'Failed to save project.';
                $messageType = 'error';
            }
        }
    }

    // Delete project
    if (isset($_POST['delete_project'])) {
        $projectId = sanitize($_POST['project_id']);
        if (isset($projects[$projectId])) {
            // Delete project images
            if (!empty($projects[$projectId]['images'])) {
                foreach ($projects[$projectId]['images'] as $img) {
                    $imgPath = __DIR__ . '/../' . $img;
                    if (file_exists($imgPath)) {
                        unlink($imgPath);
                    }
                }
            }
            unset($projects[$projectId]);
            file_put_contents($projectsFile, json_encode($projects, JSON_PRETTY_PRINT));
            $message = 'Project deleted successfully!';
            $messageType = 'success';
        }
    }

    // Delete single image from project
    if (isset($_POST['delete_image'])) {
        $projectId = sanitize($_POST['project_id']);
        $imageIndex = intval($_POST['image_index']);

        if (isset($projects[$projectId]['images'][$imageIndex])) {
            $imgPath = __DIR__ . '/../' . $projects[$projectId]['images'][$imageIndex];
            if (file_exists($imgPath)) {
                unlink($imgPath);
            }
            array_splice($projects[$projectId]['images'], $imageIndex, 1);
            file_put_contents($projectsFile, json_encode($projects, JSON_PRETTY_PRINT));
            
            // Redirect back to edit page
            header("Location: projects.php?edit=" . urlencode($projectId) . "&deleted=1");
            exit;
        }
    }
}

// Get edit mode
$editId = isset($_GET['edit']) ? sanitize($_GET['edit']) : '';
$editProject = ($editId && isset($projects[$editId])) ? $projects[$editId] : null;

// Check for success messages from redirects
if (isset($_GET['saved'])) {
    $message = 'Project updated successfully!';
    $messageType = 'success';
}
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

        .admin-header h1 { font-size: 1.3rem; display: flex; align-items: center; gap: 10px; }

        .admin-header a {
            color: var(--white);
            text-decoration: none;
            padding: 8px 16px;
            background: var(--primary);
            border-radius: 6px;
        }

        .container {
            max-width: 1100px;
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
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group { margin-bottom: 20px; }

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

        .form-control:focus { outline: none; border-color: var(--primary); }

        textarea.form-control { min-height: 100px; resize: vertical; }

        .tags-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .tag-option { cursor: pointer; }

        .tag-option input { display: none; }

        .tag-option span {
            display: inline-block;
            padding: 8px 15px;
            border: 2px solid #e5e5e5;
            border-radius: 20px;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .tag-option input:checked+span {
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
            text-decoration: none;
        }

        .btn-primary { background: var(--primary); color: var(--white); }
        .btn-success { background: var(--success); color: var(--white); }
        .btn-secondary { background: #e5e5e5; color: var(--dark); }
        .btn-danger { background: var(--error); color: var(--white); padding: 8px 15px; font-size: 0.85rem; }
        .btn-info { background: var(--info); color: var(--white); padding: 8px 15px; font-size: 0.85rem; }

        .file-upload-area {
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }

        .file-upload-area:hover, .file-upload-area.dragover { 
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

        .file-upload-area i { font-size: 2.5rem; color: var(--primary); margin-bottom: 10px; }

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
            width: 28px;
            height: 28px;
            background: var(--error);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 0.9rem;
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

        /* Projects Grid */
        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .project-item {
            background: var(--light-gray);
            border-radius: 12px;
            overflow: hidden;
        }

        .project-item-image {
            height: 180px;
            background: #ddd;
            position: relative;
        }

        .project-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .project-item-image .image-count {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
        }

        .project-item-content { padding: 15px; }

        .project-item-content h4 { margin-bottom: 5px; }

        .project-item-content .location {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .project-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-bottom: 10px;
        }

        .project-tag {
            background: var(--primary);
            color: white;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
        }

        .project-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {
            .form-row { grid-template-columns: 1fr; }
            .projects-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>

<body>
    <header class="admin-header">
        <h1><i class="fas fa-project-diagram"></i> Update Projects</h1>
        <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </header>

    <div class="container">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Add/Edit Project Form -->
        <div class="card">
            <h2>
                <i class="fas fa-<?php echo $editProject ? 'edit' : 'plus-circle'; ?>"></i>
                <?php echo $editProject ? 'Edit Project' : 'Add New Project'; ?>
            </h2>

            <?php if ($editProject): ?>
                <div style="margin-bottom: 20px;">
                    <a href="projects.php" class="btn btn-secondary">
                        <i class="fas fa-plus"></i> Add New Instead
                    </a>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" id="projectForm">
                <input type="hidden" name="save_project" value="1">
                <?php if ($editId): ?>
                    <input type="hidden" name="edit_id" value="<?php echo $editId; ?>">
                <?php endif; ?>

                <div class="form-row">
                    <div class="form-group">
                        <label>Project Name *</label>
                        <input type="text" name="project_name" class="form-control"
                            value="<?php echo $editProject['name'] ?? ''; ?>"
                            placeholder="e.g., Modern Kitchen Project" required>
                    </div>

                    <div class="form-group">
                        <label>Location *</label>
                        <input type="text" name="location" class="form-control" 
                            value="<?php echo $editProject['location'] ?? ''; ?>"
                            placeholder="e.g., Anna Nagar, Chennai" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Property Area</label>
                        <input type="text" name="property_area" class="form-control" 
                            value="<?php echo $editProject['property_area'] ?? ''; ?>"
                            placeholder="e.g., Velachery, Chennai">
                    </div>

                    <div class="form-group">
                        <label>Property Type</label>
                        <input type="text" name="property_type" class="form-control" 
                            value="<?php echo $editProject['property_type'] ?? ''; ?>"
                            placeholder="e.g., 2 BHK, 3 BHK, Villa, Apartment">
                    </div>
                </div>

                <div class="form-group">
                    <label>Client Brief (What the client wanted)</label>
                    <textarea name="client_brief" class="form-control" rows="4"
                        placeholder="The client wanted a clutter-free home with minimal furniture..."><?php echo $editProject['client_brief'] ?? ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label>Design Solution (How you solved it)</label>
                    <textarea name="design_solution" class="form-control" rows="4"
                        placeholder="Our team collaborated together to make a perfect home..."><?php echo $editProject['design_solution'] ?? ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label>Short Description (for project cards)</label>
                    <textarea name="description" class="form-control"
                        placeholder="Brief description of the project..."><?php echo $editProject['description'] ?? ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label>Tags (Select applicable categories)</label>
                    <div class="tags-grid">
                        <?php foreach ($availableTags as $tag): 
                            $isChecked = $editProject && in_array($tag, $editProject['tags'] ?? []);
                        ?>
                            <label class="tag-option">
                                <input type="checkbox" name="tags[]" value="<?php echo $tag; ?>" 
                                    <?php echo $isChecked ? 'checked' : ''; ?>>
                                <span><?php echo $tag; ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Upload Images (Multiple) <?php echo $editProject ? '' : '*'; ?></label>
                    <div class="file-upload-area" id="dropZone">
                        <i class="fas fa-images"></i>
                        <p><strong>Click or drag to upload multiple images</strong></p>
                        <p style="color: #666; font-size: 0.9rem;">Hold Ctrl/Cmd to select multiple files</p>
                        <input type="file" name="images[]" id="imageInput" multiple accept="image/*">
                    </div>

                    <!-- New images preview (selected but not yet uploaded) -->
                    <div class="image-preview-container" id="newImagesPreview"></div>
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="fas fa-<?php echo $editProject ? 'save' : 'plus'; ?>"></i>
                    <?php echo $editProject ? 'Update Project' : 'Add Project'; ?>
                </button>
            </form>

            <!-- Existing images (OUTSIDE main form to avoid nesting) -->
            <?php if ($editProject && !empty($editProject['images'])): ?>
                <h4 style="margin-top: 20px; margin-bottom: 10px;">Current Images (click X to delete):</h4>
                <div class="image-preview-container">
                    <?php foreach ($editProject['images'] as $index => $img): ?>
                        <div class="image-preview-item" id="img-item-<?php echo $index; ?>">
                            <img src="<?php echo SITE_URL . '/' . $img; ?>" alt="">
                            <button type="button" class="remove-btn" 
                                onclick="deleteImage(<?php echo $index; ?>, '<?php echo $editId; ?>')">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Hidden form for image deletion -->
                <form id="deleteImageForm" method="POST" style="display: none;">
                    <input type="hidden" name="delete_image" value="1">
                    <input type="hidden" name="project_id" id="del_project_id">
                    <input type="hidden" name="image_index" id="del_image_index">
                </form>
                
                <script>
                function deleteImage(index, projectId) {
                    if (confirm('Delete this image?')) {
                        document.getElementById('del_project_id').value = projectId;
                        document.getElementById('del_image_index').value = index;
                        document.getElementById('deleteImageForm').submit();
                    }
                }
                </script>
            <?php endif; ?>
        </div>

        <!-- Existing Projects -->
        <div class="card">
            <h2><i class="fas fa-th-large"></i> Existing Projects (<?php echo count($projects); ?>)</h2>

            <?php if (empty($projects)): ?>
                <p style="color: #666; padding: 20px; text-align: center;">
                    <i class="fas fa-info-circle"></i> No projects added yet. Add your first project above!
                </p>
            <?php else: ?>
                <div class="projects-grid">
                    <?php foreach ($projects as $id => $project): ?>
                        <div class="project-item">
                            <div class="project-item-image">
                                <?php if (!empty($project['images'])): ?>
                                    <img src="<?php echo SITE_URL . '/' . $project['images'][0]; ?>" alt="">
                                    <span class="image-count"><i class="fas fa-images"></i> <?php echo count($project['images']); ?> images</span>
                                <?php else: ?>
                                    <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #999;">
                                        <i class="fas fa-image" style="font-size: 3rem;"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="project-item-content">
                                <h4><?php echo $project['name']; ?></h4>
                                <p class="location"><i class="fas fa-map-marker-alt"></i> <?php echo $project['location']; ?></p>

                                <?php if (!empty($project['tags'])): ?>
                                    <div class="project-tags">
                                        <?php foreach ($project['tags'] as $tag): ?>
                                            <span class="project-tag"><?php echo $tag; ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="project-actions">
                                    <a href="?edit=<?php echo $id; ?>" class="btn btn-info">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-danger" 
                                        onclick="deleteProject('<?php echo $id; ?>')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Custom Delete Confirmation Modal -->
    <div id="deleteModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 30px; border-radius: 12px; max-width: 400px; text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
            <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ef4444; margin-bottom: 20px;"></i>
            <h3 style="margin-bottom: 15px; color: #333;">Delete Project?</h3>
            <p style="color: #666; margin-bottom: 25px;">This will permanently delete this project and all its images. This action cannot be undone.</p>
            <div style="display: flex; gap: 10px; justify-content: center;">
                <button onclick="cancelDelete()" style="padding: 12px 25px; border: 2px solid #ddd; background: #f5f5f5; border-radius: 8px; cursor: pointer; font-weight: 600;">Cancel</button>
                <button onclick="confirmDelete()" style="padding: 12px 25px; border: none; background: #ef4444; color: white; border-radius: 8px; cursor: pointer; font-weight: 600;">Yes, Delete</button>
            </div>
        </div>
    </div>
    
    <!-- Hidden form for project deletion -->
    <form id="deleteProjectForm" method="POST" style="display: none;">
        <input type="hidden" name="delete_project" value="1">
        <input type="hidden" name="project_id" id="delete_project_id">
    </form>
    
    <script>
    var pendingDeleteId = null;
    
    function deleteProject(projectId) {
        pendingDeleteId = projectId;
        document.getElementById('deleteModal').style.display = 'flex';
    }
    
    function cancelDelete() {
        pendingDeleteId = null;
        document.getElementById('deleteModal').style.display = 'none';
    }
    
    function confirmDelete() {
        if (pendingDeleteId) {
            document.getElementById('delete_project_id').value = pendingDeleteId;
            document.getElementById('deleteProjectForm').submit();
        }
    }
    </script>

    <script>
        // Image preview when selecting files
        const imageInput = document.getElementById('imageInput');
        const previewContainer = document.getElementById('newImagesPreview');
        const dropZone = document.getElementById('dropZone');

        if (imageInput) {
            imageInput.addEventListener('change', function(e) {
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
                        reader.onload = function(e) {
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
            dropZone.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('dragover');
            });

            dropZone.addEventListener('dragleave', function() {
                this.classList.remove('dragover');
            });

            dropZone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
                imageInput.files = e.dataTransfer.files;
                imageInput.dispatchEvent(new Event('change'));
            });
        }
    </script>
</body>

</html>