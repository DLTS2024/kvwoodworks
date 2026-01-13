<?php
/**
 * KV Wood Works - Admin Offer Banners Manager
 * Manage promotional banners displayed across the website
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$pageTitle = 'Offer Banners';

// Banner settings file
$bannersFile = __DIR__ . '/../config/banners.json';
$uploadDir = __DIR__ . '/../assets/images/banners/';

// Create upload directory if not exists
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Load existing settings
$banners = [];
if (file_exists($bannersFile)) {
    $banners = json_decode(file_get_contents($bannersFile), true) ?? [];
}

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Header Banner
    $banners['header_banner'] = [
        'enabled' => isset($_POST['header_enabled']),
        'image' => $banners['header_banner']['image'] ?? '',
        'link' => sanitize($_POST['header_link'] ?? 'get-estimate.php'),
        'alt_text' => sanitize($_POST['header_alt'] ?? 'Special Offer'),
        'background_color' => sanitize($_POST['header_bg_color'] ?? '#1a1a1a')
    ];

    // Middle Banner
    $banners['middle_banner'] = [
        'enabled' => isset($_POST['middle_enabled']),
        'image' => $banners['middle_banner']['image'] ?? '',
        'link' => sanitize($_POST['middle_link'] ?? 'get-estimate.php'),
        'alt_text' => sanitize($_POST['middle_alt'] ?? 'Limited Time Offer'),
        'title' => sanitize($_POST['middle_title'] ?? ''),
        'subtitle' => sanitize($_POST['middle_subtitle'] ?? ''),
        'description' => sanitize($_POST['middle_description'] ?? ''),
        'button_text' => sanitize($_POST['middle_button'] ?? 'Book Free Consultation'),
        'valid_until' => sanitize($_POST['middle_valid'] ?? '')
    ];

    // Footer Banner
    $banners['footer_banner'] = [
        'enabled' => isset($_POST['footer_enabled']),
        'image' => $banners['footer_banner']['image'] ?? '',
        'link' => sanitize($_POST['footer_link'] ?? 'get-estimate.php'),
        'alt_text' => sanitize($_POST['footer_alt'] ?? 'Exclusive Offer')
    ];

    // Guide Page Banner
    $banners['guide_banner'] = [
        'enabled' => isset($_POST['guide_enabled']),
        'image' => $banners['guide_banner']['image'] ?? '',
        'link' => sanitize($_POST['guide_link'] ?? 'get-estimate.php'),
        'alt_text' => sanitize($_POST['guide_alt'] ?? 'Design Consultation'),
        'title' => sanitize($_POST['guide_title'] ?? ''),
        'subtitle' => sanitize($_POST['guide_subtitle'] ?? ''),
        'description' => sanitize($_POST['guide_description'] ?? ''),
        'button_text' => sanitize($_POST['guide_button'] ?? 'Get Free Quote'),
        'size' => sanitize($_POST['guide_size'] ?? 'standard'),
        'background_color' => sanitize($_POST['guide_bg_color'] ?? '#1a1a1a')
    ];

    // Handle image uploads
    $bannerTypes = ['header_banner', 'middle_banner', 'footer_banner', 'guide_banner'];
    $fileFields = ['header_image', 'middle_image', 'footer_image', 'guide_image'];

    foreach ($fileFields as $index => $field) {
        if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $fileType = $_FILES[$field]['type'];

            if (in_array($fileType, $allowedTypes)) {
                // Delete old image
                if (!empty($banners[$bannerTypes[$index]]['image'])) {
                    $oldPath = __DIR__ . '/../' . $banners[$bannerTypes[$index]]['image'];
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                $extension = pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
                $newFilename = $bannerTypes[$index] . '-' . time() . '.' . $extension;
                $targetPath = $uploadDir . $newFilename;

                if (move_uploaded_file($_FILES[$field]['tmp_name'], $targetPath)) {
                    $banners[$bannerTypes[$index]]['image'] = 'assets/images/banners/' . $newFilename;
                }
            }
        }
    }

    $banners['updated_at'] = date('Y-m-d H:i:s');

    if (file_put_contents($bannersFile, json_encode($banners, JSON_PRETTY_PRINT))) {
        $message = 'Banner settings saved successfully!';
        $messageType = 'success';
    } else {
        $message = 'Failed to save settings.';
        $messageType = 'error';
    }
}

// Delete image
if (isset($_GET['delete'])) {
    $type = sanitize($_GET['delete']);
    if (isset($banners[$type]['image']) && !empty($banners[$type]['image'])) {
        $imgPath = __DIR__ . '/../' . $banners[$type]['image'];
        if (file_exists($imgPath)) {
            unlink($imgPath);
        }
        $banners[$type]['image'] = '';
        file_put_contents($bannersFile, json_encode($banners, JSON_PRETTY_PRINT));
        header('Location: banners.php?deleted=1');
        exit;
    }
}

if (isset($_GET['deleted'])) {
    $message = 'Banner image deleted!';
    $messageType = 'success';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $pageTitle; ?> | Admin
    </title>
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
            max-width: 1000px;
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
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--light-gray);
        }

        .card-header h2 {
            font-size: 1.2rem;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toggle-switch {
            position: relative;
            width: 50px;
            height: 26px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: #ccc;
            border-radius: 26px;
            transition: 0.3s;
        }

        .toggle-slider:before {
            content: "";
            position: absolute;
            height: 20px;
            width: 20px;
            left: 3px;
            bottom: 3px;
            background: white;
            border-radius: 50%;
            transition: 0.3s;
        }

        input:checked+.toggle-slider {
            background: var(--success);
        }

        input:checked+.toggle-slider:before {
            transform: translateX(24px);
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

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .image-preview {
            width: 100%;
            max-height: 200px;
            object-fit: contain;
            border-radius: 8px;
            margin-bottom: 10px;
            background: var(--light-gray);
        }

        .file-upload {
            border: 2px dashed #ccc;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .file-upload:hover {
            border-color: var(--primary);
            background: rgba(200, 149, 108, 0.05);
        }

        .file-upload input {
            display: none;
        }

        .file-upload i {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 10px;
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

        .btn-primary {
            background: var(--primary);
            color: var(--white);
        }

        .btn-danger {
            background: var(--error);
            color: var(--white);
            padding: 8px 15px;
            font-size: 0.85rem;
        }

        .info-box {
            background: #e0f2fe;
            border: 1px solid #0ea5e9;
            padding: 15px;
            border-radius: 8px;
            font-size: 0.9rem;
            color: #0369a1;
            margin-bottom: 20px;
        }

        .info-box i {
            margin-right: 8px;
        }

        .current-image {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            background: var(--light-gray);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .current-image img {
            max-width: 300px;
            max-height: 100px;
            object-fit: contain;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <header class="admin-header">
        <h1><i class="fas fa-bullhorn"></i> Offer Banners</h1>
        <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </header>

    <div class="container">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <strong>Banner Placement:</strong> These banners will appear on every page of your website.
            Upload banner images (recommended size: 1200x200px) and they will automatically appear across the site.
        </div>

        <form method="POST" enctype="multipart/form-data">

            <!-- Header Banner -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-arrow-up"></i> Header Banner (Top of Page)</h2>
                    <label class="toggle-switch">
                        <input type="checkbox" name="header_enabled" <?php echo ($banners['header_banner']['enabled'] ?? true) ? 'checked' : ''; ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <?php if (!empty($banners['header_banner']['image'])): ?>
                    <div class="current-image">
                        <img src="<?php echo SITE_URL . '/' . $banners['header_banner']['image']; ?>"
                            alt="Current Header Banner">
                        <div>
                            <p style="margin-bottom: 10px;"><strong>Current Image</strong></p>
                            <a href="?delete=header_banner" class="btn btn-danger"
                                onclick="return confirm('Delete this banner image?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label>Upload New Image (1200x200px recommended)</label>
                    <label class="file-upload">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Click to upload header banner image</p>
                        <input type="file" name="header_image" accept="image/*">
                    </label>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Link URL</label>
                        <input type="text" name="header_link" class="form-control"
                            value="<?php echo $banners['header_banner']['link'] ?? 'get-estimate.php'; ?>"
                            placeholder="e.g., get-estimate.php">
                    </div>
                    <div class="form-group">
                        <label>Alt Text</label>
                        <input type="text" name="header_alt" class="form-control"
                            value="<?php echo $banners['header_banner']['alt_text'] ?? 'Special Offer'; ?>">
                    </div>
                </div>
            </div>

            <!-- Middle Banner -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-arrows-alt-v"></i> Middle Banner (Between Sections)</h2>
                    <label class="toggle-switch">
                        <input type="checkbox" name="middle_enabled" <?php echo ($banners['middle_banner']['enabled'] ?? true) ? 'checked' : ''; ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <?php if (!empty($banners['middle_banner']['image'])): ?>
                    <div class="current-image">
                        <img src="<?php echo SITE_URL . '/' . $banners['middle_banner']['image']; ?>"
                            alt="Current Middle Banner">
                        <div>
                            <p style="margin-bottom: 10px;"><strong>Current Image</strong></p>
                            <a href="?delete=middle_banner" class="btn btn-danger"
                                onclick="return confirm('Delete this banner image?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label>Upload New Image (1200x300px recommended)</label>
                    <label class="file-upload">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Click to upload middle banner image</p>
                        <input type="file" name="middle_image" accept="image/*">
                    </label>
                </div>

                <p style="color: #666; margin-bottom: 15px; font-size: 0.9rem;">
                    <i class="fas fa-magic"></i> Or use text-based banner (if no image uploaded):
                </p>

                <div class="form-row">
                    <div class="form-group">
                        <label>Offer Title</label>
                        <input type="text" name="middle_title" class="form-control"
                            value="<?php echo $banners['middle_banner']['title'] ?? ''; ?>"
                            placeholder="e.g., New Year Special Offer!">
                    </div>
                    <div class="form-group">
                        <label>Main Offer Text</label>
                        <input type="text" name="middle_subtitle" class="form-control"
                            value="<?php echo $banners['middle_banner']['subtitle'] ?? ''; ?>"
                            placeholder="e.g., FLAT 25% OFF">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Description</label>
                        <input type="text" name="middle_description" class="form-control"
                            value="<?php echo $banners['middle_banner']['description'] ?? ''; ?>"
                            placeholder="e.g., On Modular Interiors">
                    </div>
                    <div class="form-group">
                        <label>Valid Until</label>
                        <input type="text" name="middle_valid" class="form-control"
                            value="<?php echo $banners['middle_banner']['valid_until'] ?? ''; ?>"
                            placeholder="e.g., 31st January, 2026">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Button Text</label>
                        <input type="text" name="middle_button" class="form-control"
                            value="<?php echo $banners['middle_banner']['button_text'] ?? 'Book Free Consultation'; ?>">
                    </div>
                    <div class="form-group">
                        <label>Link URL</label>
                        <input type="text" name="middle_link" class="form-control"
                            value="<?php echo $banners['middle_banner']['link'] ?? 'get-estimate.php'; ?>">
                    </div>
                </div>
            </div>

            <!-- Footer Banner -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-arrow-down"></i> Footer Banner (Before Footer)</h2>
                    <label class="toggle-switch">
                        <input type="checkbox" name="footer_enabled" <?php echo ($banners['footer_banner']['enabled'] ?? true) ? 'checked' : ''; ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <?php if (!empty($banners['footer_banner']['image'])): ?>
                    <div class="current-image">
                        <img src="<?php echo SITE_URL . '/' . $banners['footer_banner']['image']; ?>"
                            alt="Current Footer Banner">
                        <div>
                            <p style="margin-bottom: 10px;"><strong>Current Image</strong></p>
                            <a href="?delete=footer_banner" class="btn btn-danger"
                                onclick="return confirm('Delete this banner image?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label>Upload New Image (1200x200px recommended)</label>
                    <label class="file-upload">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Click to upload footer banner image</p>
                        <input type="file" name="footer_image" accept="image/*">
                    </label>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Link URL</label>
                        <input type="text" name="footer_link" class="form-control"
                            value="<?php echo $banners['footer_banner']['link'] ?? 'get-estimate.php'; ?>">
                    </div>
                    <div class="form-group">
                        <label>Alt Text</label>
                        <input type="text" name="footer_alt" class="form-control"
                            value="<?php echo $banners['footer_banner']['alt_text'] ?? 'Exclusive Offer'; ?>">
                    </div>
                </div>
            </div>

            <!-- Guide Page Banner -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-book-open"></i> Guide Page Banner (Inside Articles)</h2>
                    <label class="toggle-switch">
                        <input type="checkbox" name="guide_enabled" <?php echo ($banners['guide_banner']['enabled'] ?? false) ? 'checked' : ''; ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="info-box" style="background: #fef3c7; border-color: #f59e0b; color: #92400e;">
                    <i class="fas fa-book"></i>
                    This banner appears inside guide article pages (like Kids Room Colours Guide) every 3 sections.
                </div>

                <?php if (!empty($banners['guide_banner']['image'])): ?>
                    <div class="current-image">
                        <img src="<?php echo SITE_URL . '/' . $banners['guide_banner']['image']; ?>"
                            alt="Current Guide Banner">
                        <div>
                            <p style="margin-bottom: 10px;"><strong>Current Image</strong></p>
                            <a href="?delete=guide_banner" class="btn btn-danger"
                                onclick="return confirm('Delete this banner image?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label>Banner Size</label>
                    <select name="guide_size" class="form-control">
                        <option value="compact" <?php echo ($banners['guide_banner']['size'] ?? 'standard') === 'compact' ? 'selected' : ''; ?>>Compact (80px height)</option>
                        <option value="standard" <?php echo ($banners['guide_banner']['size'] ?? 'standard') === 'standard' ? 'selected' : ''; ?>>Standard (120px height)</option>
                        <option value="large" <?php echo ($banners['guide_banner']['size'] ?? 'standard') === 'large' ? 'selected' : ''; ?>>Large (180px height)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Upload Banner Image (800x120px recommended for standard)</label>
                    <label class="file-upload">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Click to upload guide banner image</p>
                        <input type="file" name="guide_image" accept="image/*">
                    </label>
                </div>

                <p style="color: #666; margin-bottom: 15px; font-size: 0.9rem;">
                    <i class="fas fa-magic"></i> Or use text-based banner (if no image uploaded):
                </p>

                <div class="form-row">
                    <div class="form-group">
                        <label>Banner Title</label>
                        <input type="text" name="guide_title" class="form-control"
                            value="<?php echo $banners['guide_banner']['title'] ?? ''; ?>"
                            placeholder="e.g., Need Expert Design Help?">
                    </div>
                    <div class="form-group">
                        <label>Highlight Text</label>
                        <input type="text" name="guide_subtitle" class="form-control"
                            value="<?php echo $banners['guide_banner']['subtitle'] ?? ''; ?>"
                            placeholder="e.g., FREE Consultation">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Description</label>
                        <input type="text" name="guide_description" class="form-control"
                            value="<?php echo $banners['guide_banner']['description'] ?? ''; ?>"
                            placeholder="e.g., Get personalized interior design advice">
                    </div>
                    <div class="form-group">
                        <label>Button Text</label>
                        <input type="text" name="guide_button" class="form-control"
                            value="<?php echo $banners['guide_banner']['button_text'] ?? 'Get Free Quote'; ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Link URL</label>
                        <input type="text" name="guide_link" class="form-control"
                            value="<?php echo $banners['guide_banner']['link'] ?? 'get-estimate.php'; ?>">
                    </div>
                    <div class="form-group">
                        <label>Background Color</label>
                        <input type="color" name="guide_bg_color" class="form-control"
                            style="height: 50px; padding: 5px;"
                            value="<?php echo $banners['guide_banner']['background_color'] ?? '#1a1a1a'; ?>">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save All Banner Settings
            </button>
        </form>
    </div>
</body>

</html>