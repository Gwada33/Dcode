
<?php

session_start();

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
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
    echo "Connexion réussie";
} catch (PDOException $e) {
    echo "Connexion échouée : " . $e->getMessage();
}

// Vérification que le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification que le fichier a bien été uploadé
    if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
        // Génération d'un nom unique pour le fichier vidéo
        $videoName = uniqid('video_') . '.' . pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION);
        // Déplacement du fichier vidéo vers le dossier uploads
        move_uploaded_file($_FILES['video']['tmp_name'], 'videos/' . $videoName);
        // Chemin de la vidéo téléchargée

        // Vérification que le fichier thumbnail a bien été uploadé
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
            // Génération d'un nom unique pour le fichier thumbnail
            $thumbnailName = uniqid('thumbnail_') . '.' . pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
            // Déplacement du fichier thumbnail vers le dossier thumbnails
            move_uploaded_file($_FILES['thumbnail']['tmp_name'], 'thumbnails/' . $thumbnailName);
        } else {
            // Si aucun fichier thumbnail n'a été uploadé, on utilise une image par défaut
            $thumbnailName = 'default_thumbnail.jpg';
        }

        $user_id = $_SESSION['user_id'];




        // Récupération des données du formulaire
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $username = $_SESSION['username'] ?? '';
        $profilePicture = $_SESSION['profile_picture'] ?? '';

        // Requête pour insérer la vidéo dans la base de données
        $sql = "INSERT INTO videos (title, description, filename, thumbnail, date_uploaded, username, profile_picture) VALUES (:title, :description, :filename, :thumbnail, NOW(), :username, :profile_picture)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'title' => $title,
            'description' => $description,
            'filename' => $videoName,
            'thumbnail' => $thumbnailName,
            'username' => $username,
            'profile_picture' => $profilePicture
        ]);

        // Redirection vers la page d'accueil après l'ajout de la vidéo
        header('Location: index');
        exit;
    }
}
?>

<?php include('header.php') ?>



<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=7">
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Upload de vidéo</title>
    <link rel="stylesheet" type="text/css" href="css/upload.css">
</head>
<body>
	<h1 class="page-title">Upload de vidéo</h1>
	<form class="all-title" action="upload.php" method="post"  url="/upload-picture"  enctype="multipart/form-data">
     <div class="title">
		<label for="title">Titre :</label>
		<input type="text" id="title" name="title" required>
     </div>

     <div class="thumbnail">
        <label for="thumbnail">Thumbnail :</label>
        <input type="file" name="thumbnail" id="thumbnail" onchange="previewPicture(this)" required  >
        <img src="#" alt="" id="image" style="max-width: 200px; margin-top: 20px; border-radius: 10px;" >
    </div>

    <div class="description">
		<label for="description">Description :</label>
		<input type="text" id="description" name="description"></input>
    </div>

    <div class="video">
		<label for="video">Fichier vidéo :</label>
		<input type="file" id="video" name="video" onchange="previewVideo(this)" required>
        <video src="#" alt="" id="video" style="max-width: 300px; margin-top: 20px; border-radius: 10px;">
     </div>

		<input type="submit" value="Envoyer">
	</form>
</body>
</html>
