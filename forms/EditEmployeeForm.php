<?php
require_once 'lib/forms/forms.php';
require_once 'lib/forms/fields.php';

class EditEmployeeForm extends Form {
    public function __construct($data = null, $initial = null) {
    	parent::__construct($data, $initial);
    	
    	$this->addField('first_name', new CharField());
    	$this->addField('last_name', new CharField());
    	$this->addField('active', new BooleanField());
    }
}
?>
