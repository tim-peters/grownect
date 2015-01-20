<?php
require_once("./classes/class_Moment.php");
include_once("./db_connect.inc");

function filterMoments($workingCopy, $maxUseTimestamp) {
	$counter = array();
	$toBeUnset = array();
	foreach($workingCopy as $row) 
	{
		if($row['UNIX_TIMESTAMP(u.used)'] != null && $row['UNIX_TIMESTAMP(u.used)'] >= $maxUseTimestamp)
			$toBeUnset[$row['id']] = true;
		elseif($row['UNIX_TIMESTAMP(u.used)'])
			$counter[$row['id']]++;
	}
	foreach($workingCopy as $key => $row) 
	{
		$id = $row['id'];
		if($counter[$id] >= 3 || $toBeUnset[$id]) {
			unset($workingCopy[$key]);
		}
	}

	return array_values($workingCopy);
}

switch($progress) {
	default: // new conflict to be set and specified
		$instance = Conflict::fromNew($actual_user);
		$conflict_id = $instance->id;
		$user_objects[$actual_user]->conflicts_active[$conflict_id] = $instance;

	case 1;
		if(isset($id))
		{
			if(!isset($conflict_id) && isset($_GET['conflict_id']))
			{
				$conflict_id = $_GET['conflict_id'];
			}
			elseif(!isset($conflict_id))
			{
				$GLOBALS['log']->error("Error: On this step a conflict id needs to be specified.",__FILE__,__LINE__,null,true);	
			}
			$user_objects[$actual_user]->conflicts_active[$conflict_id]->setCreated_with($id);
		}
		else
			$GLOBALS['log']->error("Error: A conflict opponent needs to be specified.",__FILE__,__LINE__,null,true);


	case 2; // First Step of conflict solving
		
		if(!isset($conflict_id) && isset($id))
			$conflict_id == $id;
		elseif(!isset($conflict_id))
			$GLOBALS['log']->error("Error: On this step the conflict needs to be already specified by an id (via $_GET)",__FILE__,__LINE__,null,true);

		$startway = 2;//rand(1,2); FIXME:
		switch($startway) {
			case 1:
				echo "<h2>Did you ever act in a similar way?</h2>";
				echo "<textarea placeholder='Explain the situation you've acted in the same way.' required></textarea>";
			break;

			case 2:
				$daysSinceUse = .5;
				$maxUseTimestamp = time() -($daysSinceUse*24*60*60);

				if(!$moment_db_outcome = $db->query("
					SELECT
						m.id,
						m.created_by,
						m.created_with,
						m.type,
						m.path,
						m.content,
						m.rating,
						UNIX_TIMESTAMP(u.used)
					FROM
						moments as m
					LEFT JOIN
						moments_use as u
					ON
						u.moment = m.id
						AND
						u.user = ".$actual_user."
					WHERE 
						(
							m.created_by = ".$actual_user." 
							AND 
							m.created_with = ".$id."
						)
						OR
						(
							m.created_with = ".$actual_user." 
							AND 
							m.created_by = ".$id."
						)
					ORDER BY
						m.rating DESC,
						u.used ASC
				"))
				{
					$GLOBALS['log']->error("Database Error",__FILE__,__line__,$db->error,true);
				}
				else
				{
					// Filter DB results
					$moments= array();
					while($moments[] = mysqli_fetch_assoc($moment_db_outcome));
					$moment_row = filterMoments($moments, $maxUseTimestamp)[0];

					if(count($moment_row)) // If at least one fitting positive moment was found..
					{
						$moment = Moment::fromRowArray($moment_row);

						if($moment->show()) {
							$moment->setUse($actual_user);
						}

						$user_objects[$actual_user]->conflicts_active[$conflict_id]->setMoment_used($moment->id, false);
						$user_objects[$actual_user]->conflicts_active[$conflict_id]->setProgress(2); // FIXME
						// FIXME: Add: richtig weiterverlinken
					}
					else // .. else if not
					{
						echo "letzte pos handlung";
						// Frage nach letzter Positiver Handlung
					}
				}

			break;
		}
		echo "<a href='?state=add_moment&progress=".($progress+1)."&id=".$conflict_id."'>weiter</a>\n";
	break;
}
?>