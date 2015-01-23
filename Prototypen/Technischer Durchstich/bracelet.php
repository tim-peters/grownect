<?php
error_reporting(E_ALL ^ E_NOTICE);
require_once("./classes/class_Log.php");

include_once("./classes/class_User.php");

if(!isset($_GET['id']) || !$user_object = User::FromDb($_GET['id']))
	die("No User specified");

?>
<!DOCTYPE html>
<html>
<head>
	<title>Bracelet</title>
	<link rel="stylesheet" href="./style/all.css" type="text/css" />
	<script type="text/javascript" src="//js.pusher.com/2.2/pusher.min.js"> </script>
	<script type="text/javascript" src="./js/core/jquery.js"> </script>
	<script type="text/javascript">
	var techID = "<?php echo $user_object->tech_id; ?>";
	var pusher = new Pusher('80c930949c53e186da3a');
	var channel = pusher.subscribe('grownect');

	channel.bind('events', function(data) {
		console.log("event registered");
		if(data.id == techID)
		{
			console.log("event accepted: "+data.name);
			switch(data.name) {
				case "checkPulse":
					var pulse = getPulse();
					console.log("working");
					var dataObject = {
						id: techID,
						name: 'setPulse',
						value: pulse
					};
					
					$.ajax({
						type: "POST",
						url: "./api/api_braceletAnswer.php",
						data: dataObject,
						success: handlePulseAnswer
					});
				break;

				default:
					console.error("Received event could not be identified");
			}
		}
	});

	function handlePulseAnswer(msg) {
		console.log("Answer: "+msg);
	}

	function getPulse() {
		return Math.random(55,170);
	}


	/**
	 * SOURCE: http://stackoverflow.com/questions/901115/how-can-i-get-query-string-values-in-javascript
	 */
	function getParameterByName(name) {
	    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
	    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
	        results = regex.exec(location.search);
	    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
	}
	</script>
</head>
<body>
<h1><?php echo $user_object->name; ?>'s Bracelet</h1>
</body>
</html>