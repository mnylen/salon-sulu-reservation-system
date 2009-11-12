<?php
require_once 'lib/forms/forms.php';
require_once 'lib/forms/fields.php';

class ContactInfoForm extends Form {
	public function __construct($data = null, $initial = null) {
		parent::__construct($data, $initial);
		
		$this->addField('cust_fname', new CharField());
		$this->addField('cust_lname', new CharField());
		$this->addField('cust_email', new EmailAddressField());
		$this->addField('cust_phone', new PhoneNumberField());
	}
}
?>