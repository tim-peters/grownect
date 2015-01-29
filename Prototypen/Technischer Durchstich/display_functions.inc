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
				"icon" => "edit profile",
				"state" => "edit_profile"
			],
			[
				"icon" => "my conficts",
				"state" => "show_statistics"
			]
		],
		"other" => [ // structure for the icons of the other users
			[
				"icon" => "create conflict",
				"state" => "add_conflict",
				"id" => true
			],
			[
				"icon" => "add moment",
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
		$is_active = ($user_object->id == $with); // check whether it's an affected user or not

		$output .= "<li class='"; // create a li-tag
		// if it's the viewer himself, add a class ("self"); if it's an other user which is affekted by the running interaction, add class "active"
		if($is_self)
			$output .= "self";
		elseif($is_active)
			$output .= "active";

		$output .= "' style='color: ".$user_object->color."'>\n	"; // set the css-color to the color of the user (important for the colored box-shadow)
		$output .= "<span style='background: ".$user_object->color."' class='amount_issues'>".count($user_object->conflicts_passive)."</span>\n";

		/*if(isset($with)) $output .= "<a href='?state=start'>"; // if a user is chosen (e.g. while creating a event with him), every image should link back to the startpage
		else*/if(isset($choose_mode) && !$is_self) $output .= "<a href='?state=".$choose_mode."&id=".$user_object->id."&progress=1'>";

		$output .= "<img src='".$user_object->picture."' alt='".$user_object->name."'>"; // add the user's image (with his name as fallback)
		
		//if(isset($with) || (isset($choose_mode) && !$is_self)) // the link-tag from above needs to be closed (of it exists)
		if(isset($choose_mode) && !$is_self)
			$output .= "</a>\n";
		elseif(!isset($with)) // if not: means: If there is not interaction in progress
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
				$output .= 				"'>".$option['icon']."</a>\n";
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

function showHomebutton() {
	$output = "<a href=\"./\" title=\"Back to homescreen\">Cancel</a>\n";

	return $output;
}

?>