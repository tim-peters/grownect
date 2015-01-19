<?php
$actual_user = 2;
$id = 1;

include("./db_connect.inc");
if($moment_db_outcome = $db->query("
	SELECT
		m.id,
		m.created_by,
		m.created_with,
		m.type,
		m.path,
		m.content,
		u.used
	FROM
		moments as m
	LEFT JOIN
		moments_use as u
	ON
		u.moment = m.id && u.user = ".$actual_user."
	WHERE ". // u.used > minZeitpunkt AND (
"		(m.created_by = ".$actual_user." AND m.created_with = ".$id.")
		OR
		(m.created_with = ".$actual_user." AND m.created_by = ".$id.")
	ORDER BY
		 m.rating
"))
{
	while($row = $moment_db_outcome->fetch_object())
	{
		var_dump($row);
	}
}
else
	die($db->error);

?>