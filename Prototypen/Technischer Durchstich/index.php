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

	if($actual_user < 0) $state = "sign_up";
	switch($state) {


		case "sign_up": {
			switch($progress) {
				case 1:
					// TODO: Checking tranfered data to avoid malware injection
					if($_POST['name'] != "" || $_POST['color'] != "") {
						
						$tech_id = md5(rand(0,99999)); // FIXME: Replace by real tech_id (from bracelet)
						$picture = "./img/user.png"; // FIXME: replace by real image url

						$new_user_object = User::fromNew($tech_id, $_POST['name'], $picture, $_POST['description'], $_POST['color']);
						$id = $new_user_object->id;
						$user_objects[$id] = $new_user_object;
						echo "<h2>User erfolgreich angelegt!</h2>\n";
						echo "<a href='?state=start&change_user=".$id."'>weiter</a>\n";
					}
				break;

				default:
				echo "<br><br><br>\n";
				echo "<p class='big_userpic'><img src='./img/user.png'></p>\n";
				echo "<form method='post' action='?state=".$state."&progress=1'>\n";
				echo "	<p>\n";
				echo "		<input type='text' name='name' placeholder='Your Name' required>\n";
				echo "	</p>\n";
				echo "	<p>\n";
				echo "		<input type='color' name='color' value='".sprintf('#%06x',rand(0,16777215))."' required>\n";
				echo "	</p>\n";
				echo "	<p>\n";
				echo "		<textarea name='description' placeholder='Tell us something about ya!'></textarea>\n";
				echo "	</p>\n";
				echo "	<input type='submit' value='weiter'>\n";
				echo "</form>\n";
			}
		break; }


		case "start":
			$conflicts_just_opened = array();
			foreach($user_objects[$actual_user]->conflicts_active as $conflict_active)
				if($conflict_active->progress == 0) $conflicts_just_opened[] = $conflict_active->$id;
			if(count($conflicts_just_opened) > 0)
			{
				echo showUserbar($user_objects, $actual_user, null, "add_conflict");
				echo "<h2>You have ".count($conflicts_just_opened)." open conflicts you need to specify.</h2>\n";
				echo "<strong>Please choose the user, you were in trouble with at ".date('r', $user_objects[$actual_user]->conflicts_active[$conflicts_just_opened[0]]->created)."</strong>\n";
			}
			else
				echo showUserbar($user_objects, $actual_user);
		break;


		case "add_moment":
			echo showUserbar($user_objects, $actual_user, $id);
			echo "<h2>Creating a good moment with ".$user_objects[$id]->name."</h2>";
		break;


		case "add_conflict": {
			switch(progress) {
				case 1: // already opened conflict to be specified (e.g. by hit on bracelet)
				break;
				
				default: // new conflict to be set and specified
					if(isset($id))
					{
						echo showUserbar($user_objects, $actual_user, $id);
						$instance = Conflict::fromNew($actual_user);
						$instance->setCreated_with($id);
						$user_objects[$actual_user]->conflicts_active[$instance->id] = $instance;
					}
					else
						die("Error: You need to specify a person you want to open a conflict with.");

					$startway = rand(1,3);
					switch($startway) {
						case 1:
						break;

						case 2:
						break;

						case 3:
						break;
					}
			}
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