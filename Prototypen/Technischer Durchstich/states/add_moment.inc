<?php
require_once("./classes/class_Moment.php");

$content['body'] .= showUserbar($user_objects, $actual_user, $id);
$content['body'] .= "<h2>Creating a good moment with ".$user_objects[$id]->name."</h2>";
switch($progress) {
	default:
		$content['body'] .= "<h3>what kind of good moment do you want to create?</h3>\n";
		$content['body'] .= "<ul>\n";
		$content['body'] .= "	<li><a href='?state=".$state."&progress=1&id=".$id."&type=0'>Text</a></li>\n";
		$content['body'] .= "	<li><a href='?state=".$state."&progress=1&id=".$id."&type=1'>Picture</a></li>\n";
		$content['body'] .= "	<li><a href='?state=".$state."&progress=1&id=".$id."&type=2'>Voicemail</a></li>\n";
		$content['body'] .= "	<li><a href='?state=".$state."&progress=1&id=".$id."&type=3'>Video</a></li>\n";
		$content['body'] .= "</ul>\n";
	break;
	case 1:
		switch($_GET['type']) {
			default:
			$content['js'][] = "./js/custom/textareaVoiceRecognition.js";
			$content['script'] .= "var techID = \"".$user_objects[$actual_user]->tech_id."\";";
			$content['body'] .= "<form method='POST' action='?state=".$state."&progress=2&id=".$id."&type=0'>\n";
			$content['body'] .= "<textarea name=\"text\" id=\"".createHash()."\" placeholder='Describe the moment in your words here...' required></textarea><br>\n";
			$content['body'] .= "<select name='rating' size='1'><option value='0'>*</option><option value='25'>**</option><option value='50'>***</option><option value='75'>****</option><option value='100' selected>*****</option></select>\n";
			$content['body'] .= "<input type='submit' value='save moment'>\n";
			$content['body'] .= "</form>\n";
			break;

			case 1:
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
				$content['script'] .= "			$(\"img.qr\").after(\"<form method='POST' action='?state=".$state."&progress=2&id=".$id."&type=1'><input type='hidden' name='img' value='\"+msg+\"' /><select name='rating' size='1'><option value='0'>*</option><option value='25'>**</option><option value='50'>***</option><option value='75'>****</option><option value='100' selected>*****</option></select><input type='submit' value='save'></form>\");\n";
				$content['script'] .= "		}\n";
				$content['script'] .= "		else\n";
				$content['script'] .= "			setTimeout(function() { isItUploaded() },1000);\n";
				$content['script'] .= "	});\n";
				$content['script'] .= "}\n";
				$content['script'] .= "setTimeout(function() { isItUploaded(); },5000);\n";

				$content['body'] .= "<h3 class=\"do\">Scan the QR-Code to upload a photo from your smartphone!<h3>\n";
				$content['body'] .= "<img src=\"./api/api_pictureUpload.php?state=img&id=".$id_hash."\" width=\"159\" class=\"qr\"> <br>\n";

				// FIXME: Enhancement: Integrate Name and Date (optional)
			break;
		}
	break;

	case 2:
		switch($_GET['type']) {
			default:
				if(isset($_POST['text']) && isset($_POST['rating']))
				{
					$instance = Moment::fromNew($actual_user, $id, 0, $_POST['rating'], null, $_POST['text']);
					$content['body'] .= "Erfolgreich erstellt!\n";Erf
				}
				else
					$GLOBALS['log']->error("Text or rating is missing",__FILE__,__LINE__,null,true);
			break;

			case 1:
				if(isset($_POST['img']) && isset($_POST['rating']))
				{

					$uri_parts = explode("/", $_POST['img']);
					$file_name = array_pop($uri_parts);
					$file_id = array_pop($uri_parts);

					if(rename("./api/files/".$file_id."/".$file_name, "./img/moments/".$file_id."_".$file_name))
					//if(true) // FOR DEBUG ONLY!
					{
						//echo $actual_user.", ".$id.", 1, ".$_POST['rating'].", ".$_POST['img']."\n";
						$instance = Moment::fromNew($actual_user, $id, 1, $_POST['rating'], $_POST['img']);
						$content['body'] .= "Erfolgreich erstellt!\n";
					}
					else
						$GLOBALS['log']->error("Image could not be found/moved.",__FILE__,__LINE__,null,true);
				}
				else
					$GLOBALS['log']->error("Image URL or rating is missing.",__FILE__,__LINE__,null,true);
			break;
		}
	break;
}
$content['body'] .= showHomebutton();
?>