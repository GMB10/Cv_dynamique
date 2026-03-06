<?php
require_once 'db.php'; // Connexion PDO

header('Content-Type: application/json');

if(!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])){
    echo json_encode([]);
    exit;
}

$user_id = intval($_GET['user_id']);

try {
    $stmt = $pdo->prepare("SELECT * FROM cvs WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $cvs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($cvs);

} catch(PDOException $e){
    echo json_encode(['error' => 'Erreur lors de la récupération des CV : ' . $e->getMessage()]);
}
?>