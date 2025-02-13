<?php

require_once("../classes/class_Pusher.php");
require_once("../db_connect.inc");
require_once("../classes/class_Conflict.php");
require_once("../classes/class_User.php");
require_once("../helper_functions.inc");
require_once("../classes/class_Log.php");
$GLOBALS['log'] = new Log("../log/events.txt");

$app_id = '103648';
$app_key = '80c930949c53e186da3a';
$app_secret = '777b9e075eb9c337ea1e';
$pusher = new Pusher($app_key, $app_secret, $app_id);

if($_POST)
{
	switch($_POST['name']) {

		// tell the mirror, that a user is now standing in front of it (and who it is)
		case "setUser":
			$data['name'] = "setUser";
			$data['id'] = $_POST['user'];
			$pusher->trigger('grownect', 'events', $data);
			$GLOBALS['log']->event("API: triggered '".$data['name']."' to id '".$data['id']."'",__FILE__,__LINE__);
			echo 1;
		break;

		// tell the mirror, that the users has left the mirror
		case "leaveUser":
			$data['name'] = "leaveUser";
			$data['id'] = $_POST['user'];
			$pusher->trigger('grownect', 'events', $data);
			$GLOBALS['log']->event("API: triggered '".$data['name']."' for id '".$data['id']."'",__FILE__,__LINE__);
			echo 1;
		break;

		// decide whether the user needs to scream in his bracelet or not depending on the received pulse frequency
		// FIXME: Enhancement/Better Solution: Direct answer (echo) instead of second pusher-event
		case "setPulse": 
			$data['name'] = ($_POST['value'] > 110) ? "startScream" : "skipScream";
			$data['id'] = $_POST['id'];
			$pusher->trigger('grownect', 'events', $data);
			$GLOBALS['log']->event("API: triggered '".$data['name']."' (Pulse:".$_POST['value'].") for bracelet with id '".$data['id']."'",__FILE__,__LINE__);
			echo 1;
		break;

		// check whether the users has already screamed loud enough. If not: Forward the loudness value to the mirror
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
				$GLOBALS['log']->event("API: triggered '".$data['name']."' for bracelet with id '".$data['id']."'",__FILE__,__LINE__);
			}

			echo 1;
		break;

		// Trigger the Event to start Voice Recognition on the right bracelet
		case "startVoiceRec":
			$data['name'] = "getText";
			$data['hash'] = $_POST['hash'];
			$data['id'] = $_POST['id'];
			$data['value'] = $_POST['value'];
			$pusher->trigger('grownect', 'events', $data);
			echo 1;
		break;

		// Forward the received text (from bracelet) to the right textarea @ mirror
		case "setText":
			$data['name'] = "setText";
			$data['id'] = $_POST['id'];
			$data['hash'] = $_POST['hash'];
			$data['value'] = $_POST['value'];
			$pusher->trigger('grownect', 'answers', $data);
			//$GLOBALS['log']->event("API: triggered '".$data['name']."' with value '".$data['value']."' for id '".$data['id']."'/'".$data['hash']."'",__FILE__,__LINE__);
			echo 1;
		break;

		// Send description of the problem to the right bracelet
		case "getProblemDescription":
			$conflict_id = $_POST['value'];
			$actual_conflict = Conflict::fromDb($conflict_id);
			$user_object = User::fromDb($actual_conflict->created_by);
			$output = $user_object->name." said the issue is, that ".$actual_conflict->description;
			echo $output;
		break;

		//  Return name of the user who created the conflict identified by the received id
		case "getNameFromConflict":
			$conflict_id = $_POST['value'];
			$actual_conflict = Conflict::fromDb($conflict_id);
			$user_object = User::fromDb($actual_conflict->created_by);
			echo $user_object->name;
		break;

		// Send pusher Event to set the mirror in blurred state
		case "blurMirror";
			$data['name'] = "blurMirror";
			$data['conflict_id'] = $_POST['value'];
			$pusher->trigger('grownect', 'events', $data);
		break;

		// save received explanation (of causes) in database and send it to the right bracelet
		case "setExplanation":
			$data['name'] = "setExplanation";
			$data['conflict'] = $_POST['value'];

			$actual_conflict = Conflict::fromDb($_POST['value']);
			$actual_conflict->setExplanation(secureString($_POST['text']));
			$user_object = User::fromDb($actual_conflict->created_by);
			$data['id'] = $user_object->tech_id;
			$user_object_opponent = User::fromDb($actual_conflict->created_with);
			$data['text'] = $user_object_opponent->name." has explained the situation as following: ".$_POST['text'].". Is the conflict solved for you, with this explanation?";
			

			$pusher->trigger('grownect', 'events', $data);
			$GLOBALS['log']->event("API: triggered '".$data['name']."' to id '".$data['id']."'",__FILE__,__LINE__);
			echo 1;
		break;

		// Set conflict state to solved and refresh mirror view
		case "solveConflict":
			$actual_conflict = Conflict::fromDb($_POST['value']);
			$user_object = User::fromDb($actual_conflict->created_by);
			if($user_object->tech_id == $_POST['id'])
			{
				$actual_conflict->solve();

				$data['name'] = "reloadMirror";
				$pusher->trigger('grownect', 'events', $data);
				echo 1;
			}
			else
				echo 0;
		break;

		default:
			echo 0;
	}
}
else
	echo 0;
?>