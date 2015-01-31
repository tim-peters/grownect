<?php
error_reporting(E_ALL);

switch ($_GET['state']) {
	// check whether a picture has already been uploaded
	case 'check':
		if(isset($_GET['id']))
		{
			$files = glob("files/".$_GET['id']."/*");
			if(count($files) > 0)
			{
				echo "./api/".$files[0];
				exit;
			}
			else
				echo 0;
		}
		else
			die("Error: ID is missing");
	break;
	
	// site to upload a picture (for smartphone)
	case 'upload':
		if(isset($_GET['id']))
		{
			if(count(glob("files/".$_GET['id']."/*")) <= 0)
			{
				if($_POST) {
					$uploaddir = __DIR__.'/files/'.$_GET['id']."/";
					$uploadfile = $uploaddir.basename($_FILES['userfile']['name']);
					$check = getimagesize($_FILES["userfile"]["tmp_name"]);
	   				if($check !== false) {
						if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
							// FIXME: Enhancement: Creating Thumbnail from Image
							echo "<h3>erfolgreich hochgeladen!</h3>\n";
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
						    background: url("../img/picture_upload.png");
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
			}
			else
				die("Error: An Image has already been uploaded with that code");
		}
		else
			die("Error: ID is missing");
	break;
	
	// create a QR-Code to access the upload site
	case 'img':
		if(isset($_GET['id']))
		{
			if(!is_dir("./files/".$_GET['id']."/")) mkdir("./files/".$_GET['id']."/",0755,true);

			require_once("../classes/class_QRcode.php");
			QRcode::png("http://$_SERVER[HTTP_HOST]$_SERVER[SCRIPT_NAME]?state=upload&id=".$_GET['id']);
		}
		else
			die("Error: ID is missing");
}

?>