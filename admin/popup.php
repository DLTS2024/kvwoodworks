<?php
/**
 * KV Wood Works - Popup Settings Admin
 * Manage the consultation popup form
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/database.php';

// Popup settings file
$settingsFile = __DIR__ . '/../config/popup_settings.json';

// Default settings
$defaultSettings = [
    'enabled' => true,
    'title' => 'Get a FREE Design Consultation',
    'offer_text' => 'New Year Special Offer',
    'discount' => '25% OFF',
    'discount_label' => 'On Modular Interiors',
    'offer_expiry' => 'Hurry, Book Before 31st January 2026',
    'show_after_seconds' => 5,
    'show_on_scroll_percent' => 50,
    'image' => 'assets/images/popup-offer.jpg'
];

// Load current settings
if (file_exists($settingsFile)) {
    $settings = json_decode(file_get_contents($settingsFile), true);
} else {
    $settings = $defaultSettings;
}

// Handle form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'enabled' => isset($_POST['enabled']),
        'title' => sanitize($_POST['title']),
        'offer_text' => sanitize($_POST['offer_text']),
        'discount' => sanitize($_POST['discount']),
        'discount_label' => sanitize($_POST['discount_label']),
        'offer_expiry' => sanitize($_POST['offer_expiry']),
        'show_after_seconds' => (int) $_POST['show_after_seconds'],
        'show_on_scroll_percent' => (int) $_POST['show_on_scroll_percent'],
        'image' => $settings['image']
    ];

    // Handle image upload
    if (isset($_FILES['popup_image']) && $_FILES['popup_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../assets/images/';
        $extension = pathinfo($_FILES['popup_image']['name'], PATHINFO_EXTENSION);
        $newFilename = 'popup-offer.' . $extension;

        if (move_uploaded_file($_FILES['popup_image']['tmp_name'], $uploadDir . $newFilename)) {
            $settings['image'] = 'assets/images/' . $newFilename;
        }
    }

    // Save settings
    if (file_put_contents($settingsFile, json_encode($settings, JSON_PRETTY_PRINT))) {
        $message = 'Popup settings saved successfully!';
        $messageType = 'success';
    } else {
        $message = 'Failed to save settings.';
        $messageType = 'error';
    }
}

$pageTitle = 'Popup Settings';
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
        }

        .admin-header a {
            color: var(--white);
            text-decoration: none;
            padding: 8px 16px;
            background: var(--primary);
            border-radius: 6px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 30px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
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
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .card h2 {
            margin-bottom: 25px;
            color: var(--dark);
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 10px;
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

        .toggle-switch {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .toggle-switch input {
            width: 50px;
            height: 26px;
            appearance: none;
            background: #ddd;
            border-radius: 13px;
            position: relative;
            cursor: pointer;
            transition: background 0.3s;
        }

        .toggle-switch input:checked {
            background: var(--success);
        }

        .toggle-switch input::before {
            content: '';
            position: absolute;
            width: 22px;
            height: 22px;
            background: white;
            border-radius: 50%;
            top: 2px;
            left: 2px;
            transition: transform 0.3s;
        }

        .toggle-switch input:checked::before {
            transform: translateX(24px);
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

        .btn-primary:hover {
            background: #b8854c;
        }

        .preview-box {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 12px;
            padding: 30px;
            color: white;
            text-align: center;
            margin-top: 20px;
        }

        .preview-box h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .preview-discount {
            background: #ff6b6b;
            display: inline-block;
            padding: 10px 25px;
            border-radius: 8px;
            font-size: 1.8rem;
            font-weight: 700;
            margin: 10px 0;
        }

        .image-preview {
            max-width: 200px;
            margin-top: 15px;
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <header class="admin-header">
        <h1><i class="fas fa-window-restore"></i> Popup Settings</h1>
        <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </header>

    <div class="container">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="card">
                <h2><i class="fas fa-toggle-on"></i> Popup Status</h2>

                <div class="toggle-switch">
                    <input type="checkbox" name="enabled" id="enabled" <?php echo $settings['enabled'] ? 'checked' : ''; ?>>
                    <label for="enabled">Enable popup on all pages</label>
                </div>
            </div>

            <div class="card">
                <h2><i class="fas fa-edit"></i> Popup Content</h2>

                <div class="form-group">
                    <label>Popup Title</label>
                    <input type="text" name="title" class="form-control" value="<?php echo $settings['title']; ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Offer Text (above discount)</label>
                        <input type="text" name="offer_text" class="form-control"
                            value="<?php echo $settings['offer_text']; ?>">
                    </div>

                    <div class="form-group">
                        <label>Discount (e.g., 25% OFF)</label>
                        <input type="text" name="discount" class="form-control"
                            value="<?php echo $settings['discount']; ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Discount Label</label>
                        <input type="text" name="discount_label" class="form-control"
                            value="<?php echo $settings['discount_label']; ?>">
                    </div>

                    <div class="form-group">
                        <label>Offer Expiry Text</label>
                        <input type="text" name="offer_expiry" class="form-control"
                            value="<?php echo $settings['offer_expiry']; ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Popup Banner Image</label>
                    <input type="file" name="popup_image" class="form-control" accept="image/*">
                    <?php if (!empty($settings['image'])): ?>
                        <img src="<?php echo SITE_URL . '/' . $settings['image']; ?>" alt="Current banner"
                            class="image-preview">
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <h2><i class="fas fa-clock"></i> Display Settings</h2>

                <div class="form-row">
                    <div class="form-group">
                        <label>Show popup after (seconds)</label>
                        <input type="number" name="show_after_seconds" class="form-control"
                            value="<?php echo $settings['show_after_seconds']; ?>" min="0" max="60">
                    </div>

                    <div class="form-group">
                        <label>Or show after scroll (%)</label>
                        <input type="number" name="show_on_scroll_percent" class="form-control"
                            value="<?php echo $settings['show_on_scroll_percent']; ?>" min="0" max="100">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Settings
            </button>
        </form>

        <!-- Preview -->
        <div class="card" style="margin-top: 30px;">
            <h2><i class="fas fa-eye"></i> Preview</h2>
            <div class="preview-box">
                <p style="opacity: 0.8;">
                    <?php echo $settings['offer_text']; ?>
                </p>
                <div class="preview-discount">
                    <?php echo $settings['discount']; ?>
                </div>
                <p>
                    <?php echo $settings['discount_label']; ?>
                </p>
                <p style="margin-top: 15px; font-size: 0.9rem; opacity: 0.7;">
                    <?php echo $settings['offer_expiry']; ?>
                </p>
            </div>
        </div>
    </div>
</body>

</html>