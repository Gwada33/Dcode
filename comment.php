<?php
session_start();

// Connexion à la base de données
$dsn = 'mysql:host=localhost;dbname=admin_videos;charset=utf8';
$username = 'gwada';
$password = '&?p)W]ex?-R/57m';

try {
    $pdo = new PDO($dsn, $username, $password);
} catch (PDOException $e) {
    die('Connexion échouée : ' . $e->getMessage());
}


if (isset($_POST['comment']) && !empty($_POST['comment']) && isset($_SESSION['user_id']) && isset($_GET['id'])) {
    $comment = $_POST['comment'];
    $comment = nl2br($comment);
    $user_id = $_SESSION['user_id'];
    $video_id = $_GET['id'];
    $username = $_SESSION['username'];

    $sql = "INSERT INTO comments (video_id, user_id, comment, username) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$video_id, $user_id, $comment, $username]);

    header('Location: watch?id='. $video_id);
}

?>


