<?php

function createHash() { return md5(time().rand(0,999).$user_objects[$actual_user]->tech_id); };

function secureString($str) { return mysql_real_escape_string(strip_tags(trim($str))); };

?>