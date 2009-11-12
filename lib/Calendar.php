<?php
require_once 'models/Employee.php';
require_once 'models/WorkShift.php';
require_once 'models/Reservation.php';

class Calendar {
	/**
	 * Duration of one cell in minutes. This should be something
	 * 60 is dividable by.
	 *
	 * @var	int
	 */
	protected $cellDuration;
	
	/**
	 * The dates covered by the calendar. Each date is represented
	 * as Unix Epoch Time timestamp set to the midnight of the day
	 * in question.
	 *
	 * @var	array
	 */
	protected $dates;
	
	/**
	 * The employee this calendar is for.
	 *
	 * @var	Employee
	 */
	protected $employee;
	
	/**
	 * The starting time of the first cell as seconds since midnight.
	 *
	 * @var	int
	 */
	protected $firstCellTime;
	
	/**
	 * The ending time of the last cell as seconds since midnight.
	 *
	 * @var int
	 */
	protected $lastCellTime;
	
	/**
	 * A query iterator containing all reservations between the
	 * first and last day of the calendar.
	 *
	 * @var	QueryIterator
	 */
	protected $reservations;
	
	/**
	 * A query iterator containing all work shifts between the
	 * first and last day of the calendar.
	 *
	 * @var	QueryIterator
	 */
	protected $shifts;
	
	/**
	 * An two-dimensional array containing all cells for the calendar.
	 * Keyed by the date.
	 *
	 * @var	array
	 */
	protected $cells = array();
	
	/**
	 * How many seconds there is in one minute.
	 * 
	 * @var	int
	 *
	 */
	const SecondsInMinute = 60;
	
	/**
	 * How many seconds there is in one hour.
	 * 
	 * @var	int
	 *
	 */
	const SecondsInHour = 3600;
	
	/**
	 * How many seconds there is in one day.
	 *
	 * @var	int
	 */
	const SecondsInDay = 86400;
	
	/**
	 * Creates a new Calendar between $startDate and $endDate for the specified
	 * employee.
	 *
	 * @param	Employee	$employee
	 * @param	int			$startDate
	 * @param	int			$endDate
	 */
	public function __construct(Employee $employee, $startDate, $endDate) {
		$this->employee = $employee;
		$this->dates    = $this->createDates($startDate, $endDate);
		
		// Initialize and create cells
		$this->init();
		$this->createCells();
	}
	
	/**
	 * Creates an array of dates that are between the given start date and
	 * end date.
	 *
	 * @param int	$startDate
	 * @param int	$endDate
	 */
	private function createDates($startDate, $endDate) {
		$startDate   = mktime(0, 0, 0, date('n', $startDate), date('j', $startDate), date('Y', $startDate));
		$endDate     = mktime(0, 0, 0, date('n', $endDate), date('j', $endDate), date('Y', $endDate));
		$currentDate = $startDate;
		$dates       = array();
		
		while ($currentDate < $endDate) {
			$dates[]      = $currentDate;
			$currentDate += Calendar::SecondsInDay;
		}
		
		return $dates;
	}
	
	public function getDates() {
		return $this->dates;
	}
	
	/**
	 * Gets the cells covered by the calendar as rows. Each row
	 * in the returned array represent specific time period.
	 *
	 * @return array
	 */
	public function getCellRows() {
		$cells = array();
		$i	   = 0;
		
		// Just transpose the cells array
		foreach ($this->dates as $date) {
			for ($j = 0, $jj = count($this->cells[$date]); $j < $jj; $j++)
				$cells[$j][$i] = $this->cells[$date][$j];
				
			$i++;
		}
		
		return $cells;
	}
	
	/**
	 * Gets the cells for given date.
	 *
	 * @param	int		$date
	 * @return	array	
	 */
	public function getCells($date) {
		return $this->cells[$date];
	}
	
	/**
	 * Initializes everything.
	 *
	 */
	private function init() {
		require 'config/settings.php';
		$this->cellDuration = $SETTINGS['time_period_duration'];
		
		$firstDay = $this->dates[0];
		$lastDay  = $this->dates[count($this->dates)-1];
		
		// Find out the time of first and last cell		
		$this->firstCellTime = WorkShift::findEarliestWorkComingTime($this->employee, $firstDay, $lastDay) * Calendar::SecondsInMinute;
		$this->lastCellTime  = WorkShift::findLatestWorkingTime($this->employee, $firstDay, $lastDay) * Calendar::SecondsInMinute;

		// Load all reservations and working shifts for the period of the calendar
		$this->reservations = Reservation::findByEmployee($this->employee, time(), $lastDay);
		$this->shifts = WorkShift::findByEmployee($this->employee, $firstDay, $lastDay);
	}
	
	/**
	 * Creates the cells.
	 *
	 */
	private function createCells() {
		foreach ($this->dates as $day) {
			$cells = array();
			$time  = $day + $this->firstCellTime;
			
			while ($time <= ($day + $this->lastCellTime)) {
				$endTime = $time + ($this->cellDuration * Calendar::SecondsInMinute);
				$cell    = new Cell($time, $endTime);
				
				// 1. Check availability of the employee
				$this->shifts->rewind();
				while ( ($shift = $this->shifts->next()) !== null ) {
					if ($time >= $shift->start_time && $endTime <= $shift->end_time) {
						$cell->setAvailable(true);
						$cell->setEmpty(true);
						break;
					}
				}
				
				if ($cell->isAvailable()) {
					// 2. Check whether the cell is reserved
					$this->reservations->rewind();
					while ( ($reservation = $this->reservations->next()) !== null ) {
						if ($time >= $reservation->start_time && $time < $reservation->end_time) {
							$cell->setEmpty(false);
							$cell->setReserved(true);
							$cell->setReservation($reservation);
							break;
						}
					}
				}
				
				$cells[] = $cell;
				$time    = $endTime;
			}
			
			$this->cells[$day] = $cells;
		}
	}
	
	/**
	 * Tags all reservable cells as reservable.
	 * 
	 * A cell is considered reservable if it is empty and there's enough
	 * cells below it to make a total duration of $reservationDuration.
	 * 
	 * For example, if the calendar looks like this:
	 *      ----------------------------------
	 *      | Mon	   | Tue		| Wed
	 *      |---------------------------------
	 * 8:15 | EMPTY	   | EMPTY		| OCCUPIED
	 * 8:30	| EMPTY	   | OCCUPIED	| EMPTY
	 * 9:15	| OCCUPIED | EMPTY		| EMPTY
	 * 
	 * We want to tag all cells that can be the "starting cells" for 30 minutes
	 * reservation. After $calendar->tagAllReservable(30) the calendar would
	 * look like:
	 * 
	 *      ----------------------------------
	 *      | Mon	     | Tue		| Wed
	 *      |---------------------------------
	 * 8:15 | RESERVABLE | EMPTY	| OCCUPIED
	 * 8:30	| EMPTY	     | OCCUPIED	| RESERVABLE
	 * 9:15	| OCCUPIED   | EMPTY	| EMPTY
	 * 
	 * Note that the empty cells not having another empty cell below
	 * wont be tagged.
	 *
	 * @param int	$reservationDuration	Duration of the reservation in minutes
	 */
	public function tagAllReservable($reservationDuration) {
		$now = time();
		
		foreach ($this->dates as $date) {
			$cells = $this->getCells($date);
			
			for ($i = 0, $ii = count($cells); $i < $ii; $i++) {
				$cell = $cells[$i];

				if (!($cell->isEmpty()) || $cell->getStartTime() < $now)
					continue;
				
				$duration = $this->cellDuration;
				
				for ($j = $i+1; $j < $ii; $j++) {
					
					if ($duration >= $reservationDuration)
						break;
					
					if (!($cells[$j]->isEmpty()))
						break;
						
					$duration += $this->cellDuration;
				}
				
				if ($duration >= $reservationDuration)
					$cell->setReservable(true);
			}
		}
	}
}

/**
 * Represents a single cell in the calendar.
 *
 */
class Cell {
	protected $available;
	protected $reservable;
	protected $empty;
	protected $reserved;
	protected $reservation;
	protected $startTime;
	protected $endTime;
	
	/**
	 * Creates a new cell that starts and ends at the specified times.
	 *
	 * @param	int	$startTime
	 * @param	int	$endTime
	 */
	public function __construct($startTime, $endTime) {
		$this->startTime = $startTime;
		$this->endTime   = $endTime;
	}
	
	/**
	 * Gets whether the cell is available (employee
	 * is working).
	 * 
	 * @return bool
	 */
	public function isAvailable() {
		return $this->available;
	}
	
	/**
	 * Sets whether the cell is available (employee
	 * is working).
	 *
	 * @param	bool	$available
	 */
	public function setAvailable($available) {
		$this->available = $available;
	}
	
	/**
	 * Gets whether the cell is reservable.
	 *
	 * @return	bool
	 */
	public function isReservable() {
		return $this->reservable;
	}
	
	/**
	 * Sets whether the cell is reservable.
	 *
	 * @param	bool	$reservable
	 */
	public function setReservable($reservable) {
		$this->reservable = $reservable;
	}
	
	/**
	 * Gets whether the cell is empty (not reserved)
	 *
	 * @return	bool
	 */
	public function isEmpty() {
		return $this->empty;
	}
	
	/**
	 * Sets whether the cell is empty (not reserved)
	 *
	 * @param	bool	$empty
	 */
	public function setEmpty($empty) {
		$this->empty = $empty;
	}
	
	/**
	 * Gets whether the cell is reserved.
	 *
	 * @return	bool
	 */
	public function isReserved() {
		return $this->reserved;
	}
	
	/**
	 * Sets whether the cell is reserved.
	 *
	 * @param	bool	$reserved
	 */
	public function setReserved($reserved) {
		$this->reserved = $reserved;
	}
	
	/**
	 * Gets the reservation associated with the cell.
	 *
	 * @return	Reservation
	 */
	public function getReservation() {
		return $this->reservation;
	}
	
	/**
	 * Sets the reservation associated with the cell.
	 *
	 * @param	Reservation	$reservation
	 */
	public function setReservation($reservation) {
		$this->reservation = $reservation;
	}
	
	/**
	 * Gets the starting time of the cell.
	 *
	 * @return	int
	 */
	public function getStartTime() {
		return $this->startTime;
	}
	
	/**
	 * Gets the ending time of the cell.
	 *
	 * @return	int
	 */
	public function getEndTime() {
		return $this->endTime;
	}
	
	/**
	 * Sets the ending time of the cell.
	 *
	 * @param	int		$endTime
	 */
	public function setEndTime($endTime) {
		$this->endTime = $endTime;
	}
}
?>