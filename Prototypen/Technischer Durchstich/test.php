<?php
error_reporting(E_ALL ^ E_NOTICE);
require_once("./classes/class_Log.php");

include_once("./classes/class_User.php");

$actual_user = $_GET['id'];
if(!isset($_GET['id']) || !$user_objects[$actual_user] = User::FromDb($actual_user))
	die("No User specified");

require_once("./classes/class_Pusher.php");
$app_id = '103648';
$app_key = '80c930949c53e186da3a';
$app_secret = '777b9e075eb9c337ea1e';

$pusher = new Pusher($app_key, $app_secret, $app_id);

$data['name'] = "checkPulse";
$data['id'] = $user_objects[$actual_user]->tech_id;
$pusher->trigger('grownect', 'events', $data);

?>
<!doctype html>
<html>
<head>
	<title></title>
	<style type="text/css">
	#loudnessIndikator {
		background:red;
		width:1px;
		height:0.1px;
		margin-left:100px;
		transform: scale(200);
	}
	</style>
	<script type="text/javascript" src="//js.pusher.com/2.2/pusher.min.js"> </script>
	<script type="text/javascript" src="./js/core/jquery.js"> </script>
	<script type="text/javascript">
	var techID = "<?php echo $user_objects[$actual_user]->tech_id; ?>";
	var pusher = new Pusher('80c930949c53e186da3a');
	var channel = pusher.subscribe('grownect');

	
	channel.bind('events', function(data) {
		console.log("event detected: "+data);
		console.log("id: "+data.id+", name: "+data.name);
		if(data.id == techID && data.name == "startScream")
		{
			$(".message").text("Scream as loud as you can into your bracelet now!")
						.after("<div id=\"loudnessIndikator\"></div>");
			console.log("recognized: startScream");

		}
		else if(data.id == techID && data.name == "endScream")
		{
			$(".message").text("Congratulations! You'll be redirected now.");
			console.log("recognized: endScream");

		}
		else
		{
			console.log("Received event could not be identified");
			console.dir(data);
		}
	});

	var lastLoudness = 0;
	channel.bind('answers', function(data) {
		console.log("answer detected: "+data);
		console.log("id: "+data.id+", name: "+data.name);
		if(data.id == techID && data.name == "setLoudness")
		{
			console.log("setLoudness to "+data.value);
			$("#loudnessIndikator").animate({width:data.value+"px"}, 200);
			
			/*
			$({ value: lastLoudness }).animate({ value: data.value }, 
			{
				step: function() {
					$("#loudnessIndikator").attr("value", this.value);
				},
				complete: function() {
					console.log("complete animation");
					lastLoudness = this.value;
				}
			});*/
		} 
		else
		{
			console.log("Received answer could not be identified");
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