<?php
	$conflicts_just_opened = array();
	foreach($user_objects[$actual_user]->conflicts_active as $conflict_active)
	{
		//var_dump($conflict_active);
		if($conflict_active->progress == 0) $conflicts_just_opened[] = $conflict_active->id;
	}
	if(count($conflicts_just_opened) > 0)
	{
		echo showUserbar($user_objects, $actual_user, null, "add_conflict");
		echo "<h2>You have ".count($conflicts_just_opened)." open conflicts you need to specify.</h2>\n";
		echo "<strong>Please choose the user, you were in trouble with at ".date('r', $user_objects[$actual_user]->conflicts_active[$conflicts_just_opened[0]]->created)."</strong>\n";
	}
	else
		echo showUserbar($user_objects, $actual_user);
?>