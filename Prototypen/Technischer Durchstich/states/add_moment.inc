<?php
$content['body'] .= showUserbar($user_objects, $actual_user, $id);
$content['body'] .= "<h2>Creating a good moment with ".$user_objects[$id]->name."</h2>";
switch($progress) {
	default:
		$content['body'] .= "<h3>what kind of good moment do you want to create?</h3>\n";
		$content['body'] .= "<ul>\n";
		$content['body'] .= "	<li><a href='?state=".$state."&progress=1&type=0'>Text</a></li>\n";
		$content['body'] .= "	<li><a href='?state=".$state."&progress=1&type=1'>Picture</a></li>\n";
		$content['body'] .= "	<li><a href='?state=".$state."&progress=1&type=2'>Voicemail</a></li>\n";
		$content['body'] .= "	<li><a href='?state=".$state."&progress=1&type=3'>Video</a></li>\n";
		$content['body'] .= "</ul>\n";
	break;
	case 1:
		switch($_GET['type']) {
			default:
			$content['body'] .= "<form method='POST' action='?state=".$state."&progress=2$type=0'>\n";
			$content['body'] .= "<textarea placeholder='Describe the moment in your words here...' required></textarea><br>\n";
			$content['body'] .= "<input type='submit' value='save moment'>\n";
			$content['body'] .= "</form>\n";
			break;

			case 1:
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
				$content['script'] .= "			$(\"img.qr\").attr(\"src\", msg);\n";
				//$content['script'] .= "			$(\"strong\").html(\"<a href='./ '>Neues Bild hochladen?</a>\");\n";
				$content['script'] .= "		}\n";
				$content['script'] .= "		else\n";
				$content['script'] .= "			setTimeout(function() { isItUploaded() },1000);\n";
				$content['script'] .= "	});\n";
				$content['script'] .= "}\n";
				$content['script'] .= "setTimeout(function() { isItUploaded(); },5000);\n";

				$content['body'] .= "<h3>Scan the QR-Code to upload a photo from your smartphone!<h3>\n";
				$content['body'] .= "<img src=\"./api/api_pictureUpload.php?state=img&id=".$id_hash."\" class=\"qr\"> \n";
			break;
		}
	break;
}
?>