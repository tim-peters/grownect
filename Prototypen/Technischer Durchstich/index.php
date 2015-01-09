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
	$user_objects[$row->id] = new User($row->id); // create an object from class User and store it in the $user_objects array
}
$user_db_content->close();

// Declaring GET-Variables as regular variables
$state = $_GET['state'];
$id = $_GET['id'];

// Set the user which is actually viewing/acting
if($_POST['change_user']) // if a new user is getting set
{
	setcookie("user",$_POST['user']); // set a cookie to "remember" this choice
	$actual_user = $_POST['user'];
}
elseif(isset($_COOKIE['user'])) // if a user has been set before
	$actual_user = $_COOKIE['user'];
else // fallback
	$actual_user = 2;


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
	<form action="" method="POST">
		Act/View as 
		<select name="user" size="1">
			<?php
			if($user_db_content = $GLOBALS['db']->query("SELECT id, name FROM users")) 
			while($row = $user_db_content->fetch_object()) 
				echo "			<option value='".$row->id."'>".$row->name."</option>\n"; 
			?>
		</select>
		<input type="submit" name="change_user" />
	</form>
</div>
<section id="mirror">
	<div class="mirror">
<?php
	//echo showUserbar($user_objects, $actual_user);
	echo "View of ".$user_objects[$actual_user]->name;
	switch($state) {
		case "sign_up":
		break;
		case "start":
			echo showUserbar($user_objects, $actual_user);
		break;
		case "add_moment":
			echo showUserbar($user_objects, $actual_user, $id);
		break;
		case "feelings":
		break;
		case "good_moment":
		break;
		case "last_favor":
		break;
		case "lueckentext":
		break;
		case "scream":
		break;
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