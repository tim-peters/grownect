<?php
$content['body'] .= showUserbar($user_objects, $actual_user, $id);
switch($_GET['progress']) {
	case 1: // Change Prodile Picture
		$id_hash = md5(time()+"fileupload"+rand(000,999));
		
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
		$content['script'] .= "			$(\"img.qr\").after(\"<form method='POST' action='?state=".$state."&progress=2&id=".$id."'><input type='hidden' name='img' value='\"+msg+\"' /><input type='submit' value='save'></form>\");\n";
		$content['script'] .= "		}\n";
		$content['script'] .= "		else\n";
		$content['script'] .= "			setTimeout(function() { isItUploaded() },1000);\n";
		$content['script'] .= "	});\n";
		$content['script'] .= "}\n";
		$content['script'] .= "setTimeout(function() { isItUploaded(); },5000);\n";

		$content['body'] .= "<h3 class=\"do\">Scan the QR-Code to upload a photo from your smartphone!<h3>\n";
		$content['body'] .= "<img src=\"./api/api_pictureUpload.php?state=img&id=".$id_hash."\" width=\"159\" class=\"qr\"> <br>\n";
	break;

	case 2: // Receiving new Profile Picture
		if(isset($_POST['img']))
		{

			$uri_parts = explode("/", $_POST['img']);
			$file_name = array_pop($uri_parts);
			$file_id = array_pop($uri_parts);

			if(rename("./api/files/".$file_id."/".$file_name, "./img/users/".$file_id."_".$file_name))
			//if(true) // FOR DEBUG ONLY!
			{
				if(!$user_objects[$actual_user]->setPicture("./img/users/".$file_id."_".$file_name))
					$GLOBALS['log']->error("Picture could not be updated",__FILE__,__LINE__);
			}
			else
				$GLOBALS['log']->error("Image could not be found/moved.",__FILE__,__LINE__,null,true);
		}
		else
			$GLOBALS['log']->error("Image URL is missing.",__FILE__,__LINE__,null,true);

	default:
		$content['body'] .= "<img src=\"".$user_objects[$actual_user]->picture."\" width=\"300\"/><br>\n";
		$content['body'] .= "<a href=\"?state=".$state."&progress=1\">Change</a>\n";
		$content['body'] .= "<h2>".$user_objects[$actual_user]->name."</h2>";
}

?>