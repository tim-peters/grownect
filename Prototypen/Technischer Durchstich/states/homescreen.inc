<?php
if(isset($user_objects[$actual_user]))
{
	$conflicts_to_specify = array();
	foreach($user_objects[$actual_user]->conflicts_active as $conflict_active)
	{
		if($conflict_active->progress <= 5)
			$conflicts_to_specify[$conflict_active->progress][] = $conflict_active->id;
	}

	if(isset($conflicts_to_specify[0]))
	{
		$amount_unspecified = count($conflicts_to_specify[0]);
		if($amount_unspecified == 1) {
			$content['body'] .= "<h2>You just added a conflict.<br>Tab on the person you're in trouble with</h2>\n";
			$content['body'] .= showUserbar($user_objects, $actual_user, null, "add_conflict&conflict_id=".$conflicts_to_specify[0][0]);
		}
		else
		{
			$content['body'] .= "<h2>You just added ".count($conflicts_to_specify)." conflicts. Tab on the person, you were in trouble with ".intval(($user_objects[$actual_user]->conflicts_active[current($conflicts_to_specify)[0]]->created - time())/60)." minutes ago</h2>\n";
			$content['body'] .= showUserbar($user_objects, $actual_user, null, "add_conflict&conflict_id=".$conflicts_to_specify[0][0]);
		}
	}
	else
	{
		$content['body'] .= showUserbar($user_objects, $actual_user);

		function countOpenConflict($array) {
			$n = 0;
			foreach($array as $part)
			{
				if(is_array($part))
					$n += count($part);
			}
			return $n;
		}

		if(count($conflicts_to_specify) > 0)
		{
			$content['body'] .= "<div class=\"messages\">\n";
			$next_progress = key($conflicts_to_specify)+1;
			$amount_open_conflicts = countOpenConflict($conflicts_to_specify); // Second Layer Array parts
			$content['body'] .= "<h2>You have ".$amount_open_conflicts." open conflicts, you need to specify.</h2>\n";
			$content['body'] .= "<a href='?state=add_conflict&progress=".$next_progress."&conflict_id=".current($conflicts_to_specify)[0]."'>Tab here, to specify your conflict with ".$user_objects[$user_objects[$actual_user]->conflicts_active[current($conflicts_to_specify)[0]]->created_with]->name." <br>(Created ".date('r', $user_objects[$actual_user]->conflicts_active[current($conflicts_to_specify)[0]]->created).")</a>\n";
			$content['body'] .= "</div>\n";
		}
	}
}
?>