<?php
require_once("./classes/class_Moment.php");

$content['body'] .= showUserbar($user_objects, $actual_user, $id);
$content['body'] .= "<h2>Creating a good moment with ".$user_objects[$id]->name."</h2>";
switch($progress) {
	default:
		$content['body'] .= "<div class=\"container\">\n";
		$content['body'] .= "	<div class=\"kasten\">\n";
		$content['body'] .= "		<ul> \n";
		$content['body'] .= "			<li><a href=\"?state=".$state."&progress=1&id=".$id."&type=1\"><img src=\"img/moment-w.png\" width=\"50\" style=\"margin:10px\"><br>upload a picture</a></li>\n";
		$content['body'] .= "		</ul>\n";
		$content['body'] .= "	</div>\n";
		$content['body'] .= "	<div class=\"kasten\">\n";
		$content['body'] .= "		<ul> \n";
		$content['body'] .= "			<li><a href=\"#\"><img src=\"img/voicerecording.png\" width=\"50\" style=\"margin:10px\"><br>say something</a></li>\n";
		$content['body'] .= "		</ul>\n";
		$content['body'] .= "	</div>\n";
		$content['body'] .= "	<div class=\"kasten\">\n";
		$content['body'] .= "		<ul> \n";
		$content['body'] .= "			<li><a href=\"?state=".$state."&progress=1&id=".$id."&type=0\"><img src=\"img/keyboard.png\" width=\"50\" style=\"margin:10px\"><br>write something</a></li>\n";
		$content['body'] .= "		</ul>\n";
		$content['body'] .= "	</div>\n";
		$content['body'] .= "	<div class=\"kasten\">\n";
		$content['body'] .= "		<ul> \n";
		$content['body'] .= "			<li><a href=\"#\"><img src=\"img/movie36.png\" width=\"50\" style=\"margin:10px\"><br>upload a video</a></li>\n";
		$content['body'] .= "		</ul>\n";
		$content['body'] .= "	</div>				         \n";
		$content['body'] .= "</div>\n";
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
			// FIXME: Add Title and Date
			break;

			case 1:
				$id_hash = createHash();
				$content['head'] .= "<meta charset='UTF-8'>\n";
				$content['js'][] = "./js/custom/rating.js";
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
				$content['script'] .= "			$(\".qr img\").attr(\"src\", msg);\n";
				$content['script'] .= "			$(\"input.signup2\").after(\"<input type='hidden' name='img' value='\"+msg+\"' />\");\n";
				$content['script'] .= "		}\n";
				$content['script'] .= "		else\n";
				$content['script'] .= "			setTimeout(function() { isItUploaded() },1000);\n";
				$content['script'] .= "	});\n";
				$content['script'] .= "}\n";
				$content['script'] .= "setTimeout(function() { isItUploaded(); },5000);\n";


				$content['body'] .= "<div class=\"container\">\n";
				$content['body'] .= "	<div class=\"kasteneinbild\">\n";
				$content['body'] .= "		<div class=\"qr\">\n";
				$content['body'] .= "			<img src=\"./api/api_pictureUpload.php?state=img&id=".$id_hash."\">\n";
				$content['body'] .= "		</div>\n";
				$content['body'] .= "	</div>\n";
				$content['body'] .= "	<form method=\"POST\" action=\"?state=".$state."&progress=2&id=".$id."&type=1\"> 	\n";
				$content['body'] .= "	<p>\n";
				$content['body'] .= "		<input type='text' class=\"signup2\" name='title' placeholder='Type in the title' required>		<input type='date' class=\"signup3\" name='date' placeholder='Date'>\n";
				$content['body'] .= "	</p>\n";
				$content['body'] .= "	<input type=\"hidden\" name=\"rating\" />\n";
				$content['body'] .= "	<div class=\"rating\">\n";
				$content['body'] .= "		<span>☆</span><span>☆</span><span>☆</span><span>☆</span><span>☆</span>\n";
				$content['body'] .= "	</div>\n";
				$content['body'] .= "<button type=\"submit\" class=\"btn\"><img src=\"./img/confirm.png\" width=\"25\">Okay</button>\n";
				$content['body'] .= "	</form>\n";
				$content['body'] .= "</div>\n";

				// FIXME: Enhancement: Integrate Name and Date (optional)
			break;
		}
	break;

	case 2:
		switch($_GET['type']) {
			default:
				if(isset($_POST['text']) && isset($_POST['rating']))
				{
					$instance = Moment::fromNew($actual_user, $id, 0, secureString($_POST['title']), date("Y-m-d H:i:s", strtotime($_POST['date'])), secureString($_POST['rating']), null, secureString($_POST['text']));
					$content['body'] .= "Erfolgreich erstellt!\n"; // FIXME: Change wording
				}
				else
					$GLOBALS['log']->error("Text or rating is missing",__FILE__,__LINE__,null,true);
			break;

			case 1:
				if(isset($_POST['img']) && isset($_POST['rating']) && isset($_POST['title']))
				{

					$uri_parts = explode("/", $_POST['img']);
					$file_name = array_pop($uri_parts);
					$file_id = array_pop($uri_parts);

					if(rename("./api/files/".$file_id."/".$file_name, "./img/moments/".$file_id."_".$file_name))
					//if(true) // FOR DEBUG ONLY!
					{
						//echo $actual_user.", ".$id.", 1, ".$_POST['rating'].", ".$_POST['img']."\n";
						$instance = Moment::fromNew($actual_user, $id, 1, secureString($_POST['title']), date('Y-m-d H:i:s', strtotime($_POST['date'])),secureString($_POST['rating']), "./img/moments/".$file_id."_".$file_name);
						$content['body'] .= "<h2>Successfully uploaded</h2>\n";
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