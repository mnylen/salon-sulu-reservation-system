<?php
require_once 'lib/forms/forms.php';
require_once 'lib/forms/fields.php';

class LoginForm extends Form {
	public function __construct($data = null, $initial = null) {
		parent::__construct($data, $initial);
		
		$this->addField('username', new CharField());
		$this->addField('password', new CharField());
	}
}
?>