<?php
// Infos de connexion InfinityFree
$host = "sql304.infinityfree.com";       // Serveur MySQL
$db_name = "if0_41316843_cvgenpro";      // Remplace XXX par le nom réel de ta DB
$db_user = "if0_41316843";               // Nom d’utilisateur MySQL
$db_pass = "Ghislain2009";               // Mot de passe MySQL

// Connexion PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connexion réussie"; // Décommenter pour tester
} catch(PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>