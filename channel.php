<?php
session_start();
// Connexion à la base de données
$pdo = new PDO("mysql:host=localhost;dbname=admin_videos", "gwada", "&?p)W]ex?-R/57m");

// Récupération du nom d'utilisateur à partir de l'URL
if(isset($_GET['username'])) {
    $username = $_GET['username'];
} else {
    echo "Nom d'utilisateur non spécifié";
    exit();
}

// Requête pour récupérer les informations de l'utilisateur
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérification si l'utilisateur existe
if(!$user) {
    echo "Utilisateur non trouvé";
    exit();
}

// Requête pour récupérer le nombre d'abonnements de l'utilisateur
$sql = "SELECT COUNT(*) FROM subscriptions WHERE user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user['id']]);
$subscription_count = $stmt->fetchColumn();

// Traitement de l'abonnement
if(isset($_POST['subscribe'])) {
    // Vérification si l'utilisateur est déjà abonné
    $sql = "SELECT * FROM subscriptions WHERE user_id = ? AND subscriber_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user['id'], $_SESSION['user_id']]);
    $subscription = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$subscription) {
        // Ajout de l'abonnement dans la base de données
        $sql = "INSERT INTO subscriptions (user_id, subscriber_id) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user['id'], $_SESSION['user_id']]);
    }
} elseif(isset($_POST['unsubscribe'])) {
    // Suppression de l'abonnement dans la base de données
    $sql = "DELETE FROM subscriptions WHERE user_id = ? AND subscriber_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user['id'], $_SESSION['user_id']]);
}

// Vérification si l'utilisateur est abonné
$sql = "SELECT * FROM subscriptions WHERE user_id = ? AND subscriber_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user['id'], $_SESSION['user_id']]);
$subscription = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>


<!DOCTYPE html>
<html>
<head>
    <title><?= $user['username'] ?> - Chaine</title>
</head>
<body>
    <h1><?= $user['username'] ?> - Chaine</h1>
    <ul>
        <li><img style="width: 100px; height: 100px; border-radius: 50%;" src="upload-pfp/<?= $user['profile_picture'] ?>"> <?= $user['username'] ?></li>
        <li>Date de création du compte : <?= $user['created_at'] ?></li>
        <li>Nombre d'abonnements : <?= $subscription_count ?></li>
    </ul>

    <form method="POST">
        <?php if($subscription): ?>
            <button name="unsubscribe">Se désabonner</button>
        <?php else: ?>
            <button name="subscribe">S'abonner</button>
        <?php endif; ?>
    </form>
</body>
</html>
