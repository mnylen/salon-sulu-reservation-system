<?php
function query_string($_GET, $names) {
	$qstring = "?";
	
	foreach ($_GET as $name => $value) {
		if (in_array($name, $names))
			$qstring .= $name."=".$value."&";
	}
	
	return $qstring;
}

/**
 * Gets Unix Epoch Time timestamp for the midnight of the first day (Monday) of the week
 * represented by the given string. If parsing the string fails, <code>false</code> will
 * be returned.
 * 
 * @param	string		$week		The week in ISO week format (strftime format %Y-W%V)
 * @return	int	
 */
function getFirstDayOfWeek($week) {
	//
	// It seems strtotime is able to do all the hard work, but we
	// must ensure the given value is in correct format
	//
	
	$matches = array();
	if (!(preg_match('/([1-9][0-9]{3}-W[0-9]{2})/', $week, $matches)))
		return false;
	
	return strtotime($matches[1]);
}

/**
 * Same as getFirstDayOfWeek, but returns the first day for the next week
 * relative to the given week.
 *
 * @param	string	$week
 * @return	int
 */
function getFirstDayOfNextWeek($week) {
	$time = getFirstDayOfWeek($week);
	
	if (!($time))
		return false;
		
	return $time + (7 * Calendar::SecondsInDay);
}
?>