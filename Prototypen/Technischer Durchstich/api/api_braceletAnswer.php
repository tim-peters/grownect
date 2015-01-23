<?php

require_once("../classes/class_Pusher.php");
$app_id = '103648';
$app_key = '80c930949c53e186da3a';
$app_secret = '777b9e075eb9c337ea1e';

if($_POST)
{
	switch($_POST['name']) {
		case "setPulse";
			$pusher = new Pusher($app_key, $app_secret, $app_id);

			$data['name'] = ($_POST['value'] > 110) ? "startScream" : "endScream";
			$data['id'] = $_POST['id'];
			$pusher->trigger('grownect', 'answers', $data);
			echo 1;
		break;
	}
}
else
	echo 0;
?>