<?php
/**
 * Draws a navigation bar to choose interactions between users
 * @param  User[]	$user_objects	Array of all initialized User objects
 * @param  int  	$as 			Id of the users who is actually using the mirror
 * @param  int 		$with 			Id of the user the main user is ineracting with (disabled several functions if set)
 * @return String 					HTML-Strukture
 */
function showUserbar (&$user_objects, $as, $with = null) {
	$content = [ // Set the structure: Which icons should be displayed when (& how)?
		"self" => [ // structure for the user himself
			[
				"icon" => "edit",
				"state" => "edit_profile"
			],
			[
				"icon" => "stat",
				"state" => "show_statistics"
			]
		],
		"other" => [ // structure for the icons of the other users
			[
				"icon" => "add",
				"state" => "new_conflict",
				"id" => true
			],
			[
				"icon" => "moment",
				"state" => "add_moment",
				"id" => true
			]
		]
	];

	// Fill the structure (above) with content and generate the HTML outcome
	$output  = "<nav>\n";
	$output .= "<ul>\n";
	foreach($user_objects as $user_object) { // for each existing user object
		
		$is_self = ($user_object->id == $as); // check whether it's the viewer himself or not

		$output .= ($is_self) ? "<li class='self'" : "<li"; // create a <li>-tag; if it's the viewer himself, add a class ("self")
		$output .= " style='color: ".$user_object->color."'>\n	"; // set the css-color to the color of the user (important for the colored box-shadow)
		
		if(isset($with)) $output .= "<a href='?state=start'>"; // if a user is chosen (e.g. while creating a event with him), every image should link back to the startpage
		
		$output .= "<img src='".$user_object->picture."' alt='".$user_object->name."'>"; // add the user's image (with his name as fallback)
		
		if(isset($with)) // the link-tag from above needs to be closed (of it exists)
			$output .= "</a>\n";
		else // if not: means: If there is not interaction in progress
		{
			// display the dropdown menu for each user
			$output .= "\n	<ul>\n";

			// use the right structure, depending on whether it's the icon of the user himself ot another
			$user_key = ($is_self) ? "self" : "other";
			foreach($content[$user_key] as $option) // then for each part of the struktre (above)...
			{
				$output .= "		<li>\n"; // create a icon
				$output .= "			<a href='?state=".$option['state']; // with the right adress
				if(isset($option['id'])) $output .= "&id=".$user_object->id; // including the id (if nessesary)
				$output .= 				"''>".$option['icon']."</a>\n";
				$output .= "		</li>\n";
			}
			$output .= "	</ul>\n";
		}

		$output .= "</li>\n"; 
	}

	$output .= "</ul>\n";
	$output .= "</nav>\n";

	return $output; // return the whole html store in the $output variable
}

?>