<?php
function to_iso_week($tstamp = null) {
    if ($tstamp == null)
        $tstamp = time();
    
    return date('Y-\WW', $tstamp);
}

function time_from_iso_week($week = null) {
    if ($week == null)
        $week = to_iso_week();
        
    $matches = array();
	if (!(preg_match('/([1-9][0-9]{3}-W[0-9]{2})/', $week, $matches)))
		return false;
	
	return strtotime($matches[1]);
}
?>