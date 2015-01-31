<?php
error_reporting(E_ALL);

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

	/*switch($fileType) {
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
	}*/

	header('Content-Type: image/jpeg');
	imagejpeg($dest_img);
	imagedestroy($dest_img);
}


if($_POST)
{
	$uploaddir = __DIR__.'/files/tmp/';
	$uploadfile = $uploaddir.basename($_FILES['userfile']['name']);
	$check = getimagesize($_FILES["userfile"]["tmp_name"]);
		if($check !== false) {
		if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
			// FIXME: Enhancement: Creating Thumbnail from Image
			createThumb($uploadfile, 100);
		}
		else
			die("error: "+$_FILES['userfile']['error']);
	}
	else
	{
		echo "<h3>Bitte nur Bilder hochladen!</h3>\n";
		echo "<a href='?state=create&id=".$_GET['id'].">zurück</a>";
	}
}
else
{
?>
<!DOCTYPE html>
<html>
<head>
	<title>Bild hochladen</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<style>
	body {
		font-family: 'Lucida Grande', 'Helvetica Neue', sans-serif;
		font-size: 13px;
		text-align: center;
	}

	div.upload {
		display:inline-block;
	    width: 157px;
	    height: 57px;
	    background: url("./img/upload_button.png");
	    overflow: hidden;
	}

	div.upload input {
	    display: block !important;
	    width: 157px !important;
	    height: 57px !important;
	    opacity: 0 !important;
	    overflow: hidden !important;
	}

	</style>
</head>
<body>
<form enctype="multipart/form-data" action="" method="POST" id="form">
<div class="upload">
	<input type="hidden" name="value" />
    <input type="file" id="file" name="userfile" onChange="document.getElementById('form').submit()" />
</div>
</form>
</body>
</html>
<?php
}
?>