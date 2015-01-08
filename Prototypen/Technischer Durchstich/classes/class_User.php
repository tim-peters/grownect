<?php
include_once("./db_connect.inc");
include_once("./classes/class_conflict.php");

class User 
{
	public $id;
	public $tech_id;
	public $name;
	public $picture;
	public $description;
	public $initialized;
	public $conflicts_active = array();
	public $conflicts_passive = array();
	public $active_reminders = array();

	public function __construct($_id)
	{
		$this->id = $_id;			
		if($user_db_outcome = $GLOBALS['db']->query("
			SELECT
				users.tech_id,
				users.name,
				users.picture,
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
			$this->description = $row->description;
			$this->initialized = $row->initialized;

		
			do {
				if(isset($row->conflict_id)) 
				{
					$conflict = new Conflict($row->conflict_id);

					if(isset($conflict->created_by) && $conflict->created_by == $this->id)
						$this->conflicts_active[] = $conflict;
					elseif(isset($conflict->created_with) && $conflict->created_with == $this->id)
						$this->conflicts_passive[] = $conflict;
					else
						die("conflict can not be assigned");
				
				}
			} while ($row = $user_db_outcome->fetch_object());

			$user_db_outcome->close();

		}
		else
		{
			die(__FILE__.": ".$GLOBALS['db']->error);
		}
	}
}

?>