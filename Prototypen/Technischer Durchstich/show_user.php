<?php
//var_dump($_SERVER);
if($_SERVER['HTTP_HOST'] == "localhost" || $_SERVER['HTTP_HOST'] == "127.0.0.1")
	die("Diese Seite muss &uuml;ber die Netzwerk-IP (nicht localhost) ge&ouml;ffnet werden um zu funktionieren.");

if(isset($_GET['id']))
{
	require_once("./classes/class_QRcode.php");
	$adress = explode("show_user.php", $_SERVER['SCRIPT_NAME'])[0];
	QRcode::png("http://".$_SERVER['HTTP_HOST'].$adress."bracelet.php?id=".$_GET['id']);
}
else
{
	require_once("./db_connect.inc");

	if($user_db_outcome = $db->query("SELECT id, name, color FROM users"))
	{
		echo "<style>ul {list-style:none;margin:0;padding:0;text-align:center;} li {display:inline-block;margin:10px;padding:5px;}</style>\n";
		echo "<ul>\n";
	    while($row = $user_db_outcome->fetch_object())
	    {
	    	echo "<li style=\"border:1px solid ".$row->color."\"><strong>".$row->name."</strong> (".$row->id.")<br><img src=\"?id=".$row->id."\"></li>\n";
	    }
	    if($db_outcome = $db->query("SHOW TABLE STATUS LIKE 'users'"))
	    {	
	    	$row = $db_outcome->fetch_object();
	    	echo "<li style=\"border:1px solid #ccc\"><strong>New User</strong> (z.B. ".$row->Auto_increment.")<br><img src=\"?id=".$row->Auto_increment."\"></li>\n";
	    }
	    echo "</ul>\n";
	}
}
?>