<?php
$file = basename($_GET['file']);

  $image = imagecreate(1000,100);
  $white = ImageColorAllocate ( $image ,  255,  255, 255 );
  $black = ImageColorAllocate ( $image ,  0,  0, 0 );
  imagefilledrectangle($image, 0, 0, 150, 50, $white);
  ImageTTFText ($image, 18, 0, 5, 50, $black, "fontz/".$file,stripslashes($_GET['text']))or die("SHIT");

  $neuesBild = imageJPEG($image);
?>
