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

	public static function fromNew($_created_by, $_created_with, $_type, $_rating, $_path = null, $_content = null) {
		$instance = new self();
		if($instance->createNewMoment($_created_by, $_created_with, $_type, $_rating, $_path, $_content))
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

	protected function createNewMoment($_created_by, $_created_with, $_type, $_rating, $_path, $_content) {
		global $db;

		//echo $_created_by.", ".$_created_with.", ".$_type.", ".$_rating.", ".$_path.", ".$_content."\n";
		$query = "INSERT INTO moments(created_by, created_with, type, rating";
		if($_type==0) $query .= ", content";
		else 		$query .= ", path";
		$query .= ") VALUES ('".$_created_by."', '".$_created_with."', '".$_type."', '".$_rating."'";
		if($_type==0) $query .= ", '".$_content."'";
		else 		$query .= ",  '".$_path."'";
		$query		.= ")";

		if($db->query($query))
		{
			$GLOBALS['log']->event("Moment created",__FILE__,__line__);
			return true;
		}
		else
		{
			$GLOBALS['log']->error("Failed to update database",__FILE__,__line__,$db->error);
			return false;
		}
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