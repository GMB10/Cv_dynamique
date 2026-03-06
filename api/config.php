<?php
$host = "sql304.infinityfree.com";
$dbname = "if0_41316843_cvgenpro"; // Remplace XXX par ton suffixe réel
$user = "if0_41316843";
$pass = "Ghislain2009";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>