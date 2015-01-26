<?php
require_once("./classes/class_Conflict.php");
require_once("./classes/class_Pusher.php");
include_once("./db_connect.inc");

class User 
{
	public $id;
	public $tech_id;
	public $name;
	public $picture;
	public $color;
	public $description;
	public $initialized;
	public $conflicts_active = array();
	public $conflicts_passive = array();
	public $active_reminders = array();

	public function __construct ()
	{
		//$this->id = $_id;
	}

	public static function fromDb($_id) {
		$instance = new self();
		if($instance->createFromDatabase($_id))
			return $instance;
		else
			return false;
	}

	public static function fromNew($tech_id, $name, $picture, $description = "Loret Ipsum Dolor", $color = null) {
		$instance = new self();
		if($instance->createFromPostData($tech_id, $name, $picture, $description, $color))
			return $instance;
		else
			return false;
	}

	protected function createFromDatabase($_id) {
		global $db;
		$this->id = $_id;

		if($user_db_outcome = $db->query("
			SELECT
				users.tech_id,
				users.name,
				users.picture,
				users.color,
				users.description,
				users.initialized,
				conflicts.id AS conflict_id,
				conflicts.solved
			FROM
				users
			LEFT JOIN
				conflicts
			ON
				conflicts.solved = '0000-00-00 00:00:00.000000'
				AND
				(
					conflicts.created_by = users.id 
					OR
					conflicts.created_with = users.id
				)
			WHERE 
				users.id = ".$this->id ))
		{
		    $row = $user_db_outcome->fetch_object();

			$this->tech_id = $row->tech_id;
			$this->name = $row->name;
			$this->picture = $row->picture;
			$this->color = $row->color;
			$this->description = $row->description;
			$this->initialized = $row->initialized;

		
			do {
				if(isset($row->conflict_id)) 
				{
					$conflict = Conflict::fromDb($row->conflict_id);

					if(isset($conflict->created_by) && $conflict->created_by == $this->id)
						$this->conflicts_active[$conflict->id] = $conflict;
					elseif(isset($conflict->created_with) && $conflict->created_with == $this->id)
						$this->conflicts_passive[$conflict->id] = $conflict;
					else
						$GLOBALS['log']->error("A conflict could not be assigned",__FILE__,__line__,NULL,true);
				
				}
			} while ($row = $user_db_outcome->fetch_object());

			$user_db_outcome->close();
			return true;
		}
		else
		{
			$GLOBALS['log']->error("An user could not be found in database (id: ".$this->id.")",__FILE__,__line__,NULL,true);
			return false;
		}
	}

	protected function createFromPostData($tech_id, $name, $picture, $description, $color) {
		global $db;

		// check if a user with this tech_id (braclet) is already existing
		if($user_db_outcome = $db->query("SELECT id, tech_id, initialized FROM users WHERE tech_id = ".$tech_id))
		{
			while($row = $user_db_outcome->fetch_object()) {
				if($row->initialized == null || $row->initialized == false) // check if this user has already been initialized
				{
					// Add transfered information to user's databse entry and set his status to initialized
					if($db->query("
						INSERT INTO 
							users(name, picture, description, color, initialized) 
						VALUES 
							('".$name."', '".$picture."', '".$description."', '".$color."', '1')
					"))
					{
						$this->id = $row->id;
						$this->tech_id = $tech_id;
						$this->name = $name;
						$this->picture = $picture;
						$this->color = $color;
						$this->description = $description;
						$this->initialized = true;
						
						$user_db_outcome->close;
						return true;
					}
					else
					{
						$GLOBALS['log']->error("A database operation could not be completed",__FILE__,__line__,$db->error,true);
					}
				}
				else
				{
					$GLOBALS['log']->error("There is already an user connected to this bracelet (tech_id: ".$row->tech_id.")",__FILE__,__line__);
					return false;
				}
			}
		}
		else // create new user
		{
			if($db->query("
				INSERT INTO 
					users(tech_id, name, picture, description, color, initialized) 
				VALUES 
				  	('".$tech_id."', '".$name."', '".$picture."', '".$description."', '".$color."', '1')
		  	"))
			{
				$this->id = $db->insert_id;
				$this->tech_id = $tech_id;
				$this->name = $name;
				$this->picture = $picture;
				$this->color = $color;
				$this->description = $description;
				$this->initialized = true;

				return true;

			}
			else
			{
				$GLOBALS['log']->error("A database operation could not be completed",__FILE__,__line__,$db->error,true);
			}
		}
	}

	/**
	 * Commits all changes made to object to databse
	 * @return boolean 	sucessfull?
	 */
	protected function updateDatabase() {
		global $db;

		if($db->query("
			UPDATE
				users
			SET
				tech_id = '".$this->tech_id."',
				name = '".$this->name."',
				description = '".$this->description."',
				color = '".$this->color."',
				picture = '".$this->picture."',
				last_modified = '".$this->last_modified."',
				initialized = '".$this->initialized."'
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

	public function setPicture($_picture, $updateDatabase = true) {
		$this->picture = $_picture;

		if($updateDatabase)
			if($this->updateDatabase()) return true;
		else
			return true;
	}

	public function sendToBracelet($event) {
		$app_id = '103648';
		$app_key = '80c930949c53e186da3a';
		$app_secret = '777b9e075eb9c337ea1e';

		$pusher = new Pusher($app_key, $app_secret, $app_id);

		$data['name'] = $event;
		$data['id'] = $this->tech_id;
		$pusher->trigger('grownect', 'events', $data);
	}
}

?>