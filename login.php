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

// Vérification de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Requête pour récupérer l'utilisateur correspondant
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérification du mot de passe
    if ($user && password_verify($password, $user['password'])) {
        // Authentification réussie
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: profil.php');
        exit;
    } else {
        // Authentification échouée
        $error = 'Nom d\'utilisateur ou mot de passe incorrect.';
    }
}

?>

<?php include 'header.php'; ?>

<!DOCTYPE html>
<html>
<head>
	<title>Connexion</title>
	<!-- Inclusion de CSS pour le style -->
	<link rel="stylesheet" type="text/css" href="css/login.css">
</head>
<body>
	<h1>Connexion</h1>

	<?php if (isset($error)): ?>
		<p class="error"><?= $error ?></p>
	<?php endif ?>

	<form class="login-form" method="POST">
		<label for="username">Nom d'utilisateur :</label>
		<input type="text" name="username" required>

		<label for="password">Mot de passe :</label>
		<input type="password" name="password" required>

		<button type="submit">Se connecter</button>
	</form>

	<p>Pas encore de compte ? <a href="register.php">S'enregistrer</a></p>
</body>
</html>
