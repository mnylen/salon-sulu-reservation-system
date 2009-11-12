<?php
/**
 * An error thrown when a validation error occurs during cleaning a field.
 *
 */
class ValidationError extends ErrorException {
	public function __construct($message) {
		parent::__construct($message);
	}
	
	public function getMessages() {
		return array($this->getMessage());
	}
}
?>