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
    if ($_POST['action'] === 'changepassword') {
        // Vérification du mot de passe actuel
        $current_password = $_POST['current_password'];
        if (!password_verify($current_password, $user['password'])) {
            $error = 'Mot de passe actuel incorrect.';
        } else {
            // Modification du mot de passe
            $new_password = $_POST['new_password'];
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$hashed_password, $user['id']]);
            $success = 'Mot de passe modifié avec succès.';
        }
    } elseif ($_POST['action'] === 'changeusername') {
        // Modification du nom d'utilisateur
        $new_username = $_POST['new_username'];
        $sql = "UPDATE users SET username = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$new_username, $user['id']]);
        $success = 'Nom d\'utilisateur modifié avec succès.';
    }
}

?>

<?php include 'header.php'; ?>


<!DOCTYPE html>
<html>
<head>
    <title>Profil</title>
    <!-- Inclusion de CSS pour le style -->
    <link rel="stylesheet" type="text/css" href="css/profil.css">
   

</head>
<body>
    <div class="profil-container">
    <h1>Profil de <?= $user['username'] ?></h1> 
    <ul>
        <li>Photo de profil : <img style="width: 100px; max-height: 100px; border-radius: 50%;" src="upload-pfp/<?= $user['profile_picture'] ?>"></li>
        <li>Nom d'utilisateur : <?= $user['username'] ?></li>
        <li>Date de création du compte : <?= $user['created_at'] ?></li>
        <li>Adresse mail : <?= $user['email'] ?></li>
    </ul>

    <?php if (isset($error)): ?>
        <p class="error"><?= $error ?></p>
    <?php endif ?>

    <?php if (isset($success)): ?>
        <p class="success"><?= $success ?></p>
    <?php endif ?>

    <h2>Changer le mot de passe</h2>
    <form method="POST">
        <input type="hidden" name="action" value="changepassword">

        <label for="current_password">Mot de passe actuel :</label>
        <input type="password" name="current_password" required>

        <label for="new_password">Nouveau mot de passe :</label>
        <input type="password" name="new_password" required>

        <button type="submit">Changer le mot de passe</button>
    </form>

    <h2>Changer le nom d'utilisateur</h2>
    <form method="POST">
        <input type="hidden" name="action" value="changeusername">

        <label for="new_username">Nouveau nom d'utilisateur :</label>
        <input type="text" name="new_username" value="<?= $user['username'] ?>" required>

        <button type="submit">Changer le nom d'utilisateur</button>
    </form>

    <form action="upload-pfp.php" method="post" enctype="multipart/form-data">
  <input type="file" id="text" name="profile_picture" id="profile_picture" accept="image/*" onchange="previewPicture(this)" required>
  <input type="submit" value="Upload">
   </form>
</div>


    <p><a href="logout">Se déconnecter</a></p>
    <script type="text/javascript" >
    // L'image img#image
    var image = document.getElementById("image");
     
    // La fonction previewPicture
    var previewPicture  = function (e) {

        // e.files contient un objet FileList
        const [picture] = e.files

        // "picture" est un objet File
        if (picture) {
            // On change l'URL de l'image
            image.src = URL.createObjectURL(picture)
        }
    } 
</script>

</body>
</html>
