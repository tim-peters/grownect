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

	var isScreaming = false;

	channel.bind('events', function(data) {
		console.log("event registered");
		if(data.id == techID)
		{
			console.log("event accepted: "+data.name);
			switch(data.name) {
				case "checkPulse":
					getPulse(function (pulse) {
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
					});
				break;

				case "startScream":
					isScreaming = true;
					
					function sendLoudness(volume) {
						console.log("working");
						var dataObject = {
							id: techID,
							name: 'setLoudness',
							value: volume
						};

						function restart() {
							if(isScreaming)
								setTimeout(function() { getLoudness(sendLoudness); }, 150);
						}
						
						$.ajax({
							type: "POST",
							url: "./api/api_braceletAnswer.php",
							data: dataObject,
							success: restart
						});
					}

					getLoudness(sendLoudness);
				break;

				case "endScream":
					console.log("working");
					isScreaming = false;
				break;

				default:
					console.error("Received event could not be identified");
			}
		}
	});

	/*channel.bind('answers', function(data) {
		console.log("answer registered");
		if(data.id == techID)
		{
			console.log("answer accepted: "+data.name);
			switch(data.name) {
				case "setLoudness":
				default:
					console.error("Received event could not be identified");
			}
		}
	});*/

	function handlePulseAnswer(msg) {
		console.log("Answer: "+msg);
	}

	function getPulse(callback) {
		setTimeout(function() {
			console.log("Pulse ermittelt");
			callback(Math.floor(Math.random() * (170 - 55 + 1)) + 55);
		}, 3000);
	}

	function getLoudness(callback) {
		setTimeout(function() {
			console.log("Loudness ermittelt");
			callback(Math.random());
		}, 50);
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