<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>

<?php
error_reporting(E_ALL ^ E_NOTICE);

$actual_user = 2;
$id = 1;

$daysSinceUse = .5;
$maxUseTimestamp = time() -($daysSinceUse*24*60*60);

include("./db_connect.inc");


function filterMoments($workingCopy, $maxUseTimestamp) {
	$counter = array();
	$toBeUnset = array();
	foreach($workingCopy as $row) 
	{
		if($row['UNIX_TIMESTAMP(u.used)'] != null && $row['UNIX_TIMESTAMP(u.used)'] >= $maxUseTimestamp)
			$toBeUnset[$row['id']] = true;
		elseif($row['UNIX_TIMESTAMP(u.used)'])
			$counter[$row['id']]++;
	}
	foreach($workingCopy as $key => $row) 
	{
		$id = $row['id'];
		if($counter[$id] >= 3 || $toBeUnset[$id]) {
			unset($workingCopy[$key]);
		}
	}

	return $workingCopy;
}


if($moment_db_outcome = $db->query("
	SELECT
		m.id,
		m.created_by,
		m.created_with,
		m.type,
		m.path,
		m.content,
		m.rating,
		UNIX_TIMESTAMP(u.used)
	FROM
		moments as m
	LEFT JOIN
		moments_use as u
	ON
		u.moment = m.id
		AND
		u.user = ".$actual_user."
	WHERE 
		(
			m.created_by = ".$actual_user." 
			AND 
			m.created_with = ".$id."
		)
		OR
		(
			m.created_with = ".$actual_user." 
			AND 
			m.created_by = ".$id."
		)
	ORDER BY
		m.rating DESC,
		u.used ASC
"))
{
	$original = array();
	echo "<h3>Orginal Outcome</h3>";
	echo "<table border='1'>\n";
	$row = mysqli_fetch_assoc($moment_db_outcome);
	echo "<tr>\n";
	foreach ($row as $key => $value) {
		echo "	<td><strong>".$key."</strong></td>\n";
	}
	echo "<tr>\n\n";
	
	do
	{
		$original[] = $row;
		echo "<tr>\n";
		foreach ($row as $value) {
			echo "	<td>".$value."</td>\n";
		}
		echo "<tr>\n\n";
	} while($row = mysqli_fetch_assoc($moment_db_outcome));
	echo "</table>\n";

	$filtered = filterMoments($original, $maxUseTimestamp);

	echo "<h3>Orginal Outcome</h3>";
	echo "<table border='1'>\n";
	echo "<tr>\n";
	foreach ($filtered[0] as $key => $value) {
		echo "	<td><strong>".$key."</strong></td>\n";
	}
	echo "<tr>\n\n";

	foreach ($filtered as $row) {
	
		echo "<tr>\n";
		foreach ($row as $value) {
			echo "	<td>".$value."</td>\n";
		}
		echo "<tr>\n\n";
	}
	echo "</table>\n";

}
else
{
	die($db->error);
}

?>


</body>
</html>