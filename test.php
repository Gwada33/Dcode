<?php 

$video_path = "videos/video_63fa30f88158e.mp4";
$output_path = "videos/output.mp4";

exec("ffmpeg -i {$output_path} -vcodec libx264 -crf 28 {$video_path}");


// Convertir la vidéo en 16:9
exec("ffmpeg -i {$video_path} -vf 'crop=(ih*16/9):ih,scale=1280:720' -strict -2 {$output_path}");

// Compresser la vidéo



?>