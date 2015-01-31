<?php

function createHash() { return md5(time().rand(0,999).$user_objects[$actual_user]->tech_id); };

function secureString($str) { return mysql_real_escape_string(strip_tags(trim($str))); };

function my_explode($delim, $str, $lim = 1)
{
	if ($lim > -2) return explode($delim, $str, abs($lim));

	$lim = -$lim;
	$out = explode($delim, $str);
	if ($lim >= count($out)) return $out;

	$out = array_chunk($out, count($out) - $lim + 1);

	return array_merge(array(implode($delim, $out[0])), $out[1]);
}

function createThumb($src, $size, $prefix = null)
{

	$fileSize = GetImageSize($src); 
	$fileType = exif_imagetype($src); //Mime-Type ermitteln
	if($fileSize[0] > $fileSize[1])  // wenn Breiter als Hoch
	{
		$old_size = $fileSize[1];
		$src_x = $fileSize[0]/2-$old_size/2;
		$src_y = 0;
	}
	else
	{
		$old_size = $fileSize[0];
		$src_x = 0;
		$src_y = $fileSize[1]/2-$old_size/2;
	}
	// IMAGE erstellen je nach Dateityp
	switch($fileType) {
		case IMAGETYPE_JPEG:
			$src_img = imagecreatefromjpeg($src);
		break;

		case IMAGETYPE_GIF:
			$src_img = imagecreatefromgif($src);
		break;

		case IMAGETYPE_GIF:
			$src_img = imagecreatefromgif($src);
		break;

		default:
			die("Can't create thumbnail. Unknown Image format");
	}
	$dest_img = ImageCreateTrueColor($size,$size);
	ImageCopyResampled($dest_img,$src_img,0,0,$src_x,$src_y,$size,$size,$old_size,$old_size);
	//imagecopymerge($img2, $dest_img, 0, 0, $Position_x, $Position_y, $size, $size, 100); // Auschnitt aus verkleinertem Bild (Mittelpunkt) einfuegen

	//Die neue Datei unter einem anderen Namen abgespeichern und loeschen
	if(isset($prefix)) {
		$pathParts = my_explode("/", $src, -2);
		$newPath = $pathParts[0]."/".$prefix.$pathParts[1];
	}
	else
		$newPath = $src;

	switch($fileType) {
		case IMAGETYPE_JPEG:
			imagejpeg($dest_img, $newPath);
		break;

		case  	IMAGETYPE_GIF:
			imagegif($dest_img, $newPath);
		break;

		case  	IMAGETYPE_GIF:
			imagepng($dest_img, $newPath);
		break;

		default:
			die("Can't create thumbnail. Unknown Image format");
	}
	imagedestroy($dest_img);
}

?>