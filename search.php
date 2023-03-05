<?php

session_start();

if(!isset($_SESSION['user_id'])) {
    header('Location: login');
    exit();
}

$host = "localhost";
$username = "gwada";
$password = "&?p)W]ex?-R/57m";
$dbname = "admin_videos";

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Configuration de l'attribut PDO pour générer des exceptions pour les erreurs
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connexion échouée : " . $e->getMessage();
}

// Vérification que le formulaire de recherche a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    // Nettoyage de la chaîne de recherche
    $search = isset($_POST['search']) ? trim($_POST['search']) : '';

    // Requête pour récupérer les vidéos correspondant au terme de recherche
    $sql = "SELECT * FROM videos WHERE title LIKE :search OR description LIKE :search";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['search' => "%$search%"]);
    $videos = $stmt->fetchAll();
} else {
    // Si le formulaire n'a pas été soumis, on affiche un message d'erreur
    $error = "Veuillez entrer un terme de recherche";
}

if (isset($videos) && count($videos) > 0) {
    // Afficher les vidéos
} else {
    echo "Aucun résultat trouvé pour votre recherche.";
}


?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche de vidéos</title>
    <link rel="stylesheet" href="css/videos.css">
</head>
<body>
    <h1>Recherche de vidéos</h1>
    <form method="post">
        <input type="text" name="search" placeholder="Entrez un terme de recherche">
        <button type="submit">Rechercher</button>
    </form>
    <?php if (isset($error)): ?>
        <p><?= $error ?></p>
    <?php elseif (isset($videos)): ?>
        <ul>
        <div class="videos-container">
    <?php foreach ($videos as $video): ?>
        <a class="video-grid-item" href="watch?id=<?= $video['id'] ?>">
            <div class="video-thumbnail">
                <img class="thumbnail" src="thumbnails/<?= $video['thumbnail'] ?>" alt="<?= $video['title'] ?>">
                <h2 class="video-title"><?= $video['title'] ?></h2>
                <p><?= $video['description'] ?></p>
            </div>
        </a>
    <?php endforeach; ?>
</div>
        </ul>
    <?php endif; ?>
</body>
</html>
