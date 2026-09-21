<?php
// scr/php/config.php - Conexión centralizada a la BD lamismisimabasededatos
// XAMPP por defecto: host=127.0.0.1 / root sin pass / puerto 3306
// Si usás otro entorno, cambiá las vars o definí DB_* en variables de entorno.

declare(strict_types=1);

$DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
$DB_PORT = getenv('DB_PORT') ?: '3306';
$DB_NAME = getenv('DB_NAME') ?: 'lamismisimabasededatos';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';
$DB_CHARSET = 'utf8mb4';

$dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset={$DB_CHARSET}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
    // Asegurar utf8mb4
    $pdo->exec("SET NAMES utf8mb4");
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'ok' => false,
        'error' => 'Error de conexión a la BD. Verificá que MySQL esté corriendo y la BD lamismisimabasededatos exista.',
        'detail' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Iniciar sesión si no está iniciada - misma config para todos los endpoints
if (session_status() === PHP_SESSION_NONE) {
    // Cookies seguras para login
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_strict_mode', '1');
    session_start();
}

function json_response(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function require_method(string $method): void {
    if ($_SERVER['REQUEST_METHOD'] !== $method) {
        json_response(['ok' => false, 'error' => "Método no permitido. Se esperaba $method"], 405);
    }
}

// Helper para leer JSON o form-data
function get_input(): array {
    $ct = $_SERVER['CONTENT_TYPE'] ?? '';
    if (str_contains($ct, 'application/json')) {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }
    return $_POST;
}
