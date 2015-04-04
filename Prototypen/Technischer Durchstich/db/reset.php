<?php
$servername = "localhost";
$username = "root";
$password = "";
$db = "s3";

// SQL
$sql_create = [
  "CREATE TABLE IF NOT EXISTS `conflicts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `solved` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL,
  `created_with` int(11) NOT NULL,
  `moment_used` int(11) NOT NULL,
  `progress` int(11) NOT NULL,
  `weight` int(11) NOT NULL,
  `description` text NOT NULL,
  `improvements` text NOT NULL,
  `time_costs` int(11) NOT NULL,
  `explanation` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_2` (`id`),
  KEY `id` (`id`)
  ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22 ;",

  "CREATE TABLE IF NOT EXISTS `moments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(512) NOT NULL,
  `date` timestamp NULL DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_with` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `path` varchar(256) NOT NULL,
  `content` text NOT NULL,
  `rating` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
  ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;",

  "CREATE TABLE IF NOT EXISTS `moments_use` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `moment` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `used` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
  ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;",

  "CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tech_id` varchar(512) NOT NULL,
  `name` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `color` varchar(20) NOT NULL,
  `picture` varchar(256) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `initialized` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tech_id` (`tech_id`,`picture`),
  UNIQUE KEY `id_2` (`id`),
  KEY `id` (`id`)
  ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;"
];

$sql_insert = [
  "INSERT INTO `conflicts` (`id`, `created`, `solved`, `created_by`, `created_with`, `moment_used`, `progress`, `weight`, `description`, `improvements`, `time_costs`, `explanation`) VALUES
  (1, '2015-02-04 22:31:29', '0000-00-00 00:00:00', 1, 3, 3, 7, 38, 'is always eating my milks', 'stop it', 0, ''),
  (4, '2015-02-05 08:02:50', '2015-02-05 08:10:06', 3, 2, 4, 10, 94, 'Tim is always forgetting to turn the light off', 'tht he would care more about it.', 10, 'EHealth to get my train at harry sorry aber sofort next time'),
  (8, '2015-02-05 12:31:56', '0000-00-00 00:00:00', 1, 2, 5, 3, 0, '', '', 0, ''),
  (9, '2015-02-05 14:35:38', '0000-00-00 00:00:00', 1, 2, 5, 7, 38, 'You know the problem is I love you and you don''t know it', 'kiss a frog', 5, '');",
  
  "INSERT INTO `moments` (`id`, `title`, `date`, `created_by`, `created_with`, `type`, `path`, `content`, `rating`) VALUES
  (7, 'Eating selfmade pizza', '2014-11-23 00:00:00', 2, 3, 1, './img/moments/6ae0089b83af7f9558e1de83662682be_IMG_20141119_211335.jpg', '', 75);",
  
  "INSERT INTO `users` (`id`, `tech_id`, `name`, `description`, `color`, `picture`, `created`, `last_modified`, `initialized`) VALUES
  (2, '82f585baeae099c070aba8457633ca21', 'Tim J. Peters', 'I''m the coder', '#f59556', './img/users/df799861426ac8cfbddd5342c233cc09_grownect_tim.jpg', '2015-02-03 16:33:24', '2015-02-17 22:06:42', 1),
  (3, '3b6b1192472a51adc2e7054f16a8ceba', 'Doreen Scheller', 'Ich bin sehr ordentlich organisiert und ja es reicht.', '#acda8d', './img/users/5a5a9f70e2184243e69354720f2105b5_grownect_doreen.jpg', '2015-02-03 16:31:12', '0000-00-00 00:00:00', 1),
  (4, 'ce2e8d5238ea7f69cfd6b0db27a09cb8', 'Nadine', '', 'rgb(141, 28, 28)', './img/users/d420ff06350deab229936a5d07499e3b_grownect_nadine.jpg', '2015-02-11 15:08:45', '0000-00-00 00:00:00', 1);"
];


// Create connection
$conn = new mysqli($servername, $username, $password);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Drop database first 
if ($conn->query('DROP DATABASE IF EXISTS '.$db) === TRUE) {

	// Create database
	if ($conn->query("CREATE DATABASE IF NOT EXISTS ".$db) === TRUE) {
	    echo "Database created successfully<br>";

	    // connect to new db
	    $conn = new mysqli($servername, $username, $password, $db);
		if ($conn->connect_error) {
		    die("Connection failed: " . $conn->connect_error);
		}

		// create tables
		foreach ($sql_create as $sql_part) {
		    if($conn->query($sql_part) === TRUE)
		    {
		    	echo "successful created table<br>";
		    }
		    else
		    	die("Error creating table: " . $conn->error);
		}

		// fill tables
		foreach ($sql_insert as $sql_part) {
		    if($conn->query($sql_part) === TRUE)
		    {
		    	echo "successful filled table<br>";
		    }
		    else
		    	die("Error filling tables: " . $conn->error);
		}

	} else {
	    echo "Error creating database: " . $conn->error;
	}
} else {
    echo "Error creating database: " . $conn->error;
}
$conn->close();
echo "Database successful resettet!";
?> 
