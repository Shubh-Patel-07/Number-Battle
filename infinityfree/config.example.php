<?php
// Copy this file to config.php and add the credentials from the InfinityFree MySQL Databases page.
const DB_HOST = 'sqlXXX.infinityfree.com';
const DB_NAME = 'if0_XXXXXXXX_database';
const DB_USER = 'if0_XXXXXXXX';
const DB_PASS = 'YOUR_VPANEL_PASSWORD';

function db(): PDO {
    static $pdo;
    if (!$pdo) {
        $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
    }
    return $pdo;
}
session_start();
function respond($data, int $status = 200): never { http_response_code($status); header('Content-Type: application/json'); echo json_encode($data); exit; }
function require_user(): array { if (empty($_SESSION['user'])) respond(['error' => 'Please sign in first.'], 401); return $_SESSION['user']; }
function room_code(): string { return strtoupper(substr(str_replace(['0','O','I','L'], '', base_convert((string)random_int(36**5, 36**6 - 1), 10, 36)), 0, 6)); }
function valid_number(string $number): bool { return (bool)preg_match('/^\d{4}$/', $number) && count(array_unique(str_split($number))) === 4; }
function score(string $secret, string $guess): array { $positions = 0; $digits = 0; for ($i=0;$i<4;$i++) { if ($secret[$i] === $guess[$i]) $positions++; if (str_contains($secret, $guess[$i])) $digits++; } return ['correctDigits'=>$digits,'correctPositions'=>$positions,'wrongDigits'=>4-$digits]; }
