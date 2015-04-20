<?php
switch($progress) {
	case 1:
		// TODO: Checking tranfered data to avoid malware injection
		if($_POST['name'] != "" || $_POST['color'] != "") {
			
			$tech_id = md5(rand(0,99999)); // FIXME: Replace by real tech_id (from bracelet)
			$uri_parts = explode("/", $_POST['img']);
			$file_name = array_pop($uri_parts);
			$file_id = array_pop($uri_parts);

			if($_POST['img'] != "./img/user.png")
			{
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
			}
			else
				$picture = $_POST['img'];

			$new_user_object = User::fromNew($tech_id, secureString($_POST['name']), $picture, secureString($_POST['description']), secureString($_POST['color']));
			$id = $new_user_object->id;
			$user_objects[$id] = $new_user_object;
			header("Location: ?state=start&change_user=".$id);
		}
	break;

	default:
	$id_hash = createHash();
	$content['js'][] = "./js/custom/sign_up.js";
	$content['script'] .= "var apiurl = \"./api/api_pictureUpload.php?state=check&id=".$id_hash."\";\n";
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
	$content['body'] .= "	<input type='hidden' name='img' value='./img/user.png' />";
	$content['body'] .= "		<input type='text' class=\"signup\" name='name' placeholder='Type in our name' required>";
	$content['body'] .= "	</p>";
	$content['body'] .= "	<div class=\"textarea\">";
	$content['body'] .= "		<textarea class=\"signup\" name='description' placeholder='Tell us something about you.'></textarea>";
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
	$content['body'] .= "	<button type=\"submit\" onClick=\"window.location.href=''\"class=\"btn2\"><img src=\"img/confirm.png\" width=\"25\">Get started</button>";
	$content['body'] .= "	</form>";
}
?>