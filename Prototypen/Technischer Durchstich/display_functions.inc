<?php
/**
 * Draws a navigation bar to choose interactions between users
 * @param  User[]	$user_objects	Array of all initialized User objects
 * @param  int  	$as 			Id of the users who is actually using the mirror
 * @param  boolean 	$disabled 		sould the user be able to choose other options?
 * @return String 					HTML-Strukture
 */
function showUserbar (&$user_objects, $as, $disabled = false) {
	$content = [
		"self" => [
			[
				"icon" => "edit",
				"state" => "edit_profile"
			],
			[
				"icon" => "stat",
				"state" => "show_statistics"
			]
		],
		"other" => [
			[
				"icon" => "add",
				"state" => "new_conflict"
			],
			[
				"icon" => "moment",
				"state" => "add_moment"
			]
		]
	];

	$output  = "<nav>\n";
	$output .= "<ul>\n";
	foreach($user_objects as $user_object) {
		$is_self = ($user_object->id == $as);
		$output .= ($is_self) ? "<li class='self'" : "<li";
		$output .= " style='color: ".$user_object->color."'>\n	";
		if($disabled) $output .= "<a href='?state=start'>";
		$output .= "<img src='".$user_object->picture."' alt='".$user_object->name."'>";
		if($disabled) $output .= "</a>\n";
		else
		{
			$output .= "\n	<ul>\n";
			$user_key = ($is_self) ? "self" : "other";
			foreach($content[$user_key] as $option)
			{
				$output .= "		<li>\n";
				$output .= "			<a href='?state=".$option['state']."'>".$option['icon']."</a>\n";
				$output .= "		</li>\n";
			}
			$output .= "	</ul>\n";
		}
		$output .= "</li>\n"; 
	}
	$output .= "</ul>\n";
	$output .= "</nav>\n";

	return $output;
}

?>