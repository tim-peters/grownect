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
	$user_objects[$row->id] = User::byDB($row->id); // create an object from class User, filled with atrributes from database and store it in the $user_objects array
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
			if($user_db_content = $GLOBALS['db']->query("SELECT id, name FROM users")) 
			while($row = $user_db_content->fetch_object()) 
				echo "			<option value='".$row->id."'>".$row->name."</option>\n"; 
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

	if($actual_user < 0) $state = "sign_up";
	switch($state) {
		case "sign_up": {
			switch($progress) {
				case 1:
					// TODO: Checking tranfered data to avoid malware injection
					if($_POST['name'] != "" || $_POST['color'] != "") {
						
						$tech_id = md5(rand(0,99999)); // FIXME: Replace by real tech_id (from bracelet)
						$picture = "./img/user.png"; // FIXME: replace by real image url

						$new_user_object = User::byPOST($tech_id, $_POST['name'], $picture, $_POST['description'], $_POST['color']);
						$id = $new_user_object->id;
						$user_objects[$id] = $new_user_object;
						echo "<h2>User erfolgreich angelegt!</h2>\n";
						echo "<a href='?state=start&change_user=".$id."'>weiter</a>\n";
					}
				break;

				default:
				echo "<form method='post' action='?state=".$state."&progress=1'>\n";
				echo "	<p>\n";
				echo "		<input type='text' name='name' placeholder='Your Name' required>\n";
				echo "	</p>\n";
				echo "	<p>\n";
				echo "		<textarea name='description' placeholder='Tell us something about ya!'></textarea>\n";
				echo "	</p>\n";
				echo "	<p>\n";
				echo "		<input type='color' name='color' value='".sprintf('#%06x',rand(0,16777215))."' required>\n";
				echo "	</p>\n";
				echo "	<input type='submit' value='weiter'>\n";
				echo "</form>\n";
			}
		break; }
		case "start":
			echo showUserbar($user_objects, $actual_user);
		break;
		case "add_moment":
			echo showUserbar($user_objects, $actual_user, $id);
			echo "<h2>Creating a good moment with ".$user_objects[$id]->name."</h2>";
		break;
		case "add_conflict":
			
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
	<?php if(isset($id)) { ?>
	<div class="bracelet">
		<br><?php echo $user_objects[$id]->name; ?>
		<span class="LED"></span>
	</div>
	<?php } ?>
</section>

<script type="text/javascript" src="./js/core/jquery.js"></script>
<script type="text/javascript">
$("nav ul > li").click(function() {
	$(this).toggleClass("open");
	$("nav .open").not(this).removeClass("open");
});
</script>
</body>
</html>