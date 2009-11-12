<?php
require_once 'lib/forms/forms.php';
require_once 'lib/forms/fields.php';
require_once 'models/Reservation.php';

class CancelReservationForm extends Form {
	public function __construct($data = null, $initial = null) {
		parent::__construct($data, $initial);
		
		$this->addField('cust_email', new CharField());
		$this->addField('cancel_key', new CharField());
	}
	
	public function clean() {
        if (count($this->errors) != 0)
            return $this->cleanedData;
        
		$reservation = Reservation::findByCancelKeyAndEmail(
			$this->getCleanData('cancel_key'), $this->getCleanData('cust_email'));
			
		if (!($reservation))
			throw new ValidationError('No reservation with the specified details found.');
			
		if (!($reservation->isCancellable()))
			throw new ValidationError('The reservation can not be cancelled.');
			
		$this->cleanedData['reservation'] = $reservation;
		return $this->cleanedData;
	}
}
?>