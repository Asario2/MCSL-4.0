<?php
$file = basename($_GET['file']);

$image = imagecreate(800, 50);

$white = ImageColorAllocate($image, 255, 255, 255);
$black = ImageColorAllocate($image, 0, 0, 0);

imagefilledrectangle($image, 0, 0, 800, 50, $white);

$text = stripslashes($_GET['text']);

$box = imagettfbbox(
    18,
    0,
    "fontz/" . $file,
    $text
);

// Texthöhe berechnen
$textHeight = $box[1] - $box[7];

// Y zentrieren (wichtig: Baseline korrigieren)
$y = (50 - $textHeight) / 2 + $textHeight;

imagettftext(
    $image,
    18,
    0,
    5,
    $y,
    $black,
    "fontz/" . $file,
    $text
);

imagejpeg($image);
?>
