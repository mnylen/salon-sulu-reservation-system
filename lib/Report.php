<?php
require_once 'lib/db/Database.php';

class Report {
	/**
	 * The starting date of the report.
	 *
	 * @var int
	 */
	private $periodStartDate;
	
	/**
	 * The ending date of the report.
	 *
	 * @var int
	 */
	private $periodEndDate;
	
	/**
	 * The array containing all report fields.
	 *
	 * @var array
	 */
	private $report;
	
	/**
	 * Creates a new report taking place between the given starting and ending date
	 * of a period.
	 *
	 * @param	int	$periodStartDate (inclusive)
	 * @param	int	$periodEndDate (inclusive)
	 */
	public function __construct($periodStartDate, $periodEndDate) {
		$this->periodStartDate = $periodStartDate;
		$this->periodEndDate   = $periodEndDate;
		$this->report          = array();
		
		// Find the total number of reservations
		$sql = "SELECT COUNT(*) AS reservation_count FROM reservations WHERE start_time BETWEEN :startDate AND :endDate";
		$this->execAndFetch($sql);

		// Find the total number of cancelled reservations
		$sql = "SELECT COUNT(*) AS cancelled_count FROM reservations WHERE cancelled = TRUE AND start_time BETWEEN :startDate AND :endDate";
		$this->execAndFetch($sql);
		
		// Get the service stats
		$sql = "SELECT rs.service_id, sc.name, COUNT(rs.service_id) AS reservation_count FROM reservations rs ".
		       " LEFT JOIN service_catalog sc ON sc.id = rs.service_id ".
		       "WHERE rs.start_time BETWEEN :startDate AND :endDate ".
		       "GROUP BY rs.service_id, sc.name ORDER BY reservation_count DESC";
		
		$this->execAndFetch($sql, 'serviceStats');
		
		// Get the employee stats
		$sql = "SELECT rs.employee_id, emp.first_name, emp.last_name, COUNT(rs.employee_id) AS reservation_count FROM reservations rs ".
		       " LEFT JOIN employees emp ON emp.id = rs.employee_id ".
		       "WHERE rs.start_time BETWEEN :startDate AND :endDate ".
		       "GROUP BY rs.employee_id, emp.first_name, emp.last_name ORDER BY reservation_count DESC";
		
		$this->execAndFetch($sql, 'employeeStats');
	}
	
	public function __get($name) {
		return $this->report[$name];
	}
	
	private function execAndFetch($sql, $asKey = null) {
		$stmt = Database::getPDOObject()->prepare($sql);
		$stmt->bindValue(':startDate', date('Y-m-d', $this->periodStartDate));
		$stmt->bindValue(':endDate', date('Y-m-d', $this->periodEndDate+Calendar::SecondsInDay));
		$stmt->execute();
		
		if ($asKey == null) {
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$this->report = array_merge($this->report, $row);
		} else {
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$this->report[$asKey] = $results;
		}
	}
	
	public function getPeriodStartDate() {
		return $this->periodStartDate;
	}
	
	public function getPeriodEndDate() {
		return $this->periodEndDate;
	}
}
?>