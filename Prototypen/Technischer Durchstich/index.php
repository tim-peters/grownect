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
if($_GET['change_user']) // if a new user is getting set
{
	setcookie("user",$_GET['change_user']); // set a cookie to "remember" this choice
	$actual_user = $_GET['change_user'];
}
elseif(isset($_COOKIE['user'])) // if a user has been set before
	$actual_user = $_COOKIE['user'];
else // fallback
	$actual_user = -1;


?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
<link rel="stylesheet" href="./style/all.css" type="text/css" />
<link rel="stylesheet" href="./style/main.css" type="text/css" />

</head>
<body>
<div class="free" style="width:500px;margin:0 auto;">
	<form action="" method="GET">
		Act/View as 
		<select name="change_user" size="1">
			<?php
			if($user_db_content = $db->query("SELECT id, name FROM users"))
			{
				echo "			<option value='-1'> - </option>\n"; 
				while($row = $user_db_content->fetch_object()) 
					echo "			<option value='".$row->id."'>".$row->name."</option>\n"; 
			}
			?>
		</select>
		<input type="submit" />
	</form>
</div>
<section id="mirror">
	<div class="mirror">
<?php
	//echo showUserbar($user_objects, $actual_user);
	echo "View of ".$user_objects[$actual_user]->name;

	if($actual_user < 0) $state = "sign_up"; // if a user has not been initialized already: show him the sign up screen
	switch($state) {


		case "sign_up": {
			include("./states/sign_up.inc");
		break; }


		case "homescreen":
		case "start":
			include("./states/homescreen.inc");
		break;


		case "add_moment":
			include("./states/add_moment.inc");
		break;


		case "add_conflict": {
			include("./states/add_conflict.inc");
		break; }


		default:
			echo "<a href='?state=start'><img src='./img/mirror_states/welcome.jpg'></a>";
	}
?>
	</div>
</section>
<section id="bracelet">
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
</section>

<?php
$GLOBALS['log']->printErrors();
?>

<script type="text/javascript" src="./js/core/jquery.js"></script>
<script type="text/javascript">
$("nav ul > li").click(function() {
	$(this).toggleClass("open");
	$("nav .open").not(this).removeClass("open");
});
</script>
</body>
</html>