<?php

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


	public function __construct($_id) {

		$this->id = $_id;			
		if($conflict_db_outcome = $GLOBALS['db']->query("
			SELECT
				id,
				created,
				solved,
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
			$this->solved = $row->solved;
			$this->created_by = $row->created_by;
			$this->created_with = $row->created_with;
			$this->moment_used = $row->moment_used;
			$this->progress = $row->progress;
			$this->weight = $row->weight;
			$this->description = $row->description;
			$this->improvements = $row->improvements;
			$this->time_costs = $row->time_costs;
			$this->explanation = $row->explanation;
		}
		else
		{
			die(__FILE__.": ".$GLOBALS['db']->error);
		}
	}
}

?>