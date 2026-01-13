<?php
/**
 * Handle Launch Action
 * Toggles website status between 'coming_soon' and 'live'
 */
require_once __DIR__ . '/config/database.php';
session_start();

// Security Check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $configFile = __DIR__ . '/config/launch.json';

    // Get requested action
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? 'launch'; // launch or reset

    $newState = ($action === 'launch') ? 'live' : 'coming_soon';

    file_put_contents($configFile, json_encode(['status' => $newState]));

    echo json_encode(['success' => true, 'status' => $newState]);
}
?>