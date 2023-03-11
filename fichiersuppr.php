<?php
// Vérifier si le fichier existe

    // Connecter à la base de données
    $servername = "localhost";
    $username = "gwada";
    $password = "&?p)W]ex?-R/57m";
    $dbname = "admin_videos";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérifier si le fichier est référencé dans la base de données
    // Récupérer tous les fichiers dans le dossier d'upload
$dir = 'thumbnails/';
$files = scandir($dir);

// Parcourir tous les fichiers
foreach ($files as $file) {
    // Ignorer les fichiers '.' et '..'
    if ($file == '.' || $file == '..') {
        continue;
    }

    // Vérifier si le fichier existe dans la base de données
    $query = "SELECT * FROM videos WHERE thumbnail = '$file'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 0) {
        // Le fichier n'existe pas dans la base de données, on le supprime
        unlink($dir . $file);
    }
}

// Fermer la connexion à la base de données
mysqli_close($conn);

?>
