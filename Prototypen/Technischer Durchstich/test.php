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
	<script type="text/javascript" src="./js/custom/screamCheck.js"></script>
	<script type="text/javascript">
	var techID = "<?php echo $user_objects[$actual_user]->tech_id; ?>";
	</script>
</head>
<body>
	<h3 class="message">Detecting pulse frequency</h3>

</body>
</html>