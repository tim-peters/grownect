<?php
$content['body'] .= showUserbar($user_objects, $actual_user, $id);

$content['js'][] = "./js/core/jquery.knob.js";
$content['js'][] = "./js/custom/statistics.js";

$result = $db->query("SELECT count(*) AS 'n' FROM conflicts");
$amount_conflicts = $result->fetch_object()->n;
$result->free();

$result = $db->query("SELECT count(*) AS 'n' FROM conflicts WHERE solved > 0 ");
$amount_solved_conflicts = $result->fetch_object()->n;
$result->free();

$amount_open_conflicts = $amount_conflicts-$amount_solved_conflicts;


$result = $db->query("SELECT count(*) AS 'n' FROM conflicts WHERE solved > 0 && created_with = '".$actual_user."'");
$amount_my_solved_conflicts = $result->fetch_object()->n;
$result->free();

$amount_my_open_conflicts = count($user_objects[$actual_user]->conflicts_passive);
$amount_my_created_conflicts = count($user_objects[$actual_user]->conflicts_active);

$content['body'] .= "<h2>Your conflicts</h2>\n";
$content['body'] .= "	<ul class=\"topvalues\"> \n";
$content['body'] .= "		<li><input type=\"text\" value=\"".$amount_my_solved_conflicts."\" class=\"dial\" data-min=\"0\" data-max=\"".$amount_solved_conflicts."\"></li> \n";
$content['body'] .= "		<li><input type=\"text\" value=\"".$amount_my_open_conflicts."\" class=\"dial\" data-min=\"0\" data-max=\"".$amount_open_conflicts."\"></li>\n";
$content['body'] .= "		<li><input type=\"text\" value=\"".$amount_my_created_conflicts."\" class=\"dial\" data-min=\"0\" data-max=\"".$amount_conflicts."\"></li>  \n";
$content['body'] .= "	</ul>\n";
$content['body'] .= "	<div class=\"statistic\"><img src=\"img/statisticexample.png\"></div>\n";

$content['body'] .= "<button type=\"button\" onClick=\"window.location.href='./'\" class=\"cancel\"><img src=\"img/cancel.png\" width=\"25\">Back to homescreen</button>\n";
?>