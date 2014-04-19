<?php
$infile = preg_replace('/\.+\//', '', $_GET['p']);
$maxWidth = @$_GET['w'];
$maxHeight = @$_GET['h'];
$crop = @$_GET['c'] && $maxWidth && $maxHeight;
$datadir = substr(dirname(__FILE__), 0, strrpos(dirname(__FILE__), DIRECTORY_SEPARATOR.'plugins')) . '/data/';
$imagedir = $datadir . 'uploads/';
if (!$maxWidth && !$maxHeight) {
  $info = @getimagesize($imagedir.$infile);
  if (!$info) die('File not found or not an image!');
  header('Content-Type: '.$info['mime']);
  readfile($imagedir.$infile);
} else {
  $pos = strrpos($infile,'/');
  if ($pos === false) $pos = -1;
  $outfile = substr($infile, 0, $pos+1) . 'i18npic.' . ($crop ? 'C' : '') . ($maxWidth ? $maxWidth.'x' : '0x') . ($maxHeight ? $maxHeight.'.' : '0.') . substr($infile, $pos+1);
  $outfile = substr($outfile, 0, strrpos($outfile,'.')) . '.jpg';
  $thumbdir = $datadir . 'thumbs/';
  if (!file_exists($thumbdir.$outfile) || @filemtime($thumbdir.$outfile) < @filemtime($imagedir.$infile)) {
    if (!file_exists($imagedir.$infile)) die('File not found!');
    $info = @getimagesize($imagedir.$infile);
    if (!$info) die('Not an image!');
    $width = $info[0];
    $height = $info[1];
    switch ($info[2]) {
      case IMAGETYPE_JPEG:
      case IMAGETYPE_JPEG2000: $src = @imagecreatefromjpeg($imagedir.$infile); break;
      case IMAGETYPE_PNG: $src = @imagecreatefrompng($imagedir.$infile); break;
      case IMAGETYPE_GIF: $src = @imagecreatefromgif($imagedir.$infile); break;
    }
    if (!@$src) die('Can\' read image!');
    if ($crop) {
      $px = $py = 0;
      if ($maxWidth*$height > $width*$maxHeight) {
        $py = (int) (0.5 * ($height - $width*$maxHeight/$maxWidth)); 
      } else {
        $px = (int) (0.5 * ($width - $height*$maxWidth/$maxHeight));
      }
      $dst = imagecreatetruecolor($maxWidth, $maxHeight); 
      imagecopyresampled($dst, $src, 0, 0, $px, $py, $maxWidth, $maxHeight, $width-2*$px, $height-2*$py);
    } else {
      if (!$maxHeight || ($maxWidth && $width/$height > $maxWidth/$maxHeight)) {
        $newWidth = (int) $maxWidth;
        $newHeight = (int) (1.0*$newWidth*$height/$width);
      } else {
        $newHeight = (int) $maxHeight;
        $newWidth = (int) (1.0*$newHeight*$width/$height);
      }
      $dst = imagecreatetruecolor($newWidth, $newHeight); 
      imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    }
    $outdir = substr($thumbdir.$outfile, 0, strrpos($thumbdir.$outfile, '/'));
    if (!file_exists($outdir)) @mkdir($outdir, 0777, true);
    imagejpeg($dst, $thumbdir.$outfile, 85);
  }
  header('Content-Type: image/jpeg');
  readfile($thumbdir.$outfile);
} 



