<?php
/**
 * KV Wood Works - Admin Brands Management
 * Manage brand logos that appear on the homepage
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$pageTitle = 'Manage Branded Materials';
$configFile = __DIR__ . '/../config/brands.json';
$uploadDir = __DIR__ . '/../assets/images/brands/';

// Ensure upload directory exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Load brands config
$config = json_decode(file_get_contents($configFile), true);
$brands = $config['brands'] ?? [];

$message = '';
$messageType = '';

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_brand'])) {
    $brandId = $_POST['brand_id'] ?? '';

    if (!empty($_FILES['brand_image']['name']) && $brandId) {
        $file = $_FILES['brand_image'];

        // Get file extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($ext, $allowedExt)) {
            $fileName = $brandId . '_' . time() . '.' . $ext;
            $targetPath = $uploadDir . $fileName;

            // Simply move the uploaded file
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                // Update brand in config
                foreach ($brands as &$brand) {
                    if ($brand['id'] === $brandId) {
                        // Delete old image if exists
                        if (!empty($brand['image']) && file_exists($uploadDir . $brand['image'])) {
                            unlink($uploadDir . $brand['image']);
                        }
                        $brand['image'] = $fileName;
                        break;
                    }
                }

                // Save config
                $config['brands'] = $brands;
                file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT));

                $message = 'Brand logo uploaded successfully!';
                $messageType = 'success';
            } else {
                $message = 'Failed to upload file. Check folder permissions.';
                $messageType = 'error';
            }
        } else {
            $message = 'Invalid file type. Please use JPG, PNG, GIF, or WebP.';
            $messageType = 'error';
        }
    }

    // Reload brands
    $config = json_decode(file_get_contents($configFile), true);
    $brands = $config['brands'] ?? [];
}

// Handle brand update (name/description)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_brand'])) {
    $brandId = $_POST['brand_id'] ?? '';
    $name = trim($_POST['brand_name'] ?? '');
    $description = trim($_POST['brand_description'] ?? '');
    $enabled = isset($_POST['brand_enabled']) ? true : false;

    foreach ($brands as &$brand) {
        if ($brand['id'] === $brandId) {
            $brand['name'] = $name;
            $brand['description'] = $description;
            $brand['enabled'] = $enabled;
            break;
        }
    }

    $config['brands'] = $brands;
    file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT));

    $message = 'Brand updated successfully!';
    $messageType = 'success';
}

// Handle add new brand
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_brand'])) {
    $name = trim($_POST['new_brand_name'] ?? '');
    $description = trim($_POST['new_brand_description'] ?? '');

    if (!empty($name)) {
        $id = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name));

        $brands[] = [
            'id' => $id,
            'name' => $name,
            'description' => $description,
            'image' => '',
            'enabled' => true
        ];

        $config['brands'] = $brands;
        file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT));

        $message = 'New brand added successfully!';
        $messageType = 'success';
    }
}

// Handle delete brand
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_brand'])) {
    $brandId = $_POST['brand_id'] ?? '';

    foreach ($brands as $key => $brand) {
        if ($brand['id'] === $brandId) {
            // Delete image if exists
            if (!empty($brand['image']) && file_exists($uploadDir . $brand['image'])) {
                unlink($uploadDir . $brand['image']);
            }
            unset($brands[$key]);
            break;
        }
    }

    $config['brands'] = array_values($brands);
    file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT));

    $message = 'Brand deleted successfully!';
    $messageType = 'success';

    // Reload brands
    $config = json_decode(file_get_contents($configFile), true);
    $brands = $config['brands'] ?? [];
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #c8956c;
            --primary-dark: #a87754;
            --dark: #1a1a1a;
            --light-gray: #f5f5f5;
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

        .admin-header h1 {
            font-size: 1.5rem;
        }

        .admin-header h1 span {
            color: var(--primary);
        }

        .admin-nav a {
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            margin-left: 10px;
            transition: background 0.3s;
        }

        .admin-nav a:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h2 {
            margin-bottom: 10px;
        }

        .page-header p {
            color: #666;
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
            color: #dc2626;
        }

        .brands-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
        }

        .brand-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .brand-card-header {
            background: var(--light-gray);
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-card-header h3 {
            font-size: 1.1rem;
        }

        .brand-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .brand-status.active {
            background: #dcfce7;
            color: #166534;
        }

        .brand-status.inactive {
            background: #fee2e2;
            color: #dc2626;
        }

        .brand-logo-preview {
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            border-bottom: 1px solid #eee;
            padding: 20px;
        }

        .brand-logo-preview img {
            max-width: 100%;
            max-height: 80px;
            object-fit: contain;
        }

        .brand-logo-preview .no-image {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            color: #999;
        }

        .brand-logo-preview .no-image i {
            font-size: 2.5rem;
            color: #ddd;
        }

        .brand-card-body {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.95rem;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }

        .upload-box {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: #fafafa;
        }

        .upload-box:hover {
            border-color: var(--primary);
            background: #fff;
        }

        .upload-box i {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .upload-box p {
            color: #666;
            font-size: 0.9rem;
        }

        .upload-box input[type="file"] {
            display: none;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-success {
            background: var(--success);
            color: #fff;
        }

        .btn-danger {
            background: #fee2e2;
            color: var(--error);
        }

        .btn-danger:hover {
            background: var(--error);
            color: #fff;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
        }

        .card-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
        }

        /* Add New Brand Card */
        .add-brand-card {
            background: #fff;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .add-brand-card h3 {
            margin-bottom: 20px;
        }

        .add-brand-form {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 15px;
            align-items: end;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .brands-grid {
                grid-template-columns: 1fr;
            }

            .add-brand-form {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <header class="admin-header">
        <h1><i class="fas fa-award"></i> Branded <span>Materials</span></h1>
        <nav class="admin-nav">
            <a href="index.php"><i class="fas fa-arrow-left"></i> Dashboard</a>
            <a href="<?php echo SITE_URL; ?>" target="_blank"><i class="fas fa-external-link-alt"></i> View Site</a>
        </nav>
    </header>

    <div class="container">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="page-header">
            <h2>Manage Brand Logos</h2>
            <p>Upload brand logos to display in the "We Use Only Branded Materials" section on the homepage.</p>
        </div>

        <!-- Add New Brand -->
        <div class="add-brand-card">
            <h3><i class="fas fa-plus-circle" style="color: var(--primary);"></i> Add New Brand</h3>
            <form method="POST" class="add-brand-form">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="new_brand_name">Brand Name</label>
                    <input type="text" id="new_brand_name" name="new_brand_name" class="form-control"
                        placeholder="e.g. BOSCH" required>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="new_brand_description">Description</label>
                    <input type="text" id="new_brand_description" name="new_brand_description" class="form-control"
                        placeholder="e.g. Kitchen Appliances">
                </div>
                <button type="submit" name="add_brand" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add Brand
                </button>
            </form>
        </div>

        <!-- Brands Grid -->
        <div class="brands-grid">
            <?php foreach ($brands as $brand): ?>
                <div class="brand-card">
                    <div class="brand-card-header">
                        <h3>
                            <?php echo htmlspecialchars($brand['name']); ?>
                        </h3>
                        <span class="brand-status <?php echo $brand['enabled'] ? 'active' : 'inactive'; ?>">
                            <?php echo $brand['enabled'] ? 'Active' : 'Hidden'; ?>
                        </span>
                    </div>

                    <div class="brand-logo-preview">
                        <?php if (!empty($brand['image']) && file_exists($uploadDir . $brand['image'])): ?>
                            <img src="../assets/images/brands/<?php echo $brand['image']; ?>?v=<?php echo time(); ?>"
                                alt="<?php echo htmlspecialchars($brand['name']); ?>">
                        <?php else: ?>
                            <div class="no-image">
                                <i class="fas fa-image"></i>
                                <span>No logo uploaded</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="brand-card-body">
                        <!-- Upload Image Form -->
                        <form method="POST" enctype="multipart/form-data" style="margin-bottom: 15px;">
                            <input type="hidden" name="brand_id" value="<?php echo $brand['id']; ?>">
                            <label class="upload-box" onclick="this.querySelector('input').click()">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Click to upload logo</p>
                                <small style="color: #999;">JPG, PNG, GIF, WebP (any size)</small>
                                <input type="file" name="brand_image" accept="image/*" onchange="this.form.submit()">
                            </label>
                            <input type="hidden" name="upload_brand" value="1">
                        </form>

                        <!-- Update Details Form -->
                        <form method="POST">
                            <input type="hidden" name="brand_id" value="<?php echo $brand['id']; ?>">

                            <div class="form-group">
                                <label>Brand Name</label>
                                <input type="text" name="brand_name" class="form-control"
                                    value="<?php echo htmlspecialchars($brand['name']); ?>">
                            </div>

                            <div class="form-group">
                                <label>Description</label>
                                <input type="text" name="brand_description" class="form-control"
                                    value="<?php echo htmlspecialchars($brand['description']); ?>">
                            </div>

                            <div class="form-group">
                                <label class="checkbox-group">
                                    <input type="checkbox" name="brand_enabled" <?php echo $brand['enabled'] ? 'checked' : ''; ?>>
                                    Show on website
                                </label>
                            </div>

                            <div class="card-actions">
                                <button type="submit" name="update_brand" class="btn btn-primary btn-sm">
                                    <i class="fas fa-save"></i> Save
                                </button>
                        </form>

                        <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this brand?');">
                            <input type="hidden" name="brand_id" value="<?php echo $brand['id']; ?>">
                            <button type="submit" name="delete_brand" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    </div>
</body>

</html>