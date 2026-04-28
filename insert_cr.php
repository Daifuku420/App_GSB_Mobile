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

    $matricule = $data['matricule'] ?? null;
    $pra_num = $data['pra_num'] ?? null;
    $rap_date = $data['rap_date'] ?? null;
    $rap_motif = $data['rap_motif'] ?? null;
    $rap_bilan = $data['rap_bilan'] ?? null;
    $echantillons = $data['echantillons'] ?? [];

    if (!$matricule || !$pra_num || !$rap_date || !$rap_motif || !$rap_bilan) {
        http_response_code(400);
        echo json_encode(["status" => 400, "message" => "Missing required fields (matricule, pra_num, rap_date, rap_motif, rap_bilan)."]);
        exit();
    }

    $stmt_check_visiteur = $pdo->prepare("SELECT COUNT(*) FROM VISITEUR WHERE vis_matricule = ?");
    $stmt_check_visiteur->execute([$matricule]);
    if ($stmt_check_visiteur->fetchColumn() == 0) {
        http_response_code(404);
        echo json_encode(["status" => 404, "message" => "Error: The provided visitor matricule does not exist."]);
        exit();
    }

    $stmt_check_praticien = $pdo->prepare("SELECT COUNT(*) FROM PRATICIEN WHERE pra_num = ?");
    $stmt_check_praticien->execute([$pra_num]);
    if ($stmt_check_praticien->fetchColumn() == 0) {
        http_response_code(404);
        echo json_encode(["status" => 404, "message" => "Error: The provided practitioner number does not exist."]);
        exit();
    }

    $pdo->beginTransaction();

    $stmt_rapport = $pdo->prepare(
        "INSERT INTO RAPPORT_VISITE (vis_matricule, pra_num, rap_date, rap_bilan, rap_motif) 
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmt_rapport->execute([$matricule, $pra_num, $rap_date, $rap_bilan, $rap_motif]);

    $last_rap_num = $pdo->lastInsertId();

    if (!empty($echantillons) && is_array($echantillons)) {
        $stmt_offrir = $pdo->prepare(
            "INSERT INTO OFFRIR (rap_num, med_depotlegal, off_qte) 
             VALUES (?, ?, ?)"
        );
        $stmt_check_medicament = $pdo->prepare("SELECT COUNT(*) FROM MEDICAMENT WHERE med_depotlegal = ?");

        foreach ($echantillons as $echantillon) {
            $med_depotlegal = $echantillon['med_depotlegal'] ?? null;
            $quantite = $echantillon['quantite'] ?? 0;

            if ($med_depotlegal && is_numeric($quantite) && $quantite > 0) {
                $stmt_check_medicament->execute([$med_depotlegal]);
                if ($stmt_check_medicament->fetchColumn() == 0) {
                    $pdo->rollBack();
                    http_response_code(400);
                    echo json_encode(["status" => 400, "message" => "Invalid data: Medication with ID '{$med_depotlegal}' does not exist."]);
                    exit();
                }

                $stmt_offrir->execute([$last_rap_num, $med_depotlegal, $quantite]);
            }
        }
    }

    $pdo->commit();
    http_response_code(201);
    echo json_encode(["status" => 201, "message" => "Visit report created successfully.", "rap_num" => $last_rap_num]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(["status" => 500, "message" => "An error occurred: " . $e->getMessage()]);
}