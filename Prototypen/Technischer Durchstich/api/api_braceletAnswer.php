<?php

require_once("../classes/class_Pusher.php");
$app_id = '103648';
$app_key = '80c930949c53e186da3a';
$app_secret = '777b9e075eb9c337ea1e';
$pusher = new Pusher($app_key, $app_secret, $app_id);

if($_POST)
{
	switch($_POST['name']) {
		case "setPulse":

			$data['name'] = ($_POST['value'] > 110) ? "startScream" : "endScream";
			$data['id'] = $_POST['id'];
			$pusher->trigger('grownect', 'events', $data);
			echo 1;
		break;

		case "setLoudness":
			$data['id'] = $_POST['id'];
			
			if($_POST['value'] < 0.25)
			{
				$data['name'] = "setLoudness";
				$data['value'] = $_POST['value'];
				$pusher->trigger('grownect', 'answers', $data);
			}
			else
			{
				$data['name'] = "endScream";
				$pusher->trigger('grownect', 'events', $data);
			}

			echo 1;
		break;

		case "setText":
			$data['name'] = "setText";
			$data['id'] = $_POST['id'];
			$data['hash'] = $_POST['hash'];
			$data['value'] = $_POST['value'];
			$pusher->trigger('grownect', 'answers', $data);
			echo 1;
		break;

		case "startVoiceRec":
			$pusher = new Pusher($app_key, $app_secret, $app_id);

			$data['name'] = "getText";
			$data['hash'] = $_POST['hash'];
			$data['id'] = $_POST['id'];
			$pusher->trigger('grownect', 'events', $data);
		break;

		default:
			echo 0;
	}
}
else
	echo 0;
?>