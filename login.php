<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

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

    $matricule = $data['matricule'] ?? null;
    $password = $data['password'] ?? null;

    if ($matricule && $password) {
        $stmt = $pdo->prepare("SELECT * FROM VISITEUR WHERE VIS_MATRICULE = ?");
        $stmt->execute([$matricule]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userData && password_verify($password, $userData['VIS_PASSWORD'])) {
            echo json_encode([
                "status" => 200,
                "matricule" => $userData['VIS_MATRICULE'],
                "firstName" => $userData['VIS_PRENOM'],
                "lastName" => $userData['VIS_NOM'],
                "role" => $userData['VIS_ROLE'],
                "region" => $userData['REG_CODE'] ?? "Non renseigné"
            ]);
        } else {
            echo json_encode(["status" => 401, "message" => "Login failed"]);
        }
    } else {
        echo json_encode(["status" => 400, "message" => "Missing credentials"]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => 500, "message" => "DB Error: " . $e->getMessage()]);
}