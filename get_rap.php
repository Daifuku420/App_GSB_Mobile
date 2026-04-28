<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Api\ReportHandler;

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Il est recommandé de déplacer ces identifiants dans un fichier de configuration non versionné.
$host = 'paulpax1.mysql.db';
$port = '3306';
$db = 'paulpax1';
$user = 'paulpax1';
$pass = 'GsbExam2026';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $handler = new ReportHandler($pdo);
    $reports = $handler->getReportsByMatricule($data['matricule'] ?? null);

    echo json_encode([
        "status" => 200,
        "data" => $reports
    ]);

} catch (InvalidArgumentException $e) {
    // Utilise le code de l'exception pour la réponse HTTP
    $responseCode = is_int($e->getCode()) && $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 400;
    http_response_code($responseCode);
    echo json_encode(["status" => $responseCode, "message" => $e->getMessage()]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => 500, "message" => "Database Error: " . $e->getMessage()]);
}
