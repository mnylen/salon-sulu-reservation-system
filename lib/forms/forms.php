<?php
require_once 'lib/forms/fields.php';
require_once 'lib/forms/util.php';

/**
 * A form that can be used to capture user input.
 *
 */
class Form {
	/**
	 * The key in the error array to store any non-field errors under.
	 *
	 */
	const NonFieldErrors = "__all__";
	
	/**
	 * A flag indicating whether the form is bound or not. Currently
	 * has no use, but it might be used.
	 *
	 * @var bool
	 */
	protected $isBound;
	
	/**
	 * The cleaned data.
	 *
	 * @var array
	 */
	protected $cleanedData;
	
	/**
	 * Form fields.
	 *
	 * @var array
	 */
	protected $fields;
	
	/**
	 * Errors that occured during cleaning.
	 *
	 * @var array
	 */
	protected $errors;
	
	/**
	 * The data for the form.
	 *
	 * @var array
	 */
	protected $data;
	
	/**
	 * Creates a new form with the given data.
	 *
	 * @param array $data
	 */
	public function __construct($data = null, $initial = null) {
		$this->data    = !(is_null($data)) ? $data : $initial;
		$this->isBound = !(is_null($data));
		$this->fields  = array();
		$this->errors  = null;
	}
	
	/**
	 * Adds a field to this form. Throws <code>Exception</code> if the form already has a
	 * field with the given name.
	 *
	 * @param string $name name of the field
	 * @param Field $field the field
	 */
	public function addField($name, Field $field) {
		if (array_key_exists($name, $this->fields))
			throw new Exception("form already contains field with name '".$name."'");
		 
		$this->fields[$name] = $field;
	}
	
	/**
	 * Gets the field with the specified name. Returns <code>null</code> if there's no
	 * such field.
	 *
	 * @param string $name
	 * @return Field
	 */
	public function getField($name) {
		return $this->fields[$name];
	}
	
	/**
	 * Returns whether the form is considered valid or not.
	 *
	 * @return bool
	 */
	public function isValid() {
		if ($this->isBound) {
			$errors = $this->getErrors();
			
			return (count($errors) == 0);
		}
		
		return false;
	}
	
	/**
	 * Gets the uncleaned data. If $name is set, the data is returned for that
	 * particular field.
	 *
	 * @param string $name
	 */
	public function getData($name = null) {
		return ($name !== null) ? $this->data[$name] : $this->data;
	}
	
	/**
	 * Gets the errors for this form. If the $name parameter is set, will return
	 * only errors for that particular field. If no clean has been done before
	 * calling, will do a full clean.
	 *
	 * @param string $name
	 * @return array
	 */
	public function getErrors($name = null) {
		if ($this->errors === null)
			$this->fullClean();
		
		return ($name === null) ? $this->errors : $this->errors[$name];
	}
	
	/**
	 * Gets the cleaned data. If no clean has been done before calling,
	 * will do a full clean.
	 *
	 * @return array
	 */
	public function getCleanedData() {
		if ($this->cleanedData === null)
			$this->fullClean();
			
		return $this->cleanedData;
	}
	
	/**
	 * Gets the cleaned data for a field with the specified name. If no clean has
	 * been done before calling, will do a full clean. Returns <code>null</code>
	 * if the cleaned data does not contain value for the field.
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function getCleanData($name) {
		if ($this->cleanedData === null)
			$this->fullClean();
			
		return $this->cleanedData[$name];
	}
	
	/**
	 * Does any cleaning that takes part <strong>after</strong> the <code>clean()</code>
	 * is called for all <code>Field</code>s in this form.
	 *
	 */
	protected function clean() {
		/**
		 * Nothing is done here, because the default form implementation does not
		 * need to do post-field-cleaning. Subclassers should implement any
		 * cleaning behaviour they want in the subclasses.
		 */
		
		return $this->cleanedData;
	}
	
	/**
	 * Does a full clean on all fields in the form.
	 *
	 */
	protected function fullClean() {
		$this->errors = array();
		$this->cleanedData = array();
		
		// No cleaning needs to be done if the form is not bound
		if (!($this->isBound))
			return;
		
		foreach ($this->fields as $name => $field) {
			$value = $this->data[$name];
			
			try {
				$value = $field->clean($value);
				
				// Call any cleaning functionality specified in clean_fieldname()
				// method in this form, if such method exists
				if (method_exists($this, "clean_".$name))
					$value = call_user_func(array($this, "clean_".$name), $value);
				
					
				$this->cleanedData[$name] = $value;
			} catch (ValidationError $ex) {
				$this->errors[$name] = $ex->getMessages();
			}
		}
		
		// Do post-field-clean cleaning
		try {
			$this->cleanedData = $this->clean();
		} catch (ValidationError $ex) {
			$this->errors[self::NonFieldErrors] = $ex->getMessages();
		}
	}
}

// XXX: It would be nice if there would be some kind of concept for
// bound fields, so we could access the fields data and errors directly
// from a BoundField instance instead of the current solution, where
// we do all data and error access through Form instances. One could get
// the BoundField instance for a field by calling Form::getField.
?>