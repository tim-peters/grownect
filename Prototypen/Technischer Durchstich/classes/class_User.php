<?php
require_once("./classes/class_Conflict.php");
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
		$this->id = $_id;

		if($user_db_outcome = $GLOBALS['db']->query("
			SELECT
				users.tech_id,
				users.name,
				users.picture,
				users.color,
				users.description,
				users.initialized,
				conflicts.id AS conflict_id
			FROM
				users
			LEFT JOIN
				conflicts
			ON
				conflicts.created_by = users.id OR conflicts.created_with = users.id
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
						die(__FILE__.", Zeile ".__LINE__.": A conflict could not be assigned");
				
				}
			} while ($row = $user_db_outcome->fetch_object());

			$user_db_outcome->close();
			return true;
		}
		else
		{
			printf(__FILE__.", Zeile ".__LINE__.": An user could not be found in database (id: ".$this->id.")\n");
			return false;
		}
	}

	protected function createFromPostData($tech_id, $name, $picture, $description, $color) {
		
		// check if a user with this tech_id (braclet) is already existing
		if($user_db_outcome = $GLOBALS['db']->query("SELECT id, tech_id, initialized FROM users WHERE tech_id = ".$tech_id))
		{
			while($row = $user_db_outcome->fetch_object()) {
				if($row->initialized == null || $row->initialized == false) // check if this user has already been initialized
				{
					// Add transfered information to user's databse entry and set his status to initialized
					if($GLOBALS['db']->query("
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
						die(__FILE__.", Zeile ".__LINE__.": Error: A database operation could not be completed");
					}
				}
				else
				{
					printf(__FILE__.", Zeile ".__LINE__.": There is already an user connected to this bracelet (tech_id: ".$row->tech_id.").\n");
					return false;
				}
			}
		}
		else // create new user
		{
			if($GLOBALS['db']->query("
				INSERT INTO 
					users(tech_id, name, picture, description, color, initialized) 
				VALUES 
				  	('".$tech_id."', '".$name."', '".$picture."', '".$description."', '".$color."', '1')
		  	"))
			{
				$this->id = $GLOBALS['db']->insert_id;
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
				die(__FILE__.", Zeile ".__LINE__.": Error: A database operation could not be completed\n ".$GLOBALS['db']->error);
			}
		}
	}
}

?>