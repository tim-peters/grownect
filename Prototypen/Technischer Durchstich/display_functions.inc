<?php
/**
 * Draws a navigation bar to choose interactions between users
 * @param  User[]	$user_objects	Array of all initialized User objects
 * @param  int  	$as 			Id of the users who is actually using the mirror
 * @param  int 		$with 			Id of the user the main user is ineracting with (disabled several functions if set)
 * @return String 					HTML-Strukture
 */
function showUserbar (&$user_objects, $as, $with = null, $choose_mode = null) {
	$content = [ // Set the structure: Which icons should be displayed when (& how)?
		"self" => [ // structure for the user himself
			[
				"icon" => "<br><br><img src=\"img/profile.png\" width=\"20\"><br><br>show<br>profile",
				"state" => "edit_profile"
			],
			[
				"icon" => "<img src=\"img/statistic.png\" width=\"20\"><br><br>show<br>conflicts",
				"state" => "show_statistics"
			]
		],
		"other" => [ // structure for the icons of the other users
			[
				"icon" => "<img src=\"img/conflict-w.png\" width=\"25\"><br><br>add<br>conflict",
				"state" => "add_conflict",
				"id" => true
			],
			[
				"icon" => "<br><br><img src=\"img/moment-w.png\" width=\"25\"><br><br>create<br>moment",
				"state" => "add_moment",
				"id" => true
			]
		]
	];

	// Fill the structure (above) with content and generate the HTML outcome
	$output .= "<div class=\"usercontainer";
	if(isset($with)) $output .= " small";
	$output .= "\">\n";
	foreach($user_objects as $user_object) { // for each existing user object
		
		$is_self = ($user_object->id == $as); // check whether it's the viewer himself or not
		$is_active = ($user_object->id == $with); // check whether it's an affected user or not

		if(!(isset($choose_mode) && $is_self))
		{
			if(isset($choose_mode)) $output .= "<a href='?state=".$choose_mode."&id=".$user_object->id."&progress=1'>";

			$output .= "<div class=\"user"; // create a li-tag
			// if it's the viewer himself, add a class ("self"); if it's an other user which is affekted by the running interaction, add class "active"
			if($is_self && isset($with))
				$output .= " clickeduser\" id=\"recognizeduser";
			elseif($is_self)
				$output .= "\" id=\"recognizeduser";
			elseif($is_active)
				$output .= " clickeduser";

			$output .= "\">\n";
			$output .= "<div class=\"openconflicts\" style='background-color:".$user_object->color."'><br>".count($user_object->conflicts_passive)."</div>\n";
			$output .= "<img src=\"".$user_object->picture."\" alt=\"".$user_object->name."\">\n";

			if(isset($choose_mode)) $output .= "</a>\n";
			
			elseif(!isset($with)) // if not: means: If there is not interaction in progress
			{
				// display the dropdown menu for each user
				$output .= "\n	<div class=\"menu\">\n";
				$output .= "	<ul>\n";

				// use the right structure, depending on whether it's the icon of the user himself ot another
				$user_key = ($is_self) ? "self" : "other";
				//var_dump($content);
				foreach($content[$user_key] as $option) // then for each part of the struktre (above)...
				{
					$output .= "		<li>\n"; // create a icon
					$output .= "			<a href='?state=".$option['state']; // with the right adress
					if(isset($option['id'])) $output .= "&id=".$user_object->id; // including the id (if nessesary)
					$output .= 				"'>".$option['icon']."</a>\n";
					$output .= "		</li>\n";
				}
				$output .= "	</ul>\n";
				$output .= "	</div>\n";
			}

			$output .= "</div>\n"; 
		}
	}

	$output .= "</div>\n";

	return $output; // return the whole html store in the $output variable
}

function showHomebutton() {
	$output = "<a href=\"./\" title=\"Back to homescreen\">Cancel</a>\n";

	return $output;
}

function showNextButton($redirect_url) {
	$output = "<a href=\"".$redirect_url."\" title=\"Continue with next step\">Continue</a>\n";

	return $output;
}

?>