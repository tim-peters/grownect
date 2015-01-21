<?php
switch($progress) {
	case 1:
		// TODO: Checking tranfered data to avoid malware injection
		if($_POST['name'] != "" || $_POST['color'] != "") {
			
			$tech_id = md5(rand(0,99999)); // FIXME: Replace by real tech_id (from bracelet)
			$picture = "./img/user.png"; // FIXME: replace by real image url

			$new_user_object = User::fromNew($tech_id, $_POST['name'], $picture, $_POST['description'], $_POST['color']);
			$id = $new_user_object->id;
			$user_objects[$id] = $new_user_object;
			$content['body'] .= "<h2>User erfolgreich angelegt!</h2>\n";
			$content['body'] .= "<a href='?state=start&change_user=".$id."'>weiter</a>\n";
		}
	break;

	default:
	$content['body'] .= "<br><br><br>\n";
	$content['body'] .= "<p class='big_userpic'><img src='./img/user.png'></p>\n";
	$content['body'] .= "<form method='post' action='?state=".$state."&progress=1'>\n";
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