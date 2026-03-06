<?php
// login.php
header('Content-Type: application/json');
session_start(); // Toujours au tout début

require_once 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

// Vérification des champs
if(empty($data['email']) || empty($data['password'])){
    echo json_encode([
        'success' => false,
        'message' => 'Email et mot de passe requis.'
    ]);
    exit;
}

$email = trim($data['email']);
$password = trim($data['password']);
$hash = hash('sha256', $password); // SHA-256 pour correspondre à l'inscription

try {
    $stmt = $pdo->prepare("SELECT id, fullname, role FROM users WHERE email = ? AND password = ?");
    $stmt->execute([$email, $hash]);

    if($stmt->rowCount() === 1){
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['role'] = $user['role'];

        echo json_encode([
            'success' => true,
            'message' => 'Connexion réussie !',
            'user' => [
                'id' => $user['id'],
                'fullname' => $user['fullname'],
                'role' => $user['role']
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Email ou mot de passe incorrect.'
        ]);
    }

} catch(PDOException $e){
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la connexion : ' . $e->getMessage()
    ]);
}
?>