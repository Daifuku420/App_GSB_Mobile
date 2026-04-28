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

    $stmt_rapports = $pdo->query("
        SELECT 
            rv.rap_num, 
            rv.rap_date, 
            rv.rap_motif, 
            rv.rap_bilan, 
            p.pra_nom, 
            p.pra_prenom,
            v.vis_matricule,
            v.reg_code
        FROM RAPPORT_VISITE rv
        JOIN PRATICIEN p ON rv.pra_num = p.pra_num
        JOIN VISITEUR v ON rv.vis_matricule = v.vis_matricule
        ORDER BY rv.rap_num ASC
    ");
    $rapports = $stmt_rapports->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => 200,
        "data" => [
            "rapports" => $rapports
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => 500, "message" => "Database Error: " . $e->getMessage()]);
}