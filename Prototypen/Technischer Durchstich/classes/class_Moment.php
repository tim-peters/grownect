<?php

class Moment {
	public $id;
	public $created_by;
	public $created_with;
	public $type;
	public $path;
	public $content;

	public static function fromRowArray($data) {
		$instance = new self();
		if($instance->createFromDatabaseRowArray($data))
			return $instance;
		else
			return false;
	}

	protected function createFromDatabaseRowArray($data) {
		$this->id = $data['id'];
		$this->created_by = $data['created_by'];
		$this->created_with = $data['created_with'];
		$this->type = $data['type'];
		$this->path = $data['path'];
		$this->content = $data['content'];
		return true;
	}

	public function show() {
		$content = "";

		switch($this->type) {
			case 1: // image
				$content .= "<img src=\"".$this->path."\">\n";
			break;

			case 2: // sound file
 			break;

			case 3: // video
			break;

			default: // text
				$content .= $this->content."\n";
		}
		$content .= "Schöner Moment wird angezeigt\n";
		return $content;
	}

	public function setUse($user) {
		global $db;

		if($db->query("
			INSERT INTO
				moments_use(moment, user)
			VALUES
				('".$this->id."', '".$user."')
		"))
		{
			return true;
		}
		else
		{
			$GLOBALS['log']->error("Failed to update database",__FILE__,__line__,$db->error);
			return false;
		}
	}
}

?>