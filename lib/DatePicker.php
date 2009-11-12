<?php
class DatePicker {
    private $month;
	private $year;
	
	/**
	 * Creates a new <code>DatePicker</code>.
     *
	 */
	public function __construct() {
		$calmonth = $_GET['calmonth'];
		$calmonth = strtotime($calmonth);
		
		if (!($calmonth))
			$calmonth = strtotime(date('Y-m'));

		$this->baseURL = $baseURL;
		$this->month   = idate('m', $calmonth);
		$this->year    = idate('Y', $calmonth);
	}
	
	public function printDatePicker() {
		// Determine the first day to display in the date picker
		$first = mktime(0, 0, 0, $this->month, 1, $this->year);
		$first -= 86400 * (date('N', $first) - 1);
        
				
		// Determine the last day to display in the date picker
		$last  = mktime(0, 0, 0, $this->month+1, 1, $this->year);
		
		if (date('N', $last) == 1) {
			$last -= 86400;
		} else {
			$last += 86400 * (7 - date('N', $last));
		}
		
		// Print the date picker
		print "<table class=\"datePicker\">\r\n";
		
		$current = $first;
		$wday    = 1;
		while ($current <= $last) {
			if ($wday == 1)
				print "\t<tr>\r\n";
			
            $url  = $_SERVER['SCRIPT_NAME'];
            $url .= "?date=".date('Y-m-d', $current)."&";
            $url .= "calmonth=".date('Y-m', $current);
            
            foreach ($_GET as $key => $value) {
                if ($key != "date" && $key != "calmonth")
                    $url .= "&".$key."=".$value;
            }
            
            
			print "\t\t<td";
            
            if ($this->getSelectedDate() == $current)
                print ' class="selectedDate"';
            
            print "><a href=\"".$url."\">".date('d', $current)."</a></td>\r\n";
			
			if ($wday == 7)
				print "\t</tr>\r\n";
			
			
			$wday = ($wday == 7) ? 1 : $wday+1;
			$current += 86400;
		}
	}
    
    public function getSelectedDate() {
        $date = strtotime($_GET['date']);
        
        if ($date)
            return $date;
        
        // No date selected - use default
        if ($this->month == idate('m') && $this->year == idate('Y')) {
            $today = time();
            return strtotime(date('Y-m-d', $today));
        } else {
            return mktime(0, 0, 0, $this->month, 1, $this->year);
        }
    }
}
?>