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
$hash = md5(time().$user_objects[$actual_user]->tech_id);

$pusher = new Pusher($app_key, $app_secret, $app_id);

$data['name'] = "getText";
$data['hash'] = $hash;
$data['id'] = $user_objects[$actual_user]->tech_id;
$pusher->trigger('grownect', 'events', $data);

?>
<!doctype html>
<html>
<head>
	<title></title>
	<script type="text/javascript" src="//js.pusher.com/2.2/pusher.min.js"> </script>
	<script type="text/javascript" src="./js/core/jquery.js"> </script>
	<script type="text/javascript">
	var pusher = new Pusher('80c930949c53e186da3a');
	var channel = pusher.subscribe('grownect');
	var techID = "<?php echo $user_objects[$actual_user]->tech_id; ?>";
	channel.bind('answers', function(data) {
		console.log("event registered");
		if(data.id == techID)
		{
			console.log("event accepted: "+data.name);
			switch(data.name) {
				case "setText":
					$("#hash"+data.hash).text(data.value);
				break;
				default:
					console.error("Received event could not be identified");
			}
		}
	});
	
	</script>
</head>
<body>
	<textarea id="hash<?php echo $hash; ?>"></textarea>
</body>
</html>