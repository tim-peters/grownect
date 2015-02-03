<?php
switch($_GET['progress']) {
	case 1: // Receiving new Profile Picture
		if(isset($_POST['img']))
		{

			$uri_parts = explode("/", $_POST['img']);
			$file_name = array_pop($uri_parts);
			$file_id = array_pop($uri_parts);

			if(rename("./api/files/".$file_id."/".$file_name, "./img/users/".$file_id."_".$file_name))
			//if(true) // FOR DEBUG ONLY!
			{
				createThumb("./img/users/".$file_id."_".$file_name, 250);
				if(!$user_objects[$actual_user]->setPicture("./img/users/".$file_id."_".$file_name))
					$GLOBALS['log']->error("Picture could not be updated",__FILE__,__LINE__);
			}
			else
				$GLOBALS['log']->error("Image could not be found/moved.",__FILE__,__LINE__,null,true);
		}
		if(isset($_POST['description']))
		{
			$desc = secureString($_POST['description']);
			$user_objects[$actual_user]->setDescription($desc);
		}

	default: // Change Prodile Picture
		$content['body'] .= showUserbar($user_objects, $actual_user, $id);

		$id_hash = createHash();
		$content['js'][] = "./js/custom/change_profile.js";
		$content['js'][] = "./js/custom/textareaVoiceRecognition.js";#
		$content['script'] .= "var techID = '".$user_objects[$actual_user]->tech_id."';\n";
		$content['script'] .= "var apiurl = \"./api/api_pictureUpload.php?state=check&id=".$id_hash."\";\n";
		
		$content['body'] .= "<div class=\"userlarge\" style=\"background-image:url('".$user_objects[$actual_user]->picture."')\"> 
	</div>\n";
		
		$content['body'] .= "	<div class=\"editprofile\">\n";
		$content['body'] .= "		<div class=\"qr\">\n";
		$content['body'] .= "			Scan this code with your smartphone!<br>";
		$content['body'] .= "			<img src=\"./api/api_pictureUpload.php?state=img&id=".$id_hash."\">";
		$content['body'] .= "		</div> ";
		$content['body'] .= "		<img src=\"img/edit.png\" width=\"40\">";
		$content['body'] .= "	</div>\n";

		$content['body'] .= "<h1><b>".$user_objects[$actual_user]->name."</b></h1>\n";

		$content['body'] .= "	<form method='post' action='?state=".$state."&progress=1'>\n";

		$content['body'] .=	"<div class=\"textarea\">\n";
		$content['body'] .= "	<textarea name='description' id=\"".createHash()."\">".$user_objects[$actual_user]->description."</textarea>\n";
		$content['body'] .= "<div class=\"input\"><img src=\"img/voicerecording.png\" width=\"30\"><img src=\"img/keyboard.png\" width=\"30\">\n";
		$content['body'] .= "</div>";
		$content['body'] .= "</div>";
		$content['body'] .= "<div class=\"buttonbutton\">\n";
		$content['body'] .= "	<button type=\"button\" onClick=\"window.location.href='./'\" class=\"cancel2\"><img src=\"img/cancel.png\" width=\"25\">Cancel</button>\n";
		$content['body'] .= "	<button type=\"submit\" class=\"btn\"><img src=\"img/confirm.png\" width=\"25\">Save</button>\n";
		$content['body'] .= "</div>\n";

		$content['body'] .= "</form>";
	break;
}
?>