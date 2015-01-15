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
			echo "<h2>User erfolgreich angelegt!</h2>\n";
			echo "<a href='?state=start&change_user=".$id."'>weiter</a>\n";
		}
	break;

	default:
	echo "<br><br><br>\n";
	echo "<p class='big_userpic'><img src='./img/user.png'></p>\n";
	echo "<form method='post' action='?state=".$state."&progress=1'>\n";
	echo "	<p>\n";
	echo "		<input type='text' name='name' placeholder='Your Name' required>\n";
	echo "	</p>\n";
	echo "	<p>\n";
	echo "		<input type='color' name='color' value='".sprintf('#%06x',rand(0,16777215))."' required>\n";
	echo "	</p>\n";
	echo "	<p>\n";
	echo "		<textarea name='description' placeholder='Tell us something about ya!'></textarea>\n";
	echo "	</p>\n";
	echo "	<input type='submit' value='weiter'>\n";
	echo "</form>\n";
}
?>