<?php
require_once 'models/Employee.php';
require_once 'models/Service.php';
require_once 'lib/forms/forms.php';
require_once 'lib/forms/fields.php';

class EditServiceForm extends Form {
    public function __construct($data = null, $initial = null) {
    	parent::__construct($data, $initial);
    	
    	$this->addField('name', new CharField());
    	$this->addField('description', new CharField());
    	$this->addField('duration', new IntegerField(true, 0));
    	$this->addField('price', new FloatField(true, 0));
    	$this->addField('available', new BooleanField());
    	$this->addField('perf_emp', new MultiChoiceField(Employee::findAll(true)->choices('id')));
    }
}
?>
