<?php
	$conflicts_to_specify = array();
	foreach($user_objects[$actual_user]->conflicts_active as $conflict_active)
	{
		if($conflict_active->progress <= 3) 
			$conflicts_to_specify[$conflict_active->progress][] = $conflict_active->id;
	}

	if(count($conflicts_to_specify[0]) > 0)
	{
		echo showUserbar($user_objects, $actual_user, null, "add_conflict");
		echo "<h2>You have ".count($conflicts_to_specify)." open conflicts you need to specify.</h2>\n";
		echo "<strong>Please choose the user, you were in trouble with at ".date('r', $user_objects[$actual_user]->conflicts_active[current($conflicts_to_specify)[0]]->created)."</strong>\n";
	}
	else
	{
		echo showUserbar($user_objects, $actual_user);
		if(count($conflicts_to_specify) > 0)
		{
			$next_progress = key($conflicts_to_specify)+1;
			echo "<h2>You have ".count($conflicts_to_specify)." open conflicts you need to specify.</h2>\n";
			echo "<a href='?state=add_conflict&progress=".$next_progress."&id=".current($conflicts_to_specify)[0]."'>Tab here, to specify your conflict with ".$user_objects[$user_objects[$actual_user]->conflicts_active[current($conflicts_to_specify)[0]]->created_with]->name." (Created ".date('r', $user_objects[$actual_user]->conflicts_active[current($conflicts_to_specify)[0]]->created).")</a>\n";
		}
	}
?>