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

    $stmt_praticiens = $pdo->query("
        SELECT 
            p.pra_num,
            p.pra_nom, 
            p.pra_prenom,
            p.pra_adresse,
            p.pra_ville,
            p.pra_cp,
            p.pra_coefconfiance,
            tp.typ_libelle
        FROM PRATICIEN p
        LEFT JOIN TYPE_PRATICIEN tp ON p.typ_code = tp.typ_code
        ORDER BY p.pra_nom, p.pra_prenom
    ");
    $praticiens = $stmt_praticiens->fetchAll(PDO::FETCH_ASSOC);
    $stmt_medicaments = $pdo->query("
        SELECT 
            m.med_depotlegal,
            m.med_nomcommercial,
            m.med_composition,
            m.med_effets,
            m.med_contreindic,
            f.fam_libelle
        FROM MEDICAMENT m
        LEFT JOIN FAMILLE f ON m.fam_code = f.fam_code
        ORDER BY m.med_nomcommercial
    ");
    $medicaments = $stmt_medicaments->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => 200,
        "data" => [
            "praticiens" => $praticiens,
            "medicaments" => $medicaments
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => 500, "message" => "Database Error: " . $e->getMessage()]);
}
