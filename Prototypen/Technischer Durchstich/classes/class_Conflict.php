<?php
include_once("./db_connect.inc");

class Conflict {
	public $id;
	public $created;
	public $solved;
	public $created_by;
	public $created_with;
	public $moment_used;
	public $progress;
	public $weight;
	public $description;
	public $improvements;
	public $time_costs;
	public $explanation;

	/**
	 * static function to create a completly new conflict object
	 * @param  int 	$_created_by 	id of the creating user
	 * @return Conflict             the object itself
	 */
	public static function fromNew($_created_by) {
		$instance = new self();
		if($instance->createNewConflict($_created_by))
			return $instance;
		else
			return false;
	}

	/**
	 * static function to create a Conflict object from database
	 * @param  int $_id 	the conflict's id
	 * @return Conflict     the object itself
	 */
	public static function fromDb($_id) {
		$instance = new self();
		if($instance->createFromDatabase($_id))
			return $instance;
		else
			return false;
	}

	/**
	 * created a new database entry for the new Conflict object and writes id, created_by and time of creation in attributes
	 * @param  int 			$_created_by id of the creating user
	 * @return boolean      successful created?
	 */
	protected function createNewConflict($_created_by) {
		global $db;

		if($db->query("
			INSERT INTO 
				conflicts(created_by, created_with, progress) 
			VALUES 
			  	('".$_created_by."', -1, 0)
	  	"))
		{
			$this->id = $db->insert_id;
			$this->created_by = $_created_by;
			$this->created = time();

			return true;
		}
		else
		{
			$GLOBALS['log']->error("Failed to update database",__FILE__,__line__,$db->error);
			return false;
		}
	}

	/**
	 * Fills the object's attributes depending on database
	 * @param  int 		$_id 	the Conflic's id
	 * @return boolean			successful?
	 */
	protected function createFromDatabase($_id) {
		global $db;

		$this->id = $_id;			
		if($conflict_db_outcome = $db->query("
			SELECT
				id,
				UNIX_TIMESTAMP(created),
				UNIX_TIMESTAMP(solved),
				created_by,
				created_with,
				moment_used,
				progress,
				weight,
				description,
				improvements,
				time_costs,
				explanation
			FROM
				conflicts
			WHERE 
				id = ".$this->id ))
		{
		    $row = $conflict_db_outcome->fetch_object();

			$this->id = $row->id;
			$this->created = $row->created;
			$this->solved = ($row->solved < 0) ? 0 : $row->solved;
			$this->created_by = $row->created_by;
			$this->created_with = $row->created_with;
			$this->moment_used = $row->moment_used;
			$this->progress = $row->progress;
			$this->weight = $row->weight;
			$this->description = $row->description;
			$this->improvements = $row->improvements;
			$this->time_costs = $row->time_costs;
			$this->explanation = $row->explanation;

			return true;
		}
		else
		{
			$GLOBALS['log']->error("A database operation could not be completed",__FILE__,__line__,$db->error,true);
			return false;
		}
	}

	/**
	 * Commits all changes made to object to databse
	 * @return boolean 	sucessfull?
	 */
	protected function updateDatabase() {
		global $db;

		$translated_solved = ($this->solved == 0) ? 0 : date("Y-m-d H:i:s", $this->solved);
		if($db->query("
			UPDATE
				conflicts
			SET
				created = '".date("Y-m-d H:i:s", $this->created)."',
				solved = '".$translated_solved."',
				created_by = '".$this->created_by."',
				created_with  = '".$this->created_with."',
				moment_used  = '".$this->moment_used."',
				progress  = '".$this->progress."',
				weight  = '".$this->weight."',
				description  = '".$this->description."',
				improvements  = '".$this->improvements."',
				time_costs  = '".$this->time_costs."',
				explanation  = '".$this->explanation."'
			WHERE
				id = ".$this->id 
		))
		{
			return true;
		}
		else
		{
			$GLOBALS['log']->error("Failed to update database",__FILE__,__line__,$db->error);
			return false;
		}
	}

	/**
	 * Sets the created_with attribute
	 * @param int 	$_created_with 	id of the user a conflict is getting create with
	 */
	public function setCreated_with($_created_with, $updateDatabase = true) {
		$this->progress = 1;
		$this->created_with = $_created_with;

		if($updateDatabase)
			$this->updateDatabase();
	}

	/**
	 * Sets the moment_used attribute to
	 * @param int 	$id 	id of the used moment
	 */
	public function setMoment_used($id, $updateDatabase = true) {
		$this->moment_used = $id;

		if($updateDatabase)
			$this->updateDatabase();
	}

	/**
	* Sets the progress attribute to
	* @param int 	$_progress 	Number of progress
	*/
	public function setProgress($_progress, $updateDatabase = true) {
		$this->progress = $_progress;


		if($updateDatabase)
			$this->updateDatabase();
	}
}

?>