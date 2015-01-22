<?php
error_reporting(E_ALL ^ E_NOTICE);
require_once("./classes/class_Log.php");

// Importing classes
require_once("./classes/class_Conflict.php");
require_once("./classes/class_User.php");

// Importing dependencies
include_once("./db_connect.inc");
include_once("./display_functions.inc");

// Instanciate User Classes
$user_objects = array(); // create an empty array named $user_objects
if($user_db_content = $db->query("SELECT id FROM users")) // get all User with their id from database
while($row = $user_db_content->fetch_object()) { // for each user...
	$user_objects[$row->id] = User::fromDb($row->id); // create an object from class User, filled with atrributes from database and store it in the $user_objects array
}
$user_db_content->close();

// Declaring GET-Variables as regular variables
$state = $_GET['state'];
$progress = $_GET['progress'];
$id = $_GET['id'];

// Set the user which is actually viewing/acting
if(isset($_GET['change_user'])) // if a new user is getting set
{
	setcookie("user",$_GET['change_user']); // set a cookie to "remember" this choice
	$actual_user = $_GET['change_user'];
}
elseif(isset($_COOKIE['user'])) // if a user has been set before
	$actual_user = $_COOKIE['user'];
else // fallback
	$actual_user = -1;

$content = [
	"head" => "",
	"title" => "",
	"style" => "",
	"script" => "$(\"nav ul > li\").click(function() {\n	$(this).toggleClass(\"open\");\n	$(\"nav .open\").not(this).removeClass(\"open\");\n});",
	"body" => "",
	"css" => [
		"./style/all.css",
		"./style/main.css"
	], 
	"js" => [
		"./js/core/jquery.js"
	]
];
//////////////////////////////////////////

//echo showUserbar($user_objects, $actual_user);
if($actual_user < 0) $state = "sign_up"; // if a user has not been initialized already: show him the sign up screen
switch($state) {


	case "sign_up": {
		include("./states/sign_up.inc");
	break; }


	case "homescreen":
	case "start":
	default:
		include("./states/homescreen.inc");
	break;


	case "add_moment":
		include("./states/add_moment.inc");
	break;


	case "add_conflict": {
		include("./states/add_conflict.inc");
	break; }


	//default:
	//	$content['body'] .= "<a href='?state=start'><img src='./img/mirror_states/welcome.jpg'></a>";
}

echo "<!DOCTYPE html>\n";
echo "<html>\n";
echo "<head>\n";
echo "<title>".$content['title']."</title>\n";
foreach ($content['css'] as $css_path) {
	echo "<link rel=\"stylesheet\" href=\"".$css_path."\" type=\"text/css\" />\n";
}
echo $content['head']."\n";
if($content['style'] != "")
{
	echo "<style type=\"text/css\">\n";
	echo $content['style']."\n";
	echo "</style>\n";
}
echo "</head>\n";
echo "<body>\n";
echo "<div class=\"free\" style=\"width:500px;margin:0 auto;\">\n";
echo "	<form action=\"\" method=\"GET\">\n";
echo "		Act/View as \n";
echo "		<select name=\"change_user\" size=\"1\">\n";
			if($user_db_content = $db->query("SELECT id, name FROM users"))
			{
				echo "			<option value='-1'> - </option>\n"; 
				while($row = $user_db_content->fetch_object())
				{
					echo "			<option value='".$row->id."'";
					if($row->id == $actual_user) echo " selected";
					echo ">".$row->name."</option>\n"; 
				}
			}
echo "		</select>\n";
echo "		<input type=\"submit\" />\n";
echo "	</form>\n";
echo "</div>\n";
echo "<section id=\"mirror\">\n";
echo "	<div class=\"mirror\">";
//echo "View of ".$user_objects[$actual_user]->name."<br>\n";

echo $content['body']."\n\n";
echo "	</div>\n";
echo "</section>\n";
?>

<section id="bracelet">
<!--
	<div class="bracelet vibration">
		<br><?php echo $user_objects[$actual_user]->name; ?>
		<span class="LED blink_green"></span>
	</div>
	<?php if(isset($id)) { ?>
	<div class="bracelet">
		<br><?php echo $user_objects[$id]->name; ?>
		<span class="LED"></span>
	</div>
	<?php } ?>
-->

<?php
//$GLOBALS['log']->printEvents();
$GLOBALS['log']->printErrors();
?>

</section>

<?php
foreach ($content['js'] as $js_path) {
	echo "<script type=\"text/javascript\" src=\"".$js_path."\"></script>\n";
}

if($content['script'] != "")
{
	echo "<script type=\"text/javascript\">\n";
	echo $content['script']."\n";
	echo "</script>\n";
}

echo "</body>\n";
echo "</html>\n";