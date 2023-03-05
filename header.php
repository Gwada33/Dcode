<?php

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

session_start();

$username = $_SESSION['username'];

?>

<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/navbar.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
.material-symbols-outlined {
  font-variation-settings:
  'FILL' 0,
  'wght' 800,
  'GRAD' 0,
  'opsz' 48
}
</style>
</head>
<body>
<nav>
  <ul>
    <li><a href="index.php">Accueil</a></li>
    <li>
    <div class="search-container">
  <form action="/search">
    <input type="text" placeholder="Recherche" name="search">
    <button type="submit"><span class="material-symbols-outlined">search</span></button>
  </form>
</div>

<?php if(isset($_SESSION['username'])) { ?>

    <li><a href="upload.php">Upload</a></li>
    <li><a href="profil.php">Mon compte</a></li>
    <li><a href="logout.php">Déconnexion</a></li>
    <?php } else { ?>
        <li><a href="login.php">Connexion</a></li>
        <li><a href="register.php">S'enregistrer</a></li>
    <?php } ?>
  </ul>
</nav>
</body>