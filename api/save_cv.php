<?php
session_start();
require_once("db.php"); // Connexion PDO

header('Content-Type: application/json');

// ==============================================
// Vérification connexion utilisateur
// ==============================================
if(!isset($_SESSION['user_id'])){
    echo json_encode([
        'success' => false,
        'message' => 'Utilisateur non connecté.'
    ]);
    exit;
}

// ==============================================
// Récupération et validation des données
// ==============================================
$data = json_decode(file_get_contents('php://input'), true);

$user_id  = $_SESSION['user_id'];
$template = trim($data['template'] ?? '');
$content  = trim($data['content'] ?? '');
$photo    = $data['photo'] ?? null; // base64 si fournie

if(empty($template) || empty($content)){
    echo json_encode([
        'success' => false,
        'message' => 'Le template et le contenu du CV sont requis.'
    ]);
    exit;
}

// Optionnel : limiter la taille de la photo (ex: 2MB en base64)
if($photo && strlen($photo) > 2 * 1024 * 1024){
    echo json_encode([
        'success' => false,
        'message' => 'La photo est trop volumineuse.'
    ]);
    exit;
}

// ==============================================
// Insertion dans la base
// ==============================================
try {
    $stmt = $pdo->prepare("
        INSERT INTO cvs (user_id, template, content, photo, created_at)
        VALUES (:user_id, :template, :content, :photo, NOW())
    ");
    
    $stmt->execute([
        ':user_id'  => $user_id,
        ':template' => $template,
        ':content'  => $content,
        ':photo'    => $photo
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'CV sauvegardé avec succès !'
    ]);

} catch(PDOException $e){
    echo json_encode([
        'success' => false,
        'message' => 'Erreur SQL : '.$e->getMessage()
    ]);
}
?>