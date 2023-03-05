<?php
session_start();

// Vérification de l'authentification de l'utilisateur
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Connexion à la base de données
$dsn = 'mysql:host=localhost;dbname=admin_videos;charset=utf8';
$username = 'gwada';
$password = '&?p)W]ex?-R/57m';

try {
    $pdo = new PDO($dsn, $username, $password);
} catch (PDOException $e) {
    die('Connexion échouée : ' . $e->getMessage());
}

// Récupération des informations de l'utilisateur
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification de la présence d'un fichier
    if (isset($_FILES['profile_picture'])) {
        $file = $_FILES['profile_picture'];
        $file_name = $file['name'];
        $file_tmp_name = $file['tmp_name'];
        $file_size = $file['size'];
        $file_error = $file['error'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Vérification de l'extension du fichier
        $allowed_exts = array('jpg', 'jpeg', 'png');
        if (in_array($file_ext, $allowed_exts)) {
            // Vérification de la taille du fichier
            $max_file_size = 1024 * 1024 * 2; // 2 MB
            if ($file_size <= $max_file_size) {
                // Traitement de l'image
             
                // Enregistrement de l'image sur le serveur
                $file_name = uniqid() . '.' . $file_ext;
                $file_path = 'upload-pfp/' . $file_name;
                
                // Mise à jour du profil de l'utilisateur
                $sql = "UPDATE users SET profile_picture = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$file_name, $user['id']]);

                // Redirection vers la page de profil
                header('Location: profil.php');
                exit;
            } else {
                $error = 'Le fichier est trop volumineux.';
            }
        } else {
            $error = 'Seuls les fichiers JPG, JPEG et PNG sont autorisés.';
        }
    } else {
        $error = 'Aucun fichier sélectionné.';
    }
}

?>