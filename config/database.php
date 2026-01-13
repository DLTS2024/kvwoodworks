<?php
/**
 * Database Configuration for KV Wood Works
 * Auto-detects environment (localhost or production)
 */

// Environment Detection
$isLocalhost = (
    strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false ||
    strpos($_SERVER['HTTP_HOST'] ?? '', '127.0.0.1') !== false ||
    ($_SERVER['SERVER_NAME'] ?? '') === 'localhost'
);

// Database Configuration - Auto-detect environment
if ($isLocalhost) {
    // LOCAL DEVELOPMENT (XAMPP)
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'kvwoodworks');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('SITE_URL', 'http://localhost/KVwoodworks');
} else {
    // PRODUCTION (cPanel)
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'kvwoodwo_mainsite');
    define('DB_USER', 'kvwoodwo_admin');
    define('DB_PASS', 'Dhinesh@217');
    define('SITE_URL', 'https://kvwoodworks.in');
}

// Site Configuration (same for both environments)
define('SITE_NAME', 'KV Wood Works');
define('SITE_EMAIL', 'kvwoodworks@gmail.com');
define('SITE_PHONE', '+91 98849 72483');
define('SITE_WHATSAPP', '919884972483');
define('SITE_ADDRESS', '25, 10th, 3rd Cross St, Gangaiamman Nagar, Maduravoyal, Chennai, Tamil Nadu 600095');
define('SITE_INSTAGRAM', 'https://www.instagram.com/kv.wood.works/');

// Database Connection Class
class Database
{
    private static $instance = null;
    private $connection = null;
    private static $connectionFailed = false;

    private function __construct()
    {
        try {
            $this->connection = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            // Silently fail - pages will use static content
            self::$connectionFailed = true;
            $this->connection = null;
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        if ($this->connection === null) {
            throw new Exception("Database not available");
        }
        return $this->connection;
    }

    // Prevent cloning
    private function __clone()
    {
    }

    // Prevent unserialization
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}

// Helper function to get database connection
function getDB()
{
    return Database::getInstance()->getConnection();
}

// Helper function to sanitize input
function sanitize($input)
{
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

// Helper function to generate slugs
function generateSlug($text)
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'n-a' : $text;
}

// Get base URL helper
function baseUrl($path = '')
{
    return SITE_URL . '/' . ltrim($path, '/');
}

// Asset URL helper
function asset($path)
{
    return baseUrl('assets/' . ltrim($path, '/'));
}
// WhatsApp API Helper (Flaxxa)
// Telegram Notification Helper
function sendTelegram($message)
{
    $token = '8502177192:AAEDCHUwH85pje-fFlrwPTRffD1spmtT5m4';
    $chat_id = '8269699561';

    $url = "https://api.telegram.org/bot$token/sendMessage";

    $data = [
        'chat_id' => $chat_id,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);

    // Robust Logging
    $logMsg = "Telegram Debug: To: $chat_id | HTTP: " . $info['http_code'] . " | Error: $error | Response: $response";
    error_log($logMsg); // Writes to Apache/PHP error log

    return empty($error);
}
?>