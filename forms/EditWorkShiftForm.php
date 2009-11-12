<?php
require_once 'lib/forms/forms.php';
require_once 'lib/forms/fields.php';
require_once 'lib/forms/util.php';

class EditWorkShiftForm extends Form {
    public function __construct($data = null, $initial = null) {
    	parent::__construct($data, $initial);
    	
    	$this->addField('date', new DateField());	
    	$this->addField('start_time', new TimeField());
    	$this->addField('end_time', new TimeField());
    }
    
    public function clean() {
        if (count($this->errors) != 0)
            return $this->cleanedData;
        
        $start_time = $this->getCleanData('start_time');
        $end_time   = $this->getCleanData('end_time');
        $date       = $this->getCleanData('date');
        
        $start_time = $start_time->setDate(
            date('Y', $date->getTimestamp()),
            date('m', $date->getTimestamp()),
            date('d', $date->getTimestamp()));
            
        $end_time = $end_time->setDate(
            date('Y', $date->getTimestamp()),
            date('m', $date->getTimestamp()),
            date('d', $date->getTimestamp()));
       
        if ($start_time->getTimestamp() < time())
            throw new ValidationError("Starting time must not be in the past.");
            
        if ($end_time->getTimestamp() <= $start_time->getTimestamp())
            throw new ValidationError("Ending time must be after starting time.");
        
        $this->cleanedData['start_time'] = $start_time;
        $this->cleanedData['end_time']   = $end_time;
        
        return $this->cleanedData;
    }
}
?>
