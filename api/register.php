<?php
// register.php
header('Content-Type: application/json');
require_once 'config.php';

// Activer l'affichage des erreurs pour debug (à désactiver en production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$data = json_decode(file_get_contents('php://input'), true);

// Vérifier que tous les champs sont remplis
if(empty($data['fullname']) || empty($data['email']) || empty($data['password'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Nom, Email et Mot de passe requis.'
    ]);
    exit;
}

$fullname = trim($data['fullname']);
$email    = trim($data['email']);
$password = trim($data['password']);

// Vérifier le format de l'email
if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    echo json_encode([
        'success' => false,
        'message' => 'Adresse email invalide.'
    ]);
    exit;
}

// Vérifier longueur du mot de passe
if(strlen($password) < 6){
    echo json_encode([
        'success' => false,
        'message' => 'Le mot de passe doit contenir au moins 6 caractères.'
    ]);
    exit;
}

// Vérifier que l'email n'existe pas déjà
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if($stmt->rowCount() > 0){
    echo json_encode([
        'success' => false,
        'message' => 'Cet email est déjà utilisé.'
    ]);
    exit;
}

// Hasher le mot de passe
$hash = hash('sha256', $password);

// Insérer l'utilisateur
try {
    $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, 'user')");
    $stmt->execute([$fullname, $email, $hash]);

    echo json_encode([
        'success' => true,
        'message' => 'Inscription réussie ! Vous pouvez maintenant vous connecter.'
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de l\'inscription : ' . $e->getMessage()
    ]);
}
?>