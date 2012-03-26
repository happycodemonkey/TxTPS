<?php

$imageSrc = (string)$_GET['image'];    
$width = $_GET['width']; 

$imageSrc = "https://tps.tacc.utexas.edu/problems/XFmfL7/images/TxTPS.png";
$width = 100;

if (is_numeric($width) && isset($imageSrc)){ 
    header('Content-type: image/jpeg');
    makeThumb($imageSrc, $width); 
}

function makeThumb($src,$newWidth) { 

    $srcImage = imagecreatefromjpeg($src); 
    $width = imagesx($srcImage); 
    $height = imagesy($srcImage); 
    
    $newHeight = floor($height*($newWidth/$width)); 
    
    $newImage = imagecreatetruecolor($newWidth,$newHeight);
    
     imagecopyresized($newImage,$srcImage,0,0,0,0,$newWidth,$newHeight,$width,$height);

     imagejpeg($newImage); 
} 
?>