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
	$content['script'] .= "			$(\".qr\").remove();\n";
	$content['script'] .= "			$(\".usersignup\").css(\"background-image\", \"url('\"+msg+\"'\");\n";
	$content['script'] .= "			$(\".usersignup\").after(\"<input type='hidden' name='img' value='\"+msg+\"' />\");\n";
	$content['script'] .= "			$(\".edit img\").hide();\n";
	$content['script'] .= "		}\n";
	$content['script'] .= "		else\n";
	$content['script'] .= "			setTimeout(function() { isItUploaded() },1000);\n";
	$content['script'] .= "	});\n";
	$content['script'] .= "}\n";
	$content['script'] .= "$('.edit').click(function() {";
	$content['script'] .= "	$('.qr').show();";
	$content['script'] .= "	setTimeout(function() { isItUploaded(); },5000);\n";
	$content['script'] .= "});";
	$content['body'] .= "<div class=\"edit\">\n";
	$content['body'] .= "	<div class=\"qr\">\n";
	$content['body'] .= "			Scan this code with your smartphone!<br>";
	$content['body'] .= "		<img src=\"./api/api_pictureUpload.php?state=img&id=".$id_hash."\">";
	$content['body'] .= "	</div> ";
	$content['body'] .= "	<img src=\"img/edit.png\" width=\"40\">";
	$content['body'] .= "	</div>";
	$content['body'] .= "	<form method='post' action='?state=".$state."&progress=1'>";
	$content['body'] .= "	<div class=\"signupcontainer\">";
	$content['body'] .= "	<div class=\"usersignup\" style=\"background-image:url(img/user.png)\"></div>";
	$content['body'] .= "		<h1><b>Sign up</b></h1>";
	$content['body'] .= "	<p>";
	$content['body'] .= "		<input type='text' class=\"signup\" name='name' placeholder='Type in our name' required>";
	$content['body'] .= "	</p>";
	$content['body'] .= "	<div class=\"textarea\">";
	$content['body'] .= "		<textarea class=\"signup\" name='description' placeholder='Tell us something about you.'></textarea>";
	$content['body'] .= "		<div class=\"input\"><img src=\"img/voicerecording.png\" width=\"30\"><img src=\"img/keyboard.png\" width=\"30\">";
	$content['body'] .= "	</div>";
	$content['body'] .= "	</div>";
	$content['body'] .= "	<input type=\"hidden\" name=\"color\" />";
	$content['body'] .= "	<ul id=\"colorpicker\">";
	$content['body'] .= "	<li style=\"background-color:#1a3140\"></li>";
	$content['body'] .= "	<li style=\"background-color:#659fa6\"></li>";
	$content['body'] .= "	<li style=\"background-color:#ad809b\"></li>";
	$content['body'] .= "	<li style=\"background-color:#f2cf8c\"></li>";
	$content['body'] .= "	<li style=\"background-color:#f59556\"></li>";
	$content['body'] .= "	<li style=\"background-color:#f2786d\"></li>";
	$content['body'] .= "	<li style=\"background-color:#d9304f\"></li>";
	$content['body'] .= "	<li style=\"background-color:#8d1c1c\"></li>";
	$content['body'] .= "	<li class=\"active\" style=\"background-color:#acda8d\"></li>";
	$content['body'] .= "	<li style=\"background-color:#3f8143\"></li>";
	$content['body'] .= "	</ul>";
	$content['body'] .= "	</div>";
	$content['body'] .= "	   <button type=\"button\" type=\"submit\" class=\"btn\"><img src=\"img/confirm.png\" width=\"25\">Get started</button>";
	$content['body'] .= "	</form>";
}
?>