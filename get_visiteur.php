<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$host = 'paulpax1.mysql.db';
$port = '3306';
$db = 'paulpax1';
$user = 'paulpax1';
$pass = 'GsbExam2026';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => 405, "message" => "Method Not Allowed"]);
    exit();
}

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $requester_matricule = $data['matricule'] ?? null;

    if (!$requester_matricule) {
        http_response_code(400);
        echo json_encode(["status" => 400, "message" => "Missing required field: matricule."]);
        exit();
    }

    $stmt_role = $pdo->prepare("SELECT VIS_ROLE FROM VISITEUR WHERE VIS_MATRICULE = ?");
    $stmt_role->execute([$requester_matricule]);
    $requester = $stmt_role->fetch(PDO::FETCH_ASSOC);

    if (!$requester || $requester['VIS_ROLE'] !== 'responsable de secteur') {
        http_response_code(403);
        echo json_encode(["status" => 403, "message" => "Accès non autorisé. Seuls les responsables de secteur peuvent accéder à ces informations."]);
        exit();
    }

    $stmt = $pdo->prepare(
        "SELECT VIS_MATRICULE, VIS_NOM, VIS_PRENOM, VIS_ADRESSE, VIS_CP, VIS_VILLE, VIS_DATEEMBAUCHE, SEC_CODE, DEP_CODE, VIS_ROLE, REG_CODE
         FROM VISITEUR ORDER BY VIS_NOM, VIS_PRENOM"
    );
    $stmt->execute();
    $visitorsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    http_response_code(200);
    echo json_encode(["status" => 200, "data" => $visitorsData]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => 500, "message" => "Database Error: " . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => 500, "message" => "An unexpected error occurred: " . $e->getMessage()]);
}