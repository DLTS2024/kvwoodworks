<?php
/**
 * Polling API for Launch System
 * Frontend checks this every few seconds
 */
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

$configFile = __DIR__ . '/config/launch.json';
$status = 'coming_soon';

if (file_exists($configFile)) {
    $data = json_decode(file_get_contents($configFile), true);
    $status = $data['status'] ?? 'coming_soon';
}

echo json_encode(['status' => $status]);
?>