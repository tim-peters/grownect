<?php
switch($progress) {
	case 1:
		// TODO: Checking tranfered data to avoid malware injection
		if($_POST['name'] != "" || $_POST['color'] != "") {
			
			$tech_id = md5(rand(0,99999)); // FIXME: Replace by real tech_id (from bracelet)
			$uri_parts = explode("/", $_POST['img']);
			$file_name = array_pop($uri_parts);
			$file_id = array_pop($uri_parts);

			if(rename("./api/files/".$file_id."/".$file_name, "./img/moments/".$file_id."_".$file_name))
			//if(true) // FOR DEBUG ONLY!
			{
				$picture = "./img/moments/".$file_id."_".$file_name;
				createThumb($picture,250);
			}
			else
			{
				$picture = "./img/user.png";
				$GLOBALS['log']->error("Image could not be found/moved.",__FILE__,__LINE__);
			}

			$new_user_object = User::fromNew($tech_id, secureString($_POST['name']), $picture, secureString($_POST['description']), secureString($_POST['color']));
			$id = $new_user_object->id;
			$user_objects[$id] = $new_user_object;
			$content['body'] .= "<h2>User erfolgreich angelegt!</h2>\n"; // FIXME: Replace with auto redirect
			$content['body'] .= "<a href='?state=start&change_user=".$id."'>weiter</a>\n";
		}
	break;

	default:
	$id_hash = createHash();
	$content['script'] .= "function isItUploaded()\n";
	$content['script'] .= "{\n";
	$content['script'] .= "	console.log(\"run: isItUploaded()\");\n";
	$content['script'] .= "	$.ajax({\n";
	$content['script'] .= "		type: \"POST\",\n";
	$content['script'] .= "		url: \"./api/api_pictureUpload.php?state=check&id=".$id_hash."\"\n";
	$content['script'] .= "	})\n";
	$content['script'] .= "	.done(function( msg ) {\n";
	$content['script'] .= "		if(msg != 0)\n";
	$content['script'] .= "		{\n";
	$content['script'] .= "			$(\".do\").text('The mirror has received this photo. Do you wanna take it?');\n";
	$content['script'] .= "			$(\"img.qr\").attr(\"src\", msg);\n";
	$content['script'] .= "			$(\"img.qr\").after(\"<input type='hidden' name='img' value='\"+msg+\"' />\");\n";
	$content['script'] .= "		}\n";
	$content['script'] .= "		else\n";
	$content['script'] .= "			setTimeout(function() { isItUploaded() },1000);\n";
	$content['script'] .= "	});\n";
	$content['script'] .= "}\n";
	$content['script'] .= "setTimeout(function() { isItUploaded(); },5000);\n";
	$content['body'] .= "<br><br><br>\n";
	$content['body'] .= "<h3 class=\"do\">Scan the QR-Code to upload a profile photo from your smartphone!</h3>\n";
	$content['body'] .= "<form method='post' action='?state=".$state."&progress=1'>\n";
	$content['body'] .= "<p class='big_userpic'><img src=\"./api/api_pictureUpload.php?state=img&id=".$id_hash."\" width=\"159\" class=\"qr\"></p> <br>\n";
	$content['body'] .= "	<p>\n";
	$content['body'] .= "		<input type='text' name='name' placeholder='Your Name' required>\n";
	$content['body'] .= "	</p>\n";
	$content['body'] .= "	<p>\n";
	$content['body'] .= "		<input type='color' name='color' value='".sprintf('#%06x',rand(0,16777215))."' required>\n";
	$content['body'] .= "	</p>\n";
	$content['body'] .= "	<p>\n";
	$content['body'] .= "		<textarea name='description' placeholder='Tell us something about ya!'></textarea>\n";
	$content['body'] .= "	</p>\n";
	$content['body'] .= "	<input type='submit' value='weiter'>\n";
	$content['body'] .= "</form>\n";
}
?>