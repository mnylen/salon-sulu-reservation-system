<?php
require_once 'lib/db/Database.php';
require_once 'lib/db/QueryIterator.php';
require_once 'lib/Calendar.php';

class Employee {
	public $id;
	public $first_name;
	public $last_name;
	public $active = true;
	
	/**
	 * Saves the employee. If the Employee instance isn't saved previously,
	 * will insert a new record to the database. Otherwise the previous database
	 * record will be updated.
	 */
	public function save() {
	    if ($this->id === null) {
	        $stmt = Database::getPDOObject()->prepare(
	            "INSERT INTO employees (first_name, last_name, active) ".
	            "  VALUES (:fname, :lname, :active) RETURNING id");
	       
	        $stmt->bindValue(':fname', $this->first_name);
	        $stmt->bindValue(':lname', $this->last_name);
	        $stmt->bindValue(':active', ($this->active ? 'TRUE' : 'FALSE'));
	        $stmt->execute();
	        
	        $this->id = $stmt->fetchColumn();
	    } else {
	        $stmt = Database::getPDOObject()->prepare(
	            "UPDATE employees SET ".
	            "  first_name = :fname, ".
	            "  last_name  = :lname, ".
	            "  active     = :active ".
	            "WHERE id = :id");
	            
	        $stmt->bindValue(':fname', $this->first_name);
	        $stmt->bindValue(':lname', $this->last_name);
	        $stmt->bindValue(':active', ($this->active ? 'TRUE' : 'FALSE'));
	        $stmt->bindValue(':id', $this->id);
	        $stmt->execute();
	    }
	}
	
	/**
	 * Checks whether the current employee is available for a duration of $duration
	 * minutes starting from $time.
	 *
	 * @param	int	$time
	 * @param	int	$duration
	 */
	public function isAvailable($time, $duration) {
		require 'config/settings.php';
		
		// First, validate that $startTime is the staring time of any valid time period
		if ($time % $SETTINGS['time_period_duration'] !== 0)
			return false;
		
		// It should not be in the past
		if ($time < time())
			return false;
			
		$endTime = $time + $duration * Calendar::SecondsInMinute;
	
		// Now check that the employee is working between $time and $endTime
		$workShifts = WorkShift::findByEmployee($this, $time, $endTime);
		$working  = false;
	
		while ( ($shift = $workShifts->next()) !== null ) {
			if ($time >= $shift->start_time && $endTime <= $shift->end_time) {
				$working = true;
				break;
			}
		}
	
		if (!($working))
			return false;
			
		// Next we need to see whether there are any reservations between $startTime and $endTime
		$reservations = Reservation::findByEmployee($this, $time, $endTime);
		$available = true;
	
		while ( ($reservation = $reservations->next()) !== null) {
			if ($time >= $reservation->start_time && $time < $reservation->end_time) {
				$available = false;
				break;
			}
		}
	
		return $available;
	}
	
	/**
	 * Finds employee by ID.
	 *
	 * @param	int	$id
	 */
	public static function find($id) {
		$stmt = Database::getPDOObject()->prepare("SELECT * FROM employees WHERE id = :id");
		$stmt->bindValue(':id', $id);
		$stmt->execute();
		
		$employee = new Employee();
		$stmt->setFetchMode(PDO::FETCH_INTO, $employee);
		$stmt->fetch(PDO::FETCH_INTO);
		
		if ($employee->id === null)
			return null;
			
		return $employee;
	}
	
	/**
	 * Finds all employees.
	 *
	 * @param	bool	$active	return only active employees?
	 * @return	QueryIterator
	 */
	public static function findAll($active = true) {
		$sql = "SELECT * FROM employees ";
		
		if ($active)
		    $sql .= " WHERE active = TRUE ";
		
        $sql .= "ORDER by active DESC, first_name, last_name";
        
		$stmt = Database::getPDOObject()->prepare($sql);
		return new QueryIterator('Employee', $stmt);
	}
}
?>