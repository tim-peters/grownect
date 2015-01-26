<?php
error_reporting(E_ALL ^ E_NOTICE);
require_once("./classes/class_Log.php");

include_once("./classes/class_User.php");

$actual_user = $_GET['id'];
if(!isset($_GET['id']) || !$user_objects[$actual_user] = User::FromDb($actual_user))
	die("No User specified");

?>
<!doctype html>
<html>
<head>
	<title></title>
	<script type="text/javascript" src="//js.pusher.com/2.2/pusher.min.js"> </script>
	<script type="text/javascript" src="./js/core/jquery.js"> </script>
	<script type="text/javascript">
	var techID = "<?php echo $user_objects[$actual_user]->tech_id; ?>";
	var pusher = new Pusher('80c930949c53e186da3a');
	var channel = pusher.subscribe('grownect');

	channel.bind('answers', function(data) {
		console.log("event detected: "+data);
		console.log("id: "+data.id+", name: "+data.name);
		if(data.id == techID && data.name == "startScream")
		{
			// Mikro Lautstärke ermitteln

		} 
		else if(data.id == techID && data.name == "endScream")
		{
			var pulse = data.value;
			console.log("pulse = "+pulse);

		}
		else
		{
			console.log("Received event could not be identified");
			console.dir(data);
		}
	});

	function sendLoudness(volume) {
		$.ajax({
			type: "POST",
			url: "./api/api_braceletAnswer.php",
			data: dataObject,
			success: handlePulseAnswer
		});
	}

	</script>
</head>
<body>
	<h3 class="message">Detecting pulse frequency</h3>

</body>
</html>