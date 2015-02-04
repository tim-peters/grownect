<?php

$actual_conflict = Conflict::fromDb($_GET['conflict_id']);
$content['body'] .= showUserbar($user_objects, $actual_conflict->created_with, $actual_conflict->created_by);

$content['body'] .= "<div class=\"error\">\n";
$content['body'] .= "	Your conflict needs to be solved in person.\n";
$content['body'] .= " 	<div id=\"flash\">\n";
$content['body'] .= " 		<img src=\"img/flash.png\" width=\"400\">\n";
$content['body'] .= " 	</div>\n";
$content['body'] .= " \n";
$content['body'] .= " </div>\n";

?>