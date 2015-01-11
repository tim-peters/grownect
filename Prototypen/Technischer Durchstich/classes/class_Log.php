<?php

class Log {
	private $events = array();
	private $errors = array();

	public function event($msg, $file, $line) {
		$event = [
			"msg" => $msg
			];
		if(isset($file)) $event["file"] = $file;
		if(isset($line)) $event["line"] = $line;
		$this->events[] = $event;
	}

	public function error($msg, $file, $line, $second_msg, $critical = false) {
		$error = [
			"msg" => $msg,
			"file" => $file,
			"line" => $line
			];
		if(isset($second_msg)) $error["second_msg"] = $second_msg;
		if(isset($critical)) 
		{
			$error["critical"] = true;
			$this->errors[] = $error;
			$this->printErrors();
		}
		$this->errors[] = $error;
	}

	public function printErrors() {
		if(count($this->errors) > 0)
		{
			echo "<ul>\n";
			echo "<li><strong>".count($this->errors)." Error(s):</strong></li>\n";
			foreach($this->errors as $error) {
				echo "<li";
				if($error['critical']) echo " color='red'";
				echo ">\n";
				echo "<strong>".date('r')."</strong> ".$error['file'].", Line ".$error['line'].": <strong>".$error['msg']."<strong>\n";
				if(isset($error['second_msg'])) echo "<br><small>".$error['second_msg']."</small>\n";
				echo "</li>\n";
				if($error['critical']) die("</ul>\n");
			}
			echo "</ul>\n";
		}
	}

	public function printEvents() {
		if(count($this->events) > 0)
		{
			echo "<ul>\n";
			echo "<li><strong>".count($this->events)." Events(s):</strong></li>\n";
			foreach($this->events as $event) {
				echo "<li>\n";
				echo "<strong>".date('r')."</strong> ";
				if(isset($event['file'])) echo $event['file'].", ";
				if(isset($event['line'])) echo "Line ".$error['line'].": ";
				echo "<strong>".$error['msg']."<strong>\n";
				echo "</li>\n";
			}
			echo "</ul>\n";
		}
	}

}

$GLOBALS['log'] = new Log;
?>