<?php

session_start();

$dsn = 'mysql:host=localhost;dbname=admin_videos;charset=utf8';
$username = 'gwada';
$password = '&?p)W]ex?-R/57m';

try {
    $pdo = new PDO($dsn, $username, $password);
} catch (PDOException $e) {
    die('Connexion échouée : ' . $e->getMessage());
}



if (isset($_COOKIE['remember_me']) && !isset($_SESSION['user_id'])) {
    // Si un cookie "se souvenir de moi" existe mais que l'utilisateur n'est pas connecté
    // on essaie de le connecter avec les informations stockées dans le cookie
    $remember_me_token = $_COOKIE['remember_me'];
    $sql = "SELECT * FROM users WHERE remember_token = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$remember_me_token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: profil.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Vérification du mot de passe
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        // Si l'utilisateur a coché la case "Se souvenir de moi"
        if (isset($_POST['remember_me'])) {
            $remember_me_token = bin2hex(random_bytes(16));
            setcookie('remember_me', $remember_me_token, time() + 86400 * 15);
            $sql = "UPDATE users SET remember_token = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$remember_me_token, $user['id']]);
        }

        header('Location: profil');
        exit;
    } else {
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

		<label>
			<input type="checkbox" name="remember_me"> Se souvenir de moi
		</label>

		<button type="submit">Se connecter</button>
	</form>

	<p>Pas encore de compte ? <a href="register">S'enregistrer</a></p>
</body>
</html>

