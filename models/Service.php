<?php
require_once 'lib/db/Database.php';
require_once 'lib/db/QueryIterator.php';

class Service {
	public $id;
	public $available;
	public $name;
	public $description;
	public $price;
	public $duration;
	
	/**
	 * Returns whether the service is reservable.
	 *
	 * @return	bool
	 */
	public function isReservable() {
		$stmt = Database::getPDOObject()->prepare("SELECT COUNT(id) FROM reservable_services WHERE id = :id");
		$stmt->bindValue(':id', $this->id);
		$stmt->execute();
		
		return ($stmt->fetchColumn() != 0);
	}
	
	/**
	 * Gets a list of all employees performing the current service.
	 *
	 * @return	QueryIterator
	 */
	public function getPerformingEmployees() {
		$stmt = Database::getPDOObject()->prepare(
			"SELECT * FROM employees ed WHERE ".
			"  ed.active = TRUE AND (".
			"    SELECT COUNT(id) FROM employee_service_catalog ".
			"      WHERE employee_id = ed.id AND service_id = :servid) > 0");
		
		$stmt->bindValue(':servid', $this->id);
		return new QueryIterator('Employee', $stmt);
	}
	
	/**
	 * Updates performing employees for the current service.
	 *
	 * @param	array	$employeeIds
	 */
	public function updatePerformingEmployees($employeeIds) {
		$currentEmployees = $this->getPerformingEmployees()->flatten('id');
		
		foreach ($employeeIds as $employeeId) {
			if (!(in_array($employeeId, $currentEmployees))) {
				// Create a new association between service and employee
				$stmt = Database::getPDOObject()->prepare("INSERT INTO employee_service_catalog (service_id, employee_id) VALUES (:servid, :empid)");
				$stmt->bindValue(':servid', $this->id);
				$stmt->bindValue(':empid', $employeeId);
				$stmt->execute();
			}
		}
		
		foreach ($currentEmployees as $employeeId) {
			if (!(in_array($employeeId, $employeeIds))) {
				// Remove the association between service and employee
				$stmt = Database::getPDOObject()->prepare("DELETE FROM employee_service_catalog WHERE service_id = :servid AND employee_id = :empid");
				$stmt->bindValue(':servid', $this->id);
				$stmt->bindValue(':empid', $employeeId);
				$stmt->execute();
			}
		}
	}
	
	/**
	 * Saves the service. After saving, you can query the assigned ID.
	 *
	 */
	public function save() {
		if ($this->id === null) {
			// Insert a new record
			$this->insertRecord();
		} else {
			// Update the record
			$this->updateRecord();
		}
	}
	
	/**
	 * Inserts a record to services table.
	 *
	 */
	private function insertRecord() {
		$sql = "INSERT INTO service_catalog (name, description, duration, price, available) ".
			   "  VALUES (:name, :description, :duration, :price, :available) ".
			   "RETURNING id";
		
		$stmt = Database::getPDOObject()->prepare($sql);
		$stmt->bindValue(":name", $this->name);
		$stmt->bindValue(":description", $this->description);
		$stmt->bindValue(":duration", $this->duration);
		$stmt->bindValue(":price", $this->price);
		$stmt->bindValue(':available', ($this->available ? 'TRUE' : 'FALSE'));
		$stmt->execute();
		
		$this->id = $stmt->fetchColumn();
	}
	
	/**
	 * Updates a record in services table.
	 *
	 */
	private function updateRecord() {
		$sql = "UPDATE service_catalog SET ".
			   "  name = :name, ".
			   "  description = :description, ".
			   "  duration = :duration, ".
			   "  price = :price, " .
			   "  available = :available ".
			   "WHERE id = :id";
		
		$stmt = Database::getPDOObject()->prepare($sql);
		$stmt->bindValue(":name", $this->name);
		$stmt->bindValue(":description", $this->description);
		$stmt->bindValue(":duration", $this->duration);
		$stmt->bindValue(":price", $this->price);
		$stmt->bindValue(":available", ($this->available ? 'TRUE' : 'FALSE'));
		$stmt->bindValue(":id", $this->id);
		$stmt->execute();
	}
	
	/**
	 * Finds all services. If the $reservable parameter is set to true, will return
	 * only services that are currently reservable.
	 * 
	 * A service is considered reservable if and only if it has it's available flag
	 * set to true and it has at least one performing employee.
	 *
	 * @param	bool	$onlyReservable
	 * @return	QueryIterator
	 */
	public static function findAll($onlyReservable = false) {
		$sql = null;
		if ($onlyReservable)
			$sql = "SELECT * FROM reservable_services ORDER BY duration ASC";
		else
			$sql = "SELECT * FROM service_catalog ORDER BY duration ASC";
		
		
		$stmt = Database::getPDOObject()->prepare($sql);
		return new QueryIterator('Service', $stmt);
	}
	
	/**
	 * Finds the service with given ID. If no service with the given ID is found,
	 * returns null.
	 *
	 * @param	int	$id
	 * @return	Service
	 */
	public static function find($id) {
		$stmt = Database::getPDOObject()->prepare("SELECT * FROM service_catalog WHERE id = :id");
		$stmt->bindValue(':id', $id);
		$stmt->execute();
		
		$service = new Service();
		$stmt->setFetchMode(PDO::FETCH_INTO, $service);
		$stmt->fetch(PDO::FETCH_INTO);
		
		if ($service->id === null)
			return null;
			
		return $service;
	}
}
?>