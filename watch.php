<?php

session_start();

// Connexion à la base de données
$dsn = 'mysql:host=localhost;dbname=admin_videos;charset=utf8';
$username = 'gwada';
$password = '&?p)W]ex?-R/57m';

try {
    $pdo = new PDO($dsn, $username, $password);
} catch (PDOException $e) {
    die();
}

// Récupération de l'ID de la vidéo depuis l'URL
$id = isset($_GET['id']) ? $_GET['id'] : null;

// Vérification que l'ID de la vidéo est présent dans l'URL
if (!$id) {
    die("L'identifiant de la vidéo est manquant dans l'URL.");
}

// Vérification que la vidéo existe
$sql = "SELECT * FROM videos WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$video = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$video) {
    die("La vidéo demandée n'existe pas.");
}

// Récupération de l'ID de l'utilisateur connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
}

$user_id = $_SESSION['user_id'];



if(isset($_POST['comment'])) {
    $comment = $_POST['comment'];
    $video_id = $_POST['video_id'];
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];

    // Insérer le commentaire dans la base de données
    $sql = "INSERT INTO comments (comment, user_id, username, video_id) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$comment, $user_id, $username, $video_id]);
}

// Vérifier si le formulaire de réponse est soumis
if(isset($_POST['reply']) && isset($_POST['comment_id']) && isset($_POST['parent_id'])) {
    $reply = $_POST['reply'];
    $comment_id = $_POST['comment_id'];
    $parent_id = $_POST['parent_id'];
    $video_id = $_POST['video_id'];
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];

    $sql = "INSERT INTO comment_replies (comment_id, user_id, reply, username, parent_id) VALUES (?, ?, ?, ?, ?)";
    $stmt= $pdo->prepare($sql);
    $stmt->execute([$comment_id, $user_id, $reply, $username, $parent_id]);

    header("Location: watch.php?id=$video_id");
    exit();
}



// Récupérer les commentaires de la vidéo
$sql = "SELECT c.*, u.username, cr.reply, cr.user_id FROM comments c 
LEFT JOIN users u ON c.user_id = u.id 
LEFT JOIN comment_replies cr ON c.id = cr.comment_id 
WHERE c.video_id = ? 
ORDER BY c.created_at DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$video['id']]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);



?>

<?php include 'header.php'; ?>




<!DOCTYPE html>
<html>
<head>
	<title><?= $video['title'] ?></title>
	<!-- Inclusion de CSS pour le style -->
	<link rel="stylesheet" type="text/css" href="css/watch.css">
	<link href="https://vjs.zencdn.net/8.0.4/video-js.css" rel="stylesheet" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/videojs-seek-buttons/dist/videojs-seek-buttons.css">
</head>
<body>
	<h1><?= $video['title'] ?></h1>
<!-- Affichage de la vidéo -->
<div class="video-container">
	<video
	id="my-video"
	class="video-js"
	controls
	preload="auto"
	width="1000px"
	autoplay
	poster="thumbnails/<?= $video['thumbnail'] ?>" alt="<?= $video['title'] ?>"
	data-setup="{}">
	<source  src="videos/<?= $video['filename']?>"></source>
	<p><?= $video['description'] ?></p>
</video>
</div>

<div class="views-container">
  <ul>
    <?php
    // Récupérer l'ID de la vidéo depuis l'URL
    $id = $_GET['id'] ?? null;
    
    if ($id) {
        // Ajouter la vue dans la base de données
        $stmt = $pdo->prepare("INSERT INTO views (user_id, video_id) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $id]);
        
        // Récupérer le nombre de vues pour cette vidéo
        $sql = "SELECT COUNT(v.id) AS views_count FROM users u LEFT JOIN views v ON u.id = v.user_id WHERE v.video_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $views = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Afficher le nombre de vues
        echo ("<h2>" . $views['views_count'] . " vues</h2>");
    } else {
        echo "Impossible de récupérer l'ID de la vidéo";
    }
    ?>
  </ul>
</div>





<!-- Formulaire pour ajouter un commentaire -->
<div class="comment-section">
    <h2>Commentaires</h2>
    <?php if(isset($_SESSION['username'])) { ?>
    <form action="watch?id=<?= $video['id']?>" method="post">
        <textarea name="comment" placeholder="Votre commentaire" required></textarea>
        <input type="hidden" name="video_id" value="<?= $video['id'] ?>">
        <input type="hidden" name="parent_id" value="0">
        <input type="submit" value="Envoyer">
    </form>
    <?php } else { ?>
    <p>Pour ajouter un commentaire, <a href="login">veuillez vous connecter</a>.</p>
    <?php } ?>
    <div class="comments">
        <?php
        // Récupérer les commentaires
        $sql = "SELECT c.*, u.username FROM comments c LEFT JOIN users u ON c.user_id = u.id WHERE c.video_id = ? AND c.parent_id IS NULL ORDER BY c.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$video['id']]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(!empty($comments)) {
            foreach ($comments as $comment) { ?>
            <div class="comment">
                <a href="channel?username=<?= $comment['username'] ?>"> <?= isset($comment['username']) ? $comment['username'] : "Utilisateur inconnu" ?></a>
                <p><?= $comment['comment'] ?></p>
                <?php 
                    // Récupérer les réponses du commentaire
                    $sql = "SELECT cr.*, u.username FROM comment_replies cr LEFT JOIN users u ON cr.user_id = u.id WHERE cr.comment_id = ? ORDER BY cr.created_at ASC";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$comment['id']]);
                    $replies = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <div class="replies">
                    <?php if(!empty($replies)) { ?>
                    <?php foreach ($replies as $reply) { ?>
                    <div class="reply">
                        <h4><?= isset($reply['username']) ? $reply['username'] : "Utilisateur inconnu" ?></h4>
                        <p><?= $reply['reply'] ?></p>
                    </div>
                    <?php } ?>
                    <?php } else { ?>
                    <p>Aucune réponse pour le moment</p>
                    <?php } ?>
                </div>
                <?php if(isset($_SESSION['username'])) { ?>
                <form action="watch?id=<?= $video['id']?>" method="post">
                    <textarea name="reply" placeholder="Votre réponse" required></textarea>
                    <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                    <input type="hidden" name="video_id" value="<?= $video['id'] ?>">
                    <input type="hidden" name="parent_id" value="<?= $comment['id'] ?>">
                    <input type="submit" value="Répondre">
                </form>
                <?php } else { ?>
                <p>Pour répondre, <a href="login">veuillez vous connecter</a>.</p>
                <?php } ?>
            </div>
            <?php } ?>
        <?php } else { ?>
        <p>Aucun commentaire pour le moment</p>
        <?php } ?>
    </div>
</div>







<!-- Inclusion de JavaScript pour la vidéo -->
<script src="https://vjs.zencdn.net/8.0.4/video.min.js"></script>
</body>
</html>
