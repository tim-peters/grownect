<?php
error_reporting(E_ALL ^ E_NOTICE);

switch ($_GET['state']) {
	case 'check':
		if(isset($_GET['id']))
		{
			$files = glob("./files/".$_GET['id']."/*");
			if(count($files) > 0)
			{
				echo $files[0];
				exit;
			}
			else
				echo 0;
		}
		else
			die("Error: ID is missing");
	break;
	
	case 'upload':
		if(isset($_GET['id']))
		{
			if($_POST) {
				$uploaddir = __DIR__.'/files/'.$_GET['id']."/";
				$uploadfile = $uploaddir.basename($_FILES['userfile']['name']);
				$check = getimagesize($_FILES["userfile"]["tmp_name"]);
   				if($check !== false) {
					if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
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
					    background: url(https://lh6.googleusercontent.com/-dqTIJRTqEAQ/UJaofTQm3hI/AAAAAAAABHo/w7ruR1SOIsA/s157/upload.png);
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
			die("Error: ID is missing");
	break;
	
	case 'img':
		if(isset($_GET['id']))
		{
			include("./classes/class_QRcode.php");
			QRcode::png("http://$_SERVER[HTTP_HOST]$_SERVER[SCRIPT_NAME]?state=upload&id=".$_GET['id']);
		}
		else
			die("Error: ID is missing");


	case 'create':
	if(isset($_GET['id']))
	{
		$id = $_GET['id'];
		if(!is_dir("./files/".$id."/")) mkdir("./files/".$id."/");

		?>
		<!DOCTYPE html>
		<html>
		<head>
			<title>File upload</title>
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
			<script>
				function isItUploaded()
				{
					console.log("run: isItUploaded()");
					$.ajax({
						type: "POST",
						url: "?state=check&id=<?php echo $id; ?>"
					})
					.done(function( msg ) {
						if(msg != 0)
						{
							$("img").attr("src", msg);
							$("strong").html("<a href='./ '>Neues Bild hochladen?</a>");
						}
						else
							setTimeout(function() { isItUploaded() },1000);
					});
				}
				setTimeout(function() { isItUploaded(); },5000);
			</script>
			<style>
			body {
				text-align: center;;
			}
			img {
				max-width: 90%;
			}
			</style>
		</head>
		<body>
		<strong>Scanne den QR-Code mit deinem Smartphone!</strong><br>
		<img alt="Scanne den QR-Code mit deinem Smartphone!" src="?state=img&id=<?php echo $id; ?>">
		</body>
		</html>
		<?php
	}
	break;

	case 'show':
	?>
	<html>
	<head>
		<title></title>
	<style>
	body {
		margin:10px auto;
		width:900px;
		-moz-column-count:3;
	    -moz-column-gap: 3%;
	    -moz-column-width: 30%;
	    -webkit-column-count:3;
	    -webkit-column-gap: 3%;
	    -webkit-column-width: 30%;
	    column-count: 3;
	    column-gap: 3%;
	    column-width: 30%;
	    background:#ccc;
	}
	a {
		display:block;
		padding-bottom: 5px;
	}
	</style>
	</head>
	<body>

	<?php
		if ( ! function_exists('glob_recursive'))
		{
		    // Does not support flag GLOB_BRACE        
		   function glob_recursive($pattern, $flags = 0)
		   {
		     $files = glob($pattern, $flags);
		     foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
		     {
		       $files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
		     }
		  return $files;
		  }
		}
		$images = glob_recursive("./files/*/*.*");
		foreach($images as $image)
			echo "<a href='".$image."'><img src=".$image." width='300'></a>\n";

		echo "</body></html>";
	break;

	default:
		$id = md5(time()+"fileupload"+rand(000,999));
		header("Location: ?state=create&id=".$id);
		//echo "<a href='?state=create&id=".$id."'></a>";
	break;
}

?>