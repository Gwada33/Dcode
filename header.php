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
    <li><a class="home" href="index"><img src="svg/logo.svg"></a></li>
    <li><a href="index"><img class="home-icon" src="svg/home-icon.svg"></a></li>
    <div class="search-container">
    <form action="search.php" method="GET">
        <input class="text-search" type="text" name="query" placeholder="Recherche..."><button class="icon-text-search" type="submit"><img src="svg/search-icon.svg"></button></input>
      </form>
</div>

<?php if(isset($_SESSION['username'])) { ?>

    <li><a href="upload"><img class="upload-icon" src="svg/upload-icon.svg"></a></li>
    <li><a href="profil"><img class="profil-img" style="width: 40px; height: 40px; border-radius: 50%;" src="upload-pfp/<?= $user['profile_picture'] ?>"></a></li>
    <li><a class="profil-username" href="profil"><?= $username ?></a></li>
    <form action="search.php" method="GET">
        <button class="text-search-mobile" type="submit"><img src="svg/search-icon.svg"></button>
    </form>
    <?php } else { ?>
        <li><a href="login">Connexion</a></li>
        <li><a href="register">S'enregistrer</a></li>
    <?php } ?>
  </ul>
</nav>
<script src="https://unpkg.com/split-type@0.3.3/umd/index.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>

<script>
    const logo = new
</script>

</body>