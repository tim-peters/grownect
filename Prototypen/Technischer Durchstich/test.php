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
function createHash() { return md5(time().rand(0,999).$user_objects[$actual_user]->tech_id); }

?>
<!doctype html>
<html>
<head>
	<title></title>
	<script type="text/javascript" src="./js/core/pusher.min.js"> </script>
	<script type="text/javascript" src="./js/core/jquery.js"> </script>
	<script type="text/javascript" src="./js/custom/textareaVoiceRecognition.js"></script>
	<script type="text/javascript">
	var techID = "<?php echo $user_objects[$actual_user]->tech_id; ?>";
	</script>
</head>
<body>
<p>	<textarea id="hash<?php echo createHash(); ?>"></textarea> </p>
<p>	<textarea id="hash<?php echo createHash(); ?>"></textarea> </p>
<p>	<textarea id="hash<?php echo createHash(); ?>"></textarea> </p>
</body>
</html>