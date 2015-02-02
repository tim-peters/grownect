<?php
require_once("./classes/class_Moment.php");
include_once("./db_connect.inc");


/**
 * removes all moments which have been used since $maxUseTimestamp or have been used more than 3 times (by this user) at all.
 * @param  SQL_assoc_array 	$workingCopy     A result of a SQL Request
 * @param  timestamp 		$maxUseTimestamp 
 * @return array            Array of Entries (SQL rows?)
 */
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
	default: // CREATE NEW CONFLICT

		function findUndefinedConflicts($opponent,$src_array) {
			foreach($src_array as $id => $conflict)
			{
				if($conflict->progress < 2 && $conflict->created_with == $opponent)
					return $id;
			}
		};

		if(isset($id) && $conflict_id = findUndefinedConflicts($id, $user_objects[$actual_user]->conflicts_active)) // FIXME: Gibt es diesen Fall überhaupt real?
		{
			$actual_conflict = &$user_objects[$actual_user]->conflicts_active[$conflict_id];
			$GLOBALS['log']->event("Conflict (id:".$conflict_id.") found to continue",__FILE__,__LINE__);
		}
		else
		{
			$actual_conflict = Conflict::fromNew($actual_user);
			$conflict_id = $actual_conflict->id;
			$user_objects[$actual_user]->conflicts_active[$conflict_id] = &$actual_conflict;
			$GLOBALS['log']->event("Created new Conflict (id:".$conflict_id.")",__FILE__,__LINE__);
			$progress = 1;
		}


	case 1; // SET CONFLICT OPPONENT
		if(isset($id))
		{
			if(!isset($conflict_id))
				$GLOBALS['log']->error("On this step a conflict id needs to be specified.",__FILE__,__LINE__,null,true);	

			if(!isset($actual_conflict))
				$actual_conflict = &$user_objects[$actual_user]->conflicts_active[$conflict_id];

			$actual_conflict->setCreated_with($id);
			$GLOBALS['log']->event("Conflict opponent set to ".$id." (id:".$conflict_id.")",__FILE__,__LINE__);
			header("Location: ?state=".$state."&progress=2&conflict_id=".$conflict_id);
		}
		else
			$GLOBALS['log']->error("A conflict opponent needs to be specified.",__FILE__,__LINE__,null,true);
	break;

	case 2: // CHECK PULS AND CALM DOWN
		if(!isset($conflict_id))
			$GLOBALS['log']->error("On this step a conflict id needs to be specified.",__FILE__,__LINE__,null,true);	
		
		if(!isset($actual_conflict))
			$actual_conflict = &$user_objects[$actual_user]->conflicts_active[$conflict_id];

		$redirect_url = "?state=".$state."&progress=3&conflict_id=".$conflict_id;

		if($actual_conflict->progress < $progress)
		{
			$user_objects[$actual_user]->sendToBracelet('checkPulse');
			$content['js'][] = "./js/custom/screamCheck.js";
			$content['script'] .= "var techID = '".$user_objects[$actual_user]->tech_id."';\n";
			$content['script'] .= "var redirectURI = '".$redirect_url."';\n";
			$content['body'] .= "<strong class=\"message\">checking for pulse</strong>\n";
			// FIXME: Enhancement: Fallback if Braclet is offline
			 
			$actual_conflict->setProgress(2); // FIXME: Enhancement: Erst nach Bestätigung der Clientinteraktion ausführen
			$GLOBALS['log']->event("Conflict: Pulse check intitiated (id:".$conflict_id.")",__FILE__,__LINE__);
		}
		else
		{
			$GLOBALS['log']->event("Conflict: Conflict->progress to high for step ".$progress." (Conflict->progress: ".$actual_conflict->progress."). Redirect...",__FILE__,__LINE__);
			header("Location: ".$redirect_url);
		}
	break;


	case 3; // First Step of conflict solving
		
		if(!isset($conflict_id))
			$GLOBALS['log']->error("On this step the conflict needs to be already specified by an id (via $_GET)",__FILE__,__LINE__,null,true);
		
		if(!isset($actual_conflict))
			$actual_conflict = &$user_objects[$actual_user]->conflicts_active[$conflict_id];

		$redirect_url = "?state=add_conflict&progress=".($progress+1)."&conflict_id=".$conflict_id;

		if($actual_conflict->progress < $progress)
		{
			$GLOBALS['log']->event("Conflict: First step of conflict solving initiated (id:".$conflict_id.")",__FILE__,__LINE__);
			$startway = rand(1,2);
			switch($startway) {
				case 1:
					$content['js'][] = "./js/custom/textareaVoiceRecognition.js";
					$content['script'] .= "var techID = \"".$user_objects[$actual_user]->tech_id."\";";
					$content['body'] .= "<h2>Did you ever act in a similar way?</h2>";
					$content['body'] .= "<textarea placeholder='Explain the situation you've acted in the same way.' id=\"".createHash()."\" required></textarea>";
					$GLOBALS['log']->event("Conflict: Displayed 'explain situation' site (id:".$conflict_id.")",__FILE__,__LINE__);
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
							u.user = '".$actual_user."'
						WHERE 
							(
								m.created_by = '".$actual_user."' 
								AND 
								m.created_with = '".$actual_conflict->created_with."'
							)
							OR
							(
								m.created_with = '".$actual_user."' 
								AND 
								m.created_by = '".$actual_conflict->created_with."'
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

							if($content['body'] .= $moment->show()) {
								$moment->setUse($actual_user);
							}

							$user_objects[$actual_user]->conflicts_active[$conflict_id]->setMoment_used($moment->id, false);
							$user_objects[$actual_user]->conflicts_active[$conflict_id]->setProgress(2); // FIXME
							$GLOBALS['log']->event("Conflict: Showed the moment with id ".$moment->id." (id:".$conflict_id.")",__FILE__,__LINE__);
						}
						else // .. else if not
						{	// Frage nach letzter Positiver Handlung
							$content['js'][] = "./js/custom/textareaVoiceRecognition.js";
							$content['script'] .= "var techID = \"".$user_objects[$actual_user]->tech_id."\";";
							$content['body'] .= "<h2>What has ".$user_objects[$id]->name." done for you?</h2>";
							$content['body'] .= "<textarea placeholder='Describe something positive ".$user_objects[$actual_conflict->created_with]->name." has done for or with you.' id=\"".createHash()."\" required></textarea>";
							$GLOBALS['log']->event("Conflict: Asked for last positive thing (id:".$conflict_id.")",__FILE__,__LINE__);
							
						}
					}
				break;
			}
			$actual_conflict->setProgress(3);
			$content['body'] .= "<a href='".$redirect_url."'>weiter</a>\n";
		}
		else
			header("Location: ".$redirect_url);
	break;

	case 4: // declare feelings
		if(!isset($conflict_id))
			$GLOBALS['log']->error("On this step the conflict needs to be already specified by an id (via $_GET)",__FILE__,__LINE__,null,true);

		if(!isset($actual_conflict))
			$actual_conflict = &$user_objects[$actual_user]->conflicts_active[$conflict_id];

		$redirect_url = "?state=add_conflict&progress=".($progress+1)."&conflict_id=".$conflict_id;	

		if($actual_conflict->progress < $progress)
		{
			if($_POST['feelings'])
			{
				$angry = cutToRange($_POST['angry'],100);
				$confused = cutToRange($_POST['confused'],100);
				$disappointed = cutToRange($_POST['disappointed'],100);
				$score = cutToRange(intval(0.4*$angry)+intval(0.1*$confused)+intval(0.5*$disappointed),100);

				$actual_conflict->setWeight($score);
				$GLOBALS['log']->event("Conflict: processed feelings and redirected (id:".$conflict_id.")",__FILE__,__LINE__);
				header("Location: ".$redirect_url);
			}
			else
			{
				$content['body'] .= "<h1>How do you feel?</h1>\n";
				$content['body'] .= "<form action=\"\" method=\"POST\">\n";
				$content['body'] .= "<p><input type=\"range\" name=\"angry\" min=\"0\" max=\"100\" ></p>\n";
				$content['body'] .= "<p><input type=\"range\" name=\"confused\" min=\"0\" max=\"100\" ></p>\n";
				$content['body'] .= "<p><input type=\"range\" name=\"disappointed\" min=\"0\" max=\"100\" ></p>\n";
				$content['body'] .= "<button type=\"submit\" name=\"feelings\" value=\"val\">Continue</button>\n";
				$content['body'] .= "</form>\n";
				$GLOBALS['log']->event("Conflict: Asked for feelings (id:".$conflict_id.")",__FILE__,__LINE__);

			}
		}
		else
			header("Location: ".$redirect_url);
	break;

	case 5: // LÜCKENTEXT
		if(!isset($conflict_id))
			$GLOBALS['log']->error("On this step the conflict needs to be already specified by an id (via $_GET)",__FILE__,__LINE__,null,true);

		if(!isset($actual_conflict))
			$actual_conflict = &$user_objects[$actual_user]->conflicts_active[$conflict_id];

		$redirect_url = "?state=".$state."&progress=".($progress+1)."&conflict_id=".$conflict_id;	

		if($actual_conflict->progress < $progress)
		{
			if($_POST['cloze'])
			{
				// Auswertung
				$event = secureString($_POST['event']);
				$wish = secureString($_POST['wish']);
				$time = (isset($_POST['time'])) ? cutToRange($_POST['time'], 1440) : null ;

				$actual_conflict->setDescription($event,false);
				$actual_conflict->setImprovements($wish,false);
				if(isset($time))
					$actual_conflict->setTime_costs($time,false);
				
				if($actual_conflict->weight > 80)
				{
					$actual_conflict->setProgress(5);
					$GLOBALS['log']->event("Conflict: Processed cloze and redirected to reminder site (id:".$conflict_id.")",__FILE__,__LINE__);
					header("Location: ".$redirect_url);
				}
				else
				{
					$actual_conflict->setProgress(6);
					$GLOBALS['log']->event("Conflict: Processed cloze and redirected to bracelet send site (id:".$conflict_id.")",__FILE__,__LINE__);
					header("Location: ?state=".$state."&progress=".($progress+2)."&conflict_id=".$conflict_id);
				}
			}
			else
			{
				$content['body'] .= "<h1>About the Conflict</h1>\n";
				$content['body'] .= "<form action=\"\" method=\"POST\">\n";
				$content['body'] .= "<p>What bothers me, is that <textarea name=\"event\" class=\"small\" id=\"".createHash()."\" required></textarea></p>\n";
				$content['body'] .= "<p>The solution could be / I wish <textarea name=\"wish\" class=\"small\" id=\"".createHash()."\" placeholder=\"what is needed to solve the conflict and/or to avoid this problem in the future?\" required></textarea></p>\n";
				$content['body'] .= "<p>It took me <input name=\"time\" type=\"number\" min=\"0\" max=\"1440\" /> minutes longer</p>\n";
				$content['body'] .= "<button type=\"submit\" name=\"cloze\" value=\"val\">Continue</button>\n";
				$content['body'] .= "</form>\n";
				$GLOBALS['log']->event("Conflict: showed sloze (id:".$conflict_id.")",__FILE__,__LINE__);
			}
		}
		else
			header("Location: ".$redirect_url);
	break;

	case 6: // ADD REMINDER
		if(!isset($conflict_id))
			$GLOBALS['log']->error("On this step the conflict needs to be already specified by an id (via $_GET)",__FILE__,__LINE__,null,true);

		if(!isset($actual_conflict))
			$actual_conflict = &$user_objects[$actual_user]->conflicts_active[$conflict_id];

		$redirect_url = "?state=".$state."&progress=".($progress+1)."&conflict_id=".$conflict_id;

		if($actual_conflict->progress < $progress)
		{
			// FIXME: Enhancement: Übersicht anzeigen
			$content['body'] .= "<h1>Add Reminder</h1>\n";
		}
		else
			header("Location: ".$redirect_url);
	break;

	case 7:
		if(!isset($conflict_id))
			$GLOBALS['log']->error("On this step the conflict needs to be already specified by an id (via $_GET)",__FILE__,__LINE__,null,true);

		if(!isset($actual_conflict))
			$actual_conflict = &$user_objects[$actual_user]->conflicts_active[$conflict_id];

		if($actual_conflict->progress == 6) 
		{
			$user_objects[$actual_conflict->created_with]->sendToBracelet("conflictCreated",['value' => $actual_user->color, 'conflict' => $conflict_id, 'text' => $user_objects[$actual_conflict->created_with]->name.' has a Problem. Do you have an idea what it could be about?']);
			$GLOBALS['log']->event("Conflict: Sent out conflictCreated event",__FILE__,__LINE__);
			//$actual_conflict->setProgress($state); // FIXME: uncomment
			$content['body'] .= "<h3>Successfully created</h3>\n";
		}
		else
			$GLOBALS['log']->error("On this Step, the conflict needs to be definied completely but not be finsihed",__FILE__,__LINE__,null,true);
	break;
}
$content['body'] .= showHomebutton();
?>