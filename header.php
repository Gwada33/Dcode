<?php

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

session_start();

$username = $_SESSION['username'];

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/navbar.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>
<body>
<nav>
  <ul>
    <li><a class="home" href="index">Acceuil</a></li>
    <li>
    <div class="search-container">
    <form action="search.php" method="GET">
        <input class="text-search" type="text" name="query" placeholder="Recherche..."><button type="submit"></button></input>
      </form>
</div>

<?php if(isset($_SESSION['username'])) { ?>

    <li><a href="upload">Upload</a></li>
    <li><a href="logout">Abonnements</a></li>
    <li><a href="profil"> <img class="profil-img" style="width: 40px; height: 40px; border-radius: 50%;" src="upload-pfp/<?= $user['profile_picture'] ?>"><h1><?=$username?></a></li>
    <?php } else { ?>
        <li><a href="login">Connexion</a></li>
        <li><a href="register">S'enregistrer</a></li>
    <?php } ?>
  </ul>
</nav>
</body>