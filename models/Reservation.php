<?php
require_once 'lib/db/Database.php';
require_once 'lib/db/QueryIterator.php';
require_once 'models/Employee.php';

class Reservation {
	public $id;
	public $employee_id;
	public $service_id;
	public $start_time;
	public $end_time;
	public $price;
	public $cancel_key;
	public $cancelled;
	public $cust_fname;
	public $cust_lname;
	public $cust_email;
	public $cust_phone;
	
	public static $DbFields = array(
		'id', 'employee_id', 'service_id', 'start_time', 'end_time', 'price', 'cancel_key', 'cancelled',
		'cust_fname', 'cust_lname', 'cust_email', 'cust_phone',
		'EXTRACT(epoch FROM start_time) AS start_time',
		'EXTRACT(epoch FROM end_time) AS end_time'
	);
	
	/**
	 * Determines whether the reservation is cancellable. Reservation is cancellable
	 * if it has not been cancelled already and it's starting time is not in the past.
	 *
	 * @return	bool
	 */
	public function isCancellable() {
		if ($this->start_time > time() && !($this->cancelled))
			return true;
			
		return false;
	}
	
	/**
	 * Saves the reservation.
	 *
	 */
	public function save() {
		$sql = null;
		
		if ($this->id === null) {
			// Insert a new reservation
			$sql = "INSERT INTO reservations ".
				   "  (employee_id, service_id, start_time, end_time, price, ".
				   "   cancel_key, cust_fname, cust_lname, cust_email, cust_phone) ".
				   "VALUES (:empid, :servid, :stime, :etime, :price, gen_cancel_key(), ".
				   "        :fname, :lname, :email, :phone) RETURNING id";
		} else {
			$sql = "UPDATE reservations SET ".
				   "  employee_id = :empid, ".
				   "  service_id  = :servid, ".
				   "  start_time  = :stime, ".
				   "  end_time    = :etime, ".
				   "  price       = :price, ".
				   "  cust_fname  = :fname, ".
				   "  cust_lname  = :lname, ".
				   "  cust_email  = :email, ".
				   "  cust_phone  = :phone, ".
				   "  cancelled   = :cancelled, ".
				   "  cancel_key  = :ckey ".
				   "WHERE id = :id";
		}
		
		$stmt = Database::getPDOObject()->prepare($sql);
		$stmt->bindValue(':empid', $this->employee_id);
		$stmt->bindValue(':servid', $this->service_id);
		$stmt->bindValue(':stime', date('Y-m-d H:i', $this->start_time));
		$stmt->bindValue(':etime', date('Y-m-d H:i', $this->end_time));
		$stmt->bindValue(':price', $this->price);
		$stmt->bindValue(':fname', $this->cust_fname);
		$stmt->bindValue(':lname', $this->cust_lname);
		$stmt->bindValue(':email', $this->cust_email);
		$stmt->bindValue(':phone', $this->cust_phone);
		
		if ($this->id !== null) {
			$stmt->bindValue(':ckey',  $this->cancel_key);
			$stmt->bindValue(':cancelled', $this->cancelled);
			$stmt->bindValue(':id', $this->id);
		}
		
		$stmt->execute();
		
		if ($this->id === null)
			$this->id = $stmt->fetchColumn();
	}
	
	/**
	 * Finds a reservation by the ID. If no reservation exists with the given ID,
	 * null will be returned.
	 *
	 * @param	int	$id
	 * @return	Reservation
	 */
	public static function find($id) {
		$stmt = Database::getPDOObject()->prepare("SELECT ".implode(', ', self::$DbFields)." FROM reservations WHERE id = :id");
		$stmt->bindValue(':id', $id);
		$stmt->execute();
		
		$reservation = new Reservation();
		$stmt->setFetchMode(PDO::FETCH_INTO, $reservation);
		$stmt->fetch(PDO::FETCH_INTO);
		
		if ($reservation->id === null)
			return null;
		
		return $reservation;
	}
	
	public static function findByCancelKeyAndEmail($cancelKey, $email) {
		$stmt = Database::getPDOObject()->prepare(
			"SELECT ".implode(', ', self::$DbFields)." FROM reservations WHERE cancel_key = :cancelKey AND cust_email = :email");
		
		$stmt->bindValue(':cancelKey', $cancelKey);
		$stmt->bindValue(':email', $email);
		$stmt->execute();
		
		$reservation = new Reservation();
		$stmt->setFetchMode(PDO::FETCH_INTO, $reservation);
		$stmt->fetch(PDO::FETCH_INTO);
		
		if ($reservation->id === null)
			return null;
		
		return $reservation;
	}
	
	/**
	 * Finds all reservations by employee that occur between $startDate and $endDate.
	 *
	 * @param	Employee	$employee
	 * @param	int			$startDate
	 * @param	int			$endDate
	 * @return	QueryIterator
	 */
	public static function findByEmployee(Employee $employee, $startDate, $endDate) {
		$stmt = Database::getPDOObject()->prepare(
			"SELECT ".implode(', ', self::$DbFields)." FROM reservations ".
			"  WHERE employee_id = :empid ".
			"    AND cancelled   = FALSE ".
			"    AND date_trunc('day', start_time) >= :startDate ".
            "    AND date_trunc('day', start_time) <= :endDate"
		);
		
		$stmt->bindValue(':empid', $employee->id);
		$stmt->bindValue(':startDate', date('Y-m-d', $startDate));
		$stmt->bindValue(':endDate', date('Y-m-d', $endDate));
		
		return new QueryIterator('Reservation', $stmt);
	}
}
?>