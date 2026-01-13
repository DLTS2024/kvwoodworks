<?php
/**
 * Handle Popup Form Submission via AJAX
 */
header('Content-Type: application/json');

require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$name = sanitize($_POST['name'] ?? '');
$phone = sanitize($_POST['phone'] ?? '');
$propertyType = sanitize($_POST['property_type'] ?? '');
$location = sanitize($_POST['location'] ?? ''); // Map to city
$whatsappUpdates = isset($_POST['whatsapp_updates']) ? 'Yes' : 'No';

if (!$name || !$phone) {
    echo json_encode(['success' => false, 'message' => 'Name and Phone are required']);
    exit;
}

// 1. Try Database Insert (estimate_requests)
try {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO estimate_requests (name, phone, city, property_type, message) VALUES (?, ?, ?, ?, ?)");
    $msgContent = "Popup Query (WhatsApp Updates: $whatsappUpdates)";
    $stmt->execute([$name, $phone, $location, $propertyType, $msgContent]);

    file_put_contents('d:/website/KVwoodworks/trace_log.txt', date('H:i:s') . " - Popup DB Success\n", FILE_APPEND);
} catch (Exception $e) {
    file_put_contents('d:/website/KVwoodworks/trace_log.txt', date('H:i:s') . " - Popup DB ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
    // Non-fatal
}

// 2. Send Telegram Notification
$msg = "🎉 <b>New Popup Consultation Request</b>\n\n";
$msg .= "👤 <b>Name:</b> " . htmlspecialchars($name) . "\n";
$msg .= "📱 <b>Phone:</b> " . htmlspecialchars($phone) . "\n";
$msg .= "🏠 <b>Property:</b> " . htmlspecialchars($propertyType) . "\n";
$msg .= "📍 <b>Location:</b> " . htmlspecialchars($location) . "\n";
$msg .= "✅ <b>WhatsApp Updates:</b> " . $whatsappUpdates;

$tgResult = sendTelegram($msg);
file_put_contents('d:/website/KVwoodworks/trace_log.txt', date('H:i:s') . " - Popup Telegram: " . ($tgResult ? 'OK' : 'FAIL') . "\n", FILE_APPEND);

echo json_encode(['success' => true, 'message' => 'Request submitted successfully']);
?>