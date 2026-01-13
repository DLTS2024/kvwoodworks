<?php
/**
 * KV Wood Works - Admin Leads Management
 * View contact form and estimate request submissions
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$pageTitle = 'Leads & Inquiries';

$dbAvailable = false;
$db = null;

try {
    $db = getDB();
    $dbAvailable = true;
} catch (Exception $e) {
    $dbAvailable = false;
}

// Handle delete action
if ($dbAvailable && isset($_POST['delete_lead'])) {
    $id = (int) $_POST['lead_id'];
    $type = $_POST['lead_type'];

    try {
        if ($type === 'contact') {
            $stmt = $db->prepare("DELETE FROM contacts WHERE id = ?");
            $stmt->execute([$id]);
        } elseif ($type === 'estimate') {
            $stmt = $db->prepare("DELETE FROM estimates WHERE id = ?");
            $stmt->execute([$id]);
        } elseif ($type === 'popup') {
            $stmt = $db->prepare("DELETE FROM popup_leads WHERE id = ?");
            $stmt->execute([$id]);
        }
        header("Location: leads.php?deleted=1");
        exit;
    } catch (Exception $e) {
        // Ignore errors
    }
}

// Get all contacts
$contacts = [];
if ($dbAvailable) {
    try {
        $stmt = $db->query("SELECT * FROM contacts ORDER BY created_at DESC");
        $contacts = $stmt->fetchAll();
    } catch (Exception $e) {
        // Table might not exist
    }
}

// Get all estimates
$estimates = [];
if ($dbAvailable) {
    try {
        $stmt = $db->query("SELECT * FROM estimates ORDER BY created_at DESC");
        $estimates = $stmt->fetchAll();
    } catch (Exception $e) {
        // Table might not exist
    }
}

// Get popup leads
$popupLeads = [];
if ($dbAvailable) {
    try {
        $stmt = $db->query("SELECT * FROM popup_leads ORDER BY created_at DESC");
        $popupLeads = $stmt->fetchAll();
    } catch (Exception $e) {
        // Table might not exist
    }
}

$activeTab = $_GET['tab'] ?? 'contacts';
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
            --warning: #f59e0b;
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

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
        }

        /* Tabs */
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
        }

        .tab {
            padding: 12px 24px;
            background: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            color: #666;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tab:hover {
            background: #e5e5e5;
        }

        .tab.active {
            background: var(--primary);
            color: #fff;
        }

        .tab .badge {
            background: rgba(0, 0, 0, 0.2);
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.8rem;
        }

        .tab.active .badge {
            background: rgba(255, 255, 255, 0.3);
        }

        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .card-header {
            background: var(--light-gray);
            padding: 20px 25px;
            border-bottom: 1px solid #eee;
        }

        .card-header h3 {
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Table */
        .leads-table {
            width: 100%;
            border-collapse: collapse;
        }

        .leads-table th,
        .leads-table td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .leads-table th {
            background: var(--light-gray);
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .leads-table tr:hover {
            background: #fafafa;
        }

        .leads-table td {
            font-size: 0.95rem;
        }

        .lead-name {
            font-weight: 600;
            color: var(--dark);
        }

        .lead-contact {
            color: #666;
            font-size: 0.85rem;
        }

        .lead-contact a {
            color: var(--primary);
            text-decoration: none;
        }

        .lead-contact a:hover {
            text-decoration: underline;
        }

        .lead-message {
            max-width: 300px;
            color: #666;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .lead-date {
            color: #999;
            font-size: 0.85rem;
        }

        .btn-delete {
            background: #fee2e2;
            color: var(--error);
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-delete:hover {
            background: var(--error);
            color: #fff;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #ddd;
        }

        .property-badge {
            display: inline-block;
            padding: 4px 10px;
            background: rgba(200, 149, 108, 0.15);
            color: var(--primary);
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .leads-table {
                font-size: 0.85rem;
            }

            .leads-table th,
            .leads-table td {
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <header class="admin-header">
        <h1><i class="fas fa-users"></i> Leads & <span>Inquiries</span></h1>
        <nav class="admin-nav">
            <a href="index.php"><i class="fas fa-arrow-left"></i> Dashboard</a>
        </nav>
    </header>

    <div class="container">
        <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> Lead deleted successfully!
            </div>
        <?php endif; ?>

        <?php if (!$dbAvailable): ?>
            <div class="alert" style="background: #fef3c7; color: #92400e; padding: 25px;">
                <h3 style="margin-bottom: 10px;"><i class="fas fa-database"></i> Database Not Connected</h3>
                <p style="margin: 0;">The MySQL database is not available. Please ensure:</p>
                <ul style="margin: 10px 0 0 20px;">
                    <li>XAMPP MySQL service is running</li>
                    <li>Database "kvwoodworks" exists in phpMyAdmin</li>
                    <li>Required tables (contacts, estimates, popup_leads) are created</li>
                </ul>
            </div>
        <?php endif; ?>

        <div class="page-header">
            <h2>All Form Submissions</h2>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <a href="?tab=contacts" class="tab <?php echo $activeTab === 'contacts' ? 'active' : ''; ?>">
                <i class="fas fa-envelope"></i> Contact Forms
                <span class="badge">
                    <?php echo count($contacts); ?>
                </span>
            </a>
            <a href="?tab=estimates" class="tab <?php echo $activeTab === 'estimates' ? 'active' : ''; ?>">
                <i class="fas fa-calculator"></i> Estimate Requests
                <span class="badge">
                    <?php echo count($estimates); ?>
                </span>
            </a>
            <a href="?tab=popup" class="tab <?php echo $activeTab === 'popup' ? 'active' : ''; ?>">
                <i class="fas fa-bell"></i> Popup Leads
                <span class="badge">
                    <?php echo count($popupLeads); ?>
                </span>
            </a>
        </div>

        <?php if ($activeTab === 'contacts'): ?>
            <!-- Contact Forms -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-envelope"></i> Contact Form Submissions</h3>
                </div>
                <?php if (empty($contacts)): ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>No contact form submissions yet.</p>
                    </div>
                <?php else: ?>
                    <table class="leads-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contacts as $contact): ?>
                                <tr>
                                    <td class="lead-name">
                                        <?php echo htmlspecialchars($contact['name']); ?>
                                    </td>
                                    <td class="lead-contact">
                                        <a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>">
                                            <?php echo htmlspecialchars($contact['email']); ?>
                                        </a>
                                        <?php if (!empty($contact['phone'])): ?>
                                            <br><a href="tel:<?php echo htmlspecialchars($contact['phone']); ?>">
                                                <?php echo htmlspecialchars($contact['phone']); ?>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($contact['subject'] ?? 'General'); ?>
                                    </td>
                                    <td class="lead-message">
                                        <?php echo htmlspecialchars($contact['message'] ?? ''); ?>
                                    </td>
                                    <td class="lead-date">
                                        <?php echo date('d M Y, h:i A', strtotime($contact['created_at'])); ?>
                                    </td>
                                    <td>
                                        <form method="POST" style="display: inline;"
                                            onsubmit="return confirm('Delete this lead?');">
                                            <input type="hidden" name="lead_id" value="<?php echo $contact['id']; ?>">
                                            <input type="hidden" name="lead_type" value="contact">
                                            <button type="submit" name="delete_lead" class="btn-delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($activeTab === 'estimates'): ?>
            <!-- Estimate Requests -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-calculator"></i> Estimate Requests</h3>
                </div>
                <?php if (empty($estimates)): ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>No estimate requests yet.</p>
                    </div>
                <?php else: ?>
                    <table class="leads-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Property</th>
                                <th>Location</th>
                                <th>Budget</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($estimates as $estimate): ?>
                                <tr>
                                    <td class="lead-name">
                                        <?php echo htmlspecialchars($estimate['name']); ?>
                                    </td>
                                    <td class="lead-contact">
                                        <a href="mailto:<?php echo htmlspecialchars($estimate['email']); ?>">
                                            <?php echo htmlspecialchars($estimate['email']); ?>
                                        </a>
                                        <br><a href="tel:<?php echo htmlspecialchars($estimate['phone']); ?>">
                                            <?php echo htmlspecialchars($estimate['phone']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="property-badge">
                                            <?php echo htmlspecialchars($estimate['property_type'] ?? ''); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($estimate['location'] ?? ''); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($estimate['budget'] ?? ''); ?>
                                    </td>
                                    <td class="lead-date">
                                        <?php echo date('d M Y, h:i A', strtotime($estimate['created_at'])); ?>
                                    </td>
                                    <td>
                                        <form method="POST" style="display: inline;"
                                            onsubmit="return confirm('Delete this lead?');">
                                            <input type="hidden" name="lead_id" value="<?php echo $estimate['id']; ?>">
                                            <input type="hidden" name="lead_type" value="estimate">
                                            <button type="submit" name="delete_lead" class="btn-delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($activeTab === 'popup'): ?>
            <!-- Popup Leads -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-bell"></i> Popup Form Leads</h3>
                </div>
                <?php if (empty($popupLeads)): ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>No popup form submissions yet.</p>
                    </div>
                <?php else: ?>
                    <table class="leads-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Property</th>
                                <th>Location</th>
                                <th>WhatsApp</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($popupLeads as $lead): ?>
                                <tr>
                                    <td class="lead-name">
                                        <?php echo htmlspecialchars($lead['name']); ?>
                                    </td>
                                    <td class="lead-contact">
                                        <a href="tel:<?php echo htmlspecialchars($lead['phone']); ?>">
                                            <?php echo htmlspecialchars($lead['phone']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="property-badge">
                                            <?php echo htmlspecialchars($lead['property_type'] ?? ''); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($lead['location'] ?? ''); ?>
                                    </td>
                                    <td>
                                        <?php echo !empty($lead['whatsapp_updates']) ? '✅ Yes' : '❌ No'; ?>
                                    </td>
                                    <td class="lead-date">
                                        <?php echo date('d M Y, h:i A', strtotime($lead['created_at'])); ?>
                                    </td>
                                    <td>
                                        <form method="POST" style="display: inline;"
                                            onsubmit="return confirm('Delete this lead?');">
                                            <input type="hidden" name="lead_id" value="<?php echo $lead['id']; ?>">
                                            <input type="hidden" name="lead_type" value="popup">
                                            <button type="submit" name="delete_lead" class="btn-delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>