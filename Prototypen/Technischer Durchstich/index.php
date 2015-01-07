<?php
// Declaring GET-Variables as Normals
$state = $_GET['state'];
$id = $_GET['id'];

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
	<div class="mirror"></div>
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
</body>
</html>