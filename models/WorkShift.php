<?php
require_once 'lib/db/Database.php';
require_once 'lib/db/QueryIterator.php';
require_once 'models/Employee.php';
require_once 'models/Reservation.php';

class WorkShift {
	protected $id;
	protected $employee_id;
	protected $start_time;
	protected $end_time;
	
	public function __get($name) {
		if ($name == 'start_time' || $name == 'end_time') {
			// We must convert the PostgeSQL TIMESTAMP to Unix Epoch Time timestamp
			$value = $this->{$name};
			return strtotime($value);
		} else if ($name == 'reservationCount') {
		    return $this->getReservationCount();
		} else if ($name == 'reservations') {
		    return $this->getReservations();  
		} else {
			return $this->{$name};
		}
	}
	
	public function __set($name, $value) {
		$this->{$name} = $value;
	}
	
	private function getReservationCount() {
	    $stmt = Database::getPDOObject()->prepare(
	        "SELECT COUNT(*) FROM reservations WHERE ".
	        "  employee_id = :empid AND ".
	        "  start_time >= :shiftstart AND ".
	        "  end_time   <= :shiftend");
	        
        $stmt->bindValue(':empid', $this->employee_id);
        $stmt->bindValue(':shiftstart', $this->start_time);
        $stmt->bindValue(':shiftend', $this->end_time);
        $stmt->execute();
        
        return $stmt->fetchColumn();
	}
	
	private function getReservations() {
	    $stmt = Database::getPDOObject()->prepare(
	        "SELECT * FROM reservations WHERE ".
	        "  employee_id = :empid AND ".
	        "  start_time >= :shiftstart AND ".
	        "  end_time   <= :shiftend");
	        
        $stmt->bindValue(':empid', $this->employee_id);
        $stmt->bindValue(':shiftstart', $this->start_time);
        $stmt->bindValue(':shiftend', $this->end_time);
        
        return new QueryIterator('Reservation', $stmt);
	}
	
	public function save() {
	    if ($this->id != null)
	        return;
	        
	    $stmt = Database::getPDOObject()->prepare(
	        "INSERT INTO work_shifts (employee_id, start_time, end_time) ".
	        "  VALUES (:empid, :stime, :etime) RETURNING id");
	        
	    $stmt->bindValue(':stime', date('Y-m-d H:i', $this->start_time));
	    $stmt->bindValue(':etime', date('Y-m-d H:i', $this->end_time));
	    $stmt->bindValue(':empid', $this->employee_id);
	    $stmt->execute();
	    
	    $this->id = $stmt->fetchColumn();
	}
	
	public function delete() {
	    if ($this->id === null)
	        return;
	    
	    $stmt = Database::getPDOObject()->prepare("DELETE FROM work_shifts WHERE id = :id");
	    $stmt->bindValue(':id', $this->id);
	    $stmt->execute();   
	}
	
	public static function find($id) {
	    $stmt = Database::getPDOObject()->prepare("SELECT * FROM work_shifts WHERE id = :id");
	    $stmt->bindValue(':id', $id);
	    $stmt->execute();
	    
	    $shift = new WorkShift();
	    $stmt->setFetchMode(PDO::FETCH_INTO, $shift);
	    $stmt->fetch(PDO::FETCH_INTO);
	    
	    if ($shift->id == null)
	        return null;
	        
	    return $shift;
	}
	
	/**
	 * Finds all work shifts for the specified employee between $fromDate and $toDate.
	 *
	 * @param	Employee	$employee
	 * @param	int			$fromDate
	 * @param	int			$toDate
	 * @return	QueryIterator
	 */
	public static function findByEmployee(Employee $employee, $fromDate, $toDate) {
		$stmt = Database::getPDOObject()->prepare(
			"SELECT * FROM work_shifts WHERE ".
			"    employee_id = :empid ".
			"AND start_time >= :fromDate ".
			(($toDate != null) ? "AND start_time <= :toDate" : "").
            " ORDER BY start_time");
		
		$stmt->bindValue(':empid', $employee->id);
		$stmt->bindValue(':fromDate', date('Y-m-d', $fromDate));
		
		if ($toDate != null)
		    $stmt->bindValue(':toDate', date('Y-m-d', $toDate+Calendar::SecondsInDay));
		
		return new QueryIterator('WorkShift', $stmt);
	}
    
    public static function findNextWorkshift(Employee $employee) {
        $stmt = Database::getPDOObject()->prepare(
            "SELECT * FROM work_shifts WHERE employee_id = :empid AND start_time > now()".
            "  ORDER BY start_time ASC ".
            "  LIMIT 1");
            
        $stmt->bindValue(':empid', $employee->id);
        $stmt->execute();
        
        $shift = new WorkShift();
        $stmt->setFetchMode(PDO::FETCH_INTO, $shift);
        $stmt->fetch(PDO::FETCH_INTO);
        
        if ($shift->id == null)
            return null;
            
        return $shift;
    }
	
	/**
	 * Finds the earliest work coming time for the specified employee between the
	 * $fromDate and $toDate.
	 * 
	 * The time is returned as minutes since midnight. If there is no work shifts
	 * between the given dates, the value corresponding to 8 AM will be returned.
	 *
	 * @param	Employee	$employee
	 * @param	int			$fromDate
	 * @param	int			$toDate
	 */
	public static function findEarliestWorkComingTime(Employee $employee, $fromDate, $toDate) {
		$stmt = Database::getPDOObject()->prepare(
			"SELECT MIN( EXTRACT(hour FROM start_time) * 60 + EXTRACT(minute FROM start_time) ) ".
			"FROM work_shifts ".
			"WHERE employee_id = :empid ".
			"  AND start_time BETWEEN :fromDate AND :toDate");
		
		$stmt->bindValue(':empid', $employee->id);
		$stmt->bindValue(':fromDate', date('Y-m-d', $fromDate));
		$stmt->bindValue(':toDate', date('Y-m-d', $toDate));
		$stmt->execute();
		
		$minutes = $stmt->fetchColumn();
		if (!($minutes))
			$minutes = 8 * 60;
			
		return $minutes;
	}
	
/**
	 * Finds the latest working time for the specified employee between the
	 * $fromDate and $toDate.
	 * 
	 * The time is returned as minutes since midnight. If there is no work shifts
	 * between the given dates, the value corresponding to 4 PM will be returned.
	 *
	 * @param	Employee	$employee
	 * @param	int			$fromDate
	 * @param	int			$toDate
	 */
	public static function findLatestWorkingTime(Employee $employee, $fromDate, $toDate) {
		$stmt = Database::getPDOObject()->prepare(
			"SELECT MAX( EXTRACT(hour FROM end_time) * 60 + EXTRACT(minute FROM end_time) ) ".
			"FROM work_shifts ".
			"WHERE employee_id = :empid ".
			"  AND start_time BETWEEN :fromDate AND :toDate");
		
		$stmt->bindValue(':empid', $employee->id);
		$stmt->bindValue(':fromDate', date('Y-m-d', $fromDate));
		$stmt->bindValue(':toDate', date('Y-m-d', $toDate));
		$stmt->execute();
		
		$minutes = $stmt->fetchColumn();
		if (!($minutes))
			$minutes = 16 * 60;
			
		return $minutes;
	}
}
?>