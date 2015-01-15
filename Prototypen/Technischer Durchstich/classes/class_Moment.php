<?php

class Moment {
	public $id;
	public $created_by;
	public $created_with;
	public $type;
	public $path;
	public $content;

	public static function fromRow($data) {
		$instance = new self();
		if($instance->createFromDatabaseRow($data))
			return $instance;
		else
			return false;
	}

	protected function createFromDatabaseRow($data) {
		$this->id = $data->id;
		$this->created_by = $data->created_by;
		$this->created_with = $data->created_with;
		$this->type = $data->type;
		$this->path = $data->path;
		$this->content = $data->content;
		return true;
	}

	public function show() {
		switch($this->type) {
			case 0: // text
				echo $this->content."\n";
			break;

			case 1: // image
				echo "<img src=\"".$this->path."\">\n";
			break;

			case 2: // sound file
 			break;

			case 3: // video
			break;
		}
		echo "Schöner Moment wird angezeigt";

	}
}

?>