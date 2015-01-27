<?php

function createHash() { return md5(time().rand(0,999).$user_objects[$actual_user]->tech_id); }

?>