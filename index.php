<?php


// Connexion à la base de données
$servername = "localhost";
$username = "gwada";
$password = "&?p)W]ex?-R/57m";
$dbname = "admin_videos";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}

// Requête pour récupérer toutes les vidéos de la base de données
$sql = "SELECT * FROM videos";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);


function human_time_diff($from, $to = '') {
    if (empty($to)) {
        $to = time();
    }
    $diff = abs($to - $from);
    $minute = 60;
    $hour = $minute * 60;
    $day = $hour * 24;
    $week = $day * 7;
    $month = $day * 30;
    $year = $day * 365;
    if ($diff < $minute) {
        $result = 'Il y a ' . $diff . ' seconde';
    } elseif ($diff < $hour) {
        $result = 'Il y a ' . round($diff/$minute) . ' minutes';
    } elseif ($diff < $day) {
        $result = 'Il y a ' . round($diff/$hour) . ' heures';
    } elseif ($diff < $week) {
        $result = 'Il y a ' . round($diff/$day) . ' jours';
    } elseif ($diff < $month) {
        $result = 'Il y a ' . round($diff/$week) . ' semaines';
    } elseif ($diff < $year) {
        $result = 'Il y a ' . round($diff/$month) . ' mois';
    } else {
        $result = 'Il y a ' . round($diff/$year) . ' ans';
    }
    return $result;
}


?>

<?php include 'header.php'; ?>


<!DOCTYPE html>
<html>
<head>
	<title>Ma page de vidéos</title>
	<!-- Inclusion de CSS pour le style -->
	<link rel="stylesheet"  href="css/videos.css">
</head>
<body>

<div class="background">
  <svg viewBox="0 0 100 100" fill="#002ec6" xmlns="http://www.w3.org/2000/svg">
     <circle cx="50" cy="50" r="50" />
  </svg>
  <svg viewBox="0 0 100 100" fill="#7700c6" xmlns="http://www.w3.org/2000/svg">
     <circle cx="50" cy="50" r="50" />
  </svg>
   <svg viewBox="0 0 100 100" fill="#002ec6" xmlns="http://www.w3.org/2000/svg">
     <circle cx="50" cy="50" r="50" />
   </svg>
  <svg viewBox="0 0 100 100" fill="#7700c6" xmlns="http://www.w3.org/2000/svg">
     <circle cx="50" cy="50" r="50" />
  </svg>

</div>
<!-- Affichage de toutes les vidéos sous forme de vignettes cliquables -->
<div class="videos-container">
    <?php foreach ($videos as $video): ?>
     <div class="video-all">
            <div class="video-thumbnail">
                <a class="video-grid-item" href="watch?id=<?= $video['id'] ?>">
                <img class="thumbnail" src="thumbnails/<?= $video['thumbnail'] ?>" alt="<?= $video['title'] ?>"></a>
                <h2 class="video-title"><?= $video['title'] ?></h2>  
                </a>
              <div class="profil-container">
                <img style="width: 50px; height: 50px; border-radius: 50%;" src="upload-pfp/<?= $video['profile_picture'] ?>">
                <h3 class="username"><a href="channel?username=<?=$video['username']?>"><?= $video['username'] ?></a></h3>
             </div>
                <p class="video-view-and-date"> <?php
    // Récupérer l'ID de la vidéo depuis l'URL
    $id = $video['id'];
    
    if ($id) {

        $sql = "SELECT date_uploaded FROM videos WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$video = $stmt->fetch(PDO::FETCH_ASSOC);
$date_uploaded = $video['date_uploaded'];

// Calculer la différence de temps entre la date de mise en ligne et maintenant
$current_time = time();
$upload_time = strtotime($date_uploaded);
$time_diff = $current_time - $upload_time;

// Convertir la différence de temps en une chaîne lisible par l'homme
if ($time_diff < 60) {
  $time_string = "il y a " . $time_diff . " seconde" . ($time_diff > 1 ? "s" : "");
} elseif ($time_diff < 3600) {
  $time_diff = round($time_diff / 60);
  $time_string = "il y a " . $time_diff . " minute" . ($time_diff > 1 ? "s" : "");
} elseif ($time_diff < 86400) {
  $time_diff = round($time_diff / 3600);
  $time_string = "il y a " . $time_diff . " heure" . ($time_diff > 1 ? "s" : "");
} elseif ($time_diff < 604800) {
  $time_diff = round($time_diff / 86400);
  $time_string = "il y a " . $time_diff . " jour" . ($time_diff > 1 ? "s" : "");
} elseif ($time_diff < 2592000) {
  $time_diff = round($time_diff / 604800);
  $time_string = "il y a " . $time_diff . " semaine" . ($time_diff > 1 ? "s" : "");
} else {
  $time_string = "Mise en ligne le " . date("d/m/Y", $upload_time);
}


        
        // Récupérer le nombre de vues pour cette vidéo
        $sql = "SELECT COUNT(v.id) AS views_count FROM users u LEFT JOIN views v ON u.id = v.user_id WHERE v.video_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $views = $stmt->fetch(PDO::FETCH_ASSOC);
        
      

        $date_str = $result['date_uploaded'];
        
        // Afficher le nombre de vues
        echo ($views['views_count'] . " vues • " . $time_string);
      
    } else {
        echo "Impossible de récupérer l'ID de la vidéo";
    }
    ?></p>
     
            </div>
        </div>
        <?php endforeach; ?>
</div>
</body>
</html>
