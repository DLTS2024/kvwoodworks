<?php
/**
 * KV Wood Works - Database Setup Script
 * Run this file once to create all required database tables
 */

require_once __DIR__ . '/config/database.php';

// Check if running from browser
$isBrowser = php_sapi_name() !== 'cli';
if ($isBrowser) {
    echo '<!DOCTYPE html><html><head><title>Database Setup</title>';
    echo '<style>body{font-family:Arial,sans-serif;padding:40px;max-width:800px;margin:auto;background:#f5f5f5;}';
    echo '.card{background:#fff;padding:30px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}';
    echo 'h1{color:#1a1a1a;}.success{color:#22c55e;}.error{color:#ef4444;}.info{color:#3b82f6;}';
    echo 'pre{background:#f0f0f0;padding:15px;border-radius:8px;overflow-x:auto;}';
    echo '.btn{display:inline-block;padding:12px 24px;background:#c8956c;color:#fff;text-decoration:none;border-radius:8px;margin-top:20px;}';
    echo '</style></head><body><div class="card">';
}

function output($message, $class = '')
{
    global $isBrowser;
    if ($isBrowser) {
        echo "<p class='$class'>$message</p>";
    } else {
        echo strip_tags($message) . "\n";
    }
}

output("<h1>🔧 KV Wood Works Database Setup</h1>");

try {
    $db = getDB();
    output("✅ Connected to database successfully!", "success");
} catch (Exception $e) {
    output("❌ Database connection failed: " . $e->getMessage(), "error");
    output("<strong>Please ensure:</strong><br>
        1. XAMPP MySQL is running<br>
        2. Create a database called 'kvwoodworks' in phpMyAdmin<br>
        3. Update credentials in config/database.php if needed", "info");

    if ($isBrowser) {
        echo '<a href="http://localhost/phpmyadmin" class="btn" target="_blank">Open phpMyAdmin</a>';
        echo '</div></body></html>';
    }
    exit;
}

// SQL statements to create tables
$tables = [
    'contacts' => "
        CREATE TABLE IF NOT EXISTS contacts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(20),
            subject VARCHAR(255),
            message TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ",

    'estimates' => "
        CREATE TABLE IF NOT EXISTS estimates (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            property_type VARCHAR(100),
            location VARCHAR(255),
            budget VARCHAR(100),
            timeline VARCHAR(100),
            requirements TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ",

    'popup_leads' => "
        CREATE TABLE IF NOT EXISTS popup_leads (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            property_type VARCHAR(100),
            location VARCHAR(255),
            whatsapp_updates TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_phone (phone),
            INDEX idx_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ",

    'newsletter_subscribers' => "
        CREATE TABLE IF NOT EXISTS newsletter_subscribers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status ENUM('active', 'unsubscribed') DEFAULT 'active',
            INDEX idx_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    "
];

output("<h2>Creating Tables...</h2>");

$success = 0;
$failed = 0;

foreach ($tables as $tableName => $sql) {
    try {
        $db->exec($sql);
        output("✅ Table <strong>$tableName</strong> created/verified", "success");
        $success++;
    } catch (PDOException $e) {
        output("❌ Failed to create <strong>$tableName</strong>: " . $e->getMessage(), "error");
        $failed++;
    }
}

output("<hr>");
output("<h2>Summary</h2>");
output("✅ <strong>$success</strong> tables created/verified successfully", "success");
if ($failed > 0) {
    output("❌ <strong>$failed</strong> tables failed", "error");
}

output("<h2>Next Steps</h2>");
output("1. Delete this file (install.php) for security<br>
        2. Your forms will now save data to the database<br>
        3. View leads at: <a href='admin/leads.php'>Admin → Leads</a>", "info");

if ($isBrowser) {
    echo '<a href="admin/leads.php" class="btn">Go to Leads Dashboard</a>';
    echo '</div></body></html>';
}
?>