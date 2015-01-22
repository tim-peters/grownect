<?php
	$conflicts_to_specify = array();
	foreach($user_objects[$actual_user]->conflicts_active as $conflict_active)
	{
		if($conflict_active->progress <= 3) 
			$conflicts_to_specify[$conflict_active->progress][] = $conflict_active->id;
	}

	if(isset($conflicts_to_specify[0]))
	{
		$amount_unspecified = count($conflicts_to_specify[0]);
		if($amount_unspecified == 1) {
			$content['body'] .= "<h2>Tab on the person you're in trouble with</h2>\n";
			$content['body'] .= showUserbar($user_objects, $actual_user, null, "add_conflict&conflict_id=".$conflicts_to_specify[0][0]);
		}
		else
		{
			$content['body'] .= showUserbar($user_objects, $actual_user, null, "add_conflict&conflict_id=".$conflicts_to_specify[0][0]);
			$content['body'] .= "<h2>You have ".count($conflicts_to_specify)." open conflicts you need to specify.</h2>\n";
			$content['body'] .= "<strong>Please choose the user, you were in trouble with at ".date('r', $user_objects[$actual_user]->conflicts_active[current($conflicts_to_specify)[0]]->created)."</strong>\n";
		}
	}
	else
	{
		$content['body'] .= showUserbar($user_objects, $actual_user);
		if(count($conflicts_to_specify) > 0)
		{
			$next_progress = key($conflicts_to_specify)+1;
			$content['body'] .= "<h2>You have ".count($conflicts_to_specify)." open conflicts you need to specify.</h2>\n";
			$content['body'] .= "<a href='?state=add_conflict&progress=".$next_progress."&id=".current($conflicts_to_specify)[0]."'>Tab here, to specify your conflict with ".$user_objects[$user_objects[$actual_user]->conflicts_active[current($conflicts_to_specify)[0]]->created_with]->name." (Created ".date('r', $user_objects[$actual_user]->conflicts_active[current($conflicts_to_specify)[0]]->created).")</a>\n";
		}
	}
?>