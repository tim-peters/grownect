<?php
include_once("./classes/class_Moment.php");
switch(progress) {
	case 1: // already opened conflict to be specified (e.g. by hit on bracelet)
	break;
	
	default: // new conflict to be set and specified
		if(isset($id))
		{
			echo showUserbar($user_objects, $actual_user, $id);
			$instance = Conflict::fromNew($actual_user);
			$instance->setCreated_with($id);
			$conflict_id = $instance->id;
			$user_objects[$actual_user]->conflicts_active[$conflict_id] = $instance;
		}
		else
			$GLOBALS['log']->error("Error: You need to specify a person you want to open a conflict with.",__FILE__,__LINE__,null,true);

		$startway = 2;//rand(1,3);
		switch($startway) {
			case 1:
				echo "<h2>Did you ever act in a similar way?</h2>";
				echo "<textarea placeholder='Explain the situation you've acted in the same way.' required></textarea>";
			break;
			case 2:
				if($moment_db_outcome = $db->query(" 
					SELECT
						id,
						created_by,
						created_with,
						type,
						path,
						content
					FROM
						moments
					WHERE
						(created_by = ".$actual_user." AND created_with = ".$id.")
						OR
						(created_with = ".$actual_user." AND created_by = ".$id.")
				")) // FIXME: MYSQL-Ausdruck ersetzen. Siehe test.php
				{

					if($moment_row = $moment_db_outcome->fetch_object()) 
					{
						$moment = Moment::fromRow($moment_row);

						$moment->show();

						$user_objects[$actual_user]->conflicts_active[$conflict_id]->setMoment_used($moment->id, false);
						$user_objects[$actual_user]->conflicts_active[$conflict_id]->setProgress(2); // FIXME
						// FIXME: Add: richtig weiterverlinken

					}
					else
					{
						var_dump($moment_db_outcome->num_rows);
						echo "kein schöner moment gefunden (".$actual_user." & ".$id.")";
					}
				}
				// die($db->error);
			break;

			case 3:
			break;
		}
}
?>