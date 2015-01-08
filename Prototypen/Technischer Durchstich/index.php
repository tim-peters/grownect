<?php
error_reporting(E_ALL ^ E_NOTICE);
include_once("./db_connect.inc");

// Declaring GET-Variables as Normals
$state = $_GET['state'];
$id = $_GET['id'];

// Importing classes
include_once("./classes/class_conflict.php");
include_once("./classes/class_user.php");

// Instance User Classes
/*$user_objects = array();
foreach($users as $user) {
	$user_objects[] = new user;
}*/

?>
<!DOCTYPE html>
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
	switch($state) {
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
<?php
	$user = new User(0);
?>
</body>
</html>