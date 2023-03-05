<?php
// Connexion à la base de données
$pdo = new PDO("mysql:host=localhost;dbname=admin_videos", "gwada", "&?p)W]ex?-R/57m");

// Vérification que l'utilisateur est connecté
// Si non connecté, rediriger vers la page de connexion
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Récupération de l'ID de l'utilisateur et de la valeur de la checkbox
$user_id = $_POST['user_id'];
$isSubscribed = isset($_POST['subscribe']) && $_POST['subscribe'] == 'on';

// Requête pour ajouter ou supprimer un abonnement dans la table "subscriptions"
if($isSubscribed) {
    $sql = "INSERT INTO subscriptions (user_id, subscriber_id) VALUES (?, ?)";
} else {
    $sql = "DELETE FROM subscriptions WHERE user_id = ? AND subscriber_id = ?";
}
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id, $_SESSION['user_id']]);

// Rediriger vers la page de l'utilisateur
header("Location: channel?username={$username}");
exit();
?>
