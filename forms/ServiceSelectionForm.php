<?php
require_once 'lib/forms/forms.php';
require_once 'lib/forms/fields.php';
require_once 'models/Service.php';

class ServiceSelectionForm extends Form {
	protected $services;
	
	public function __construct($data = null, $initial = null) {
		parent::__construct($data, $initial);
		
		$this->services = Service::findAll(true);
		$this->addField('service', new ChoiceField($this->services->choices('id')));
	}
	
	public function clean_service($value) {
		return $this->services->get('id', $value);
	}
}
?>