<?php
error_reporting(E_ALL ^ E_NOTICE);

// Importing classes
require_once("./classes/class_Conflict.php");
require_once("./classes/class_User.php");

// Importing dependencies
include_once("./db_connect.inc");
include_once("./display_functions.inc");

// Instanciate User Classes
$user_objects = array(); // create an empty array named $user_objects
if($user_db_content = $GLOBALS['db']->query("SELECT id FROM users")) // get all User with their id from database
while($row = $user_db_content->fetch_object()) { // for each user...
	$user_objects[] = new User($row->id); // create an object from class User and store it in the $user_objects array
}
$user_db_content->close();

// Declaring GET-Variables as regular variables
$state = $_GET['state'];
$id = $_GET['id'];

$actual_user = 2; // FIXME: Should be defined in an other way (e.g. cookie);


?>
<!DOCTYPE html ng-app>
<html>
<head>
	<title></title>
<link rel="stylesheet" href="./style/all.css" type="text/css" />
<link rel="stylesheet" href="./style/main.css" type="text/css" />

</head>
<body>
<section id="mirror">
	<div class="mirror">
<?php
	//echo showUserbar($user_objects, $actual_user);
	switch($state) {
		case "feelings":
		break;
		case "good_moment":
		break;
		case "last_favor":
		break;
		case "lueckentext":
		break;
		case "moments":
		break;
		case "scream":
		break;
		case "sign_up":
		break;
		case "start":
			echo showUserbar($user_objects, $actual_user);
		break;
		default:
			echo "<img src='./img/mirror_states/welcome.jpg'>";
	}
?>
	</div>
</section>
<section id="bracelet">
	<div class="bracelet vibration">
		<br>User 1
		<span class="LED blink_green"></span>
	</div>
	<div class="bracelet">
		<br>User 2
		<span class="LED"></span>
	</div>
</section>

<script type="text/javascript" src="./js/core/jquery.js"></script>
<script type="text/javascript">
$("nav ul > li").click(function() {
	$(this).toggleClass("active");
	$("nav .active").not(this).removeClass("active");
});
</script>
</body>
</html>