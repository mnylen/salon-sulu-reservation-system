<?php
require_once 'lib/forms/util.php';

//
// XXX: Currently the error messages are hardcoded. It would
// be nice if the users had the opportunity to provide their
// own error messages. Take a look on how Django does this.
//

/**
 * Repreresents a field in form. The field itself does not have any bound
 * data; the data is contained in the form. A Field merely represents how
 * particular field should behave with different inputs.
 */
class Field {
	/**
	 * A flag indicating whether the field <strong>must</strong> have a value.
	 *
	 * @var unknown_type
	 */
	protected $required;
	
	/**
	 * Creates a new <code>Field</code>.
	 *
	 * @param bool $required is the field required?
	 */
	public function __construct($required = true) {
		$this->required = $required;
	}
	
	/**
	 * Validates the given value and returns its "cleaned" value as an approriate
	 * PHP data type.
	 * 
	 * <p>Throws <code>ValidationError</code> for any errors.</p>
	 *
	 * @param string $value the value to clean
	 * @return mixed the cleaned value
	 */
	public function clean($value) {
		if ($this->required && empty($value))
			throw new ValidationError("This field is required.");		
		return $value;
	}
	
	/**
	 * Returns whether this field is required.
	 *
	 * @return bool
	 */
	public function isRequired() {
		return $this->required;
	}
	
	/**
	 * Sets whether this field is required.
	 *
	 * @param bool $required
	 */
	public function setRequired($required) {
		$this->required = $required;
	}
}

/**
 * A character input field.
 *
 */
class CharField extends Field {
	/**
	 * Maximum length of the input.
	 *
	 * @var int
	 */
	protected $maxLength;
	
	/**
	 * Minimum length of the input.
	 *
	 * @var int
	 */
	protected $minLength;
	
	/**
	 * Creates a new <code>CharField</code>.
	 *
	 * @param bool $required is the field required?
	 * @param int $maxLength maximum length of the input - <code>null</code> for any input length
	 * @param int $minLength minimum length of the input - <code>null</code> for any input length
	 */
	public function __construct($required = true, $maxLength = null, $minLength = null) {
		$this->required  = $required;
		$this->maxLength = $maxLength;
		$this->minLength = $minLength;
	}
	
	/**
	 * Validates maximum and minimum length in addition to validations done in Field.
	 *
	 * @param string $value
	 * @return string
	 */
	public function clean($value) {
		$value = parent::clean($value);
		$valueLength = strlen($value);
		
		if ($this->maxLength !== null && $valueLength > $this->maxLength) {
			throw new ValidationError(sprintf("Ensure this value has at most %d characters (it has %d)",
				$this->maxLength, $valueLength));
		}
		
		if ($this->minLength !== null && $valueLength < $this->minLength) {
			throw new ValidationError(sprintf("Ensure this value has at least %d characters (it has %d)",
				$this->minLength, $valueLength));
		}
		
		return $value;
	}
}

/**
 * A field for inputting an email address.
 */
class EmailAddressField extends Field {
    public function __construct($required = true) {
        $this->required = $required;
    }
    
    public function clean($value) {
        $value = parent::clean($value);
        
        if (empty($value))
            return $value;
        
        if (!(preg_match("/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i", $value)))
            throw new ValidationError("Invalid email address");
            
        return $value;
    }
}

/**
 * A field for inputting an phone number.
 */
class PhoneNumberField extends Field {
    public function __construct($required = true) {
        $this->required = $required;
    }
    
    public function clean($value) {
        $value = parent::clean($value);
        
        if (empty($value))
            return $value;
            
        if (!(preg_match("/^\+?([0-9]+[ \-]?)+$/", $value)))
            throw new ValidationError("Invalid phone number");
        
        return $value;
    }
}

/**
 * A integer field.
 *
 */
class IntegerField extends Field {
	/**
	 * The minimum value for the field.
	 *
	 * @var int
	 */
	protected $minimum;
	
	/**
	 * The maximum value for the field.
	 *
	 * @var int
	 */
	protected $maximum;
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $required
	 * @param unknown_type $minimum
	 * @param unknown_type $maximum
	 */
	public function __construct($required = true, $minimum = null, $maximum = null) {
		$this->required = $required;
		$this->minimum = $minimum;
		$this->maximum = $maximum;
	}
	
	/**
	 * Validates the value is integer. Also validates the value is between
	 * the given range.
	 *
	 * @param mixed $value
	 * @return int
	 */
	public function clean($value) {
		$value = parent::clean($value);
		
		if (empty($value))
			return $value;
		
		//
		// XXX: Currently we check the value is integer instead
		// of float by checking that the intval() and floatval()
		// return the same value. This might not work in all
		// scenarios, but for the most part, it works.
		//
		if (!(is_numeric($value)) || intval($value) != floatval($value))
			throw new ValidationError("Enter a whole number.");
				
		$value = intval($value);
			
		if ($this->minimum !== null && $value < $this->minimum) {
			throw new ValidationError(sprintf("Ensure this value is greater than or equal to %d",
				$this->minimum));
		}
			
		if ($this->maximum !== null && $value > $this->maximum) {
			throw new ValidationError(sprintf("Ensure this value is less than or equal to %d",
				$this->maximum));
		}
		
		return $value;
	}
}

/**
 * An floating point number field.
 *
 */
class FloatField extends Field {
	/**
	 * The minimum value for the field.
	 *
	 * @var int
	 */
	protected $minimum;
	
	/**
	 * The maximum value for the field.
	 *
	 * @var int
	 */
	protected $maximum;
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $required
	 * @param unknown_type $minimum
	 * @param unknown_type $maximum
	 */
	public function __construct($required = true, $minimum = null, $maximum = null) {
		$this->required = $required;
		$this->minimum = $minimum;
		$this->maximum = $maximum;
	}
	
	/**
	 * Validates the value is a float value. Also validates the value is between
	 * the given range.
	 *
	 * @param mixed $value
	 * @return int
	 */
	public function clean($value) {
		$value = parent::clean($value);
		
		if (empty($value))
			return $value;
		
		if (!(is_numeric($value)))
			throw new ValidationError("Enter an floating point number.");
				
		$value = intval($value);
			
		if ($this->minimum !== null && $value < $this->minimum) {
			throw new ValidationError(sprintf("Ensure this value is greater than or equal to %d",
				$this->minimum));
		}
			
		if ($this->maximum !== null && $value > $this->maximum) {
			throw new ValidationError(sprintf("Ensure this value is less than or equal to %d",
				$this->maximum));
		}
		
		return $value;
	}
}

/**
 * A choice field; the value must be one of the predefined choices.
 *
 */
class ChoiceField extends Field {
	/**
	 * An associative array containing all possible choices for the
	 * field. The key of any array element should be the actual value
	 * and the the value should be the label for the choice.
	 *
	 * @var array
	 */
	protected $choices;
	
	/**
	 * Creates a new <code>ChoiceField</code>.
	 *
	 * @param array $choices the choices
	 * @param bool $required is this field required?
	 */
	public function __construct($choices, $required = true) {
		parent::__construct($required);
		
		$this->choices = $choices;
	}
	
	/**
	 * Validates that the value is one of the available choices.
	 *
	 * @param string $value
	 * @return mixed
	 */
	public function clean($value) {
		$value = parent::clean($value);
		
		if (!(empty($value))) {
			if (!(array_key_exists($value, $this->choices))) {
				throw new ValidationError(
					sprintf("Select a valid choice. '%s' is not one of the available choices.", $value));	
			}
		}
		
		return $value;
	}
	
	/**
	 * Gets the choices.
	 *
	 * @return array
	 */
	public function getChoices() {
		return $this->choices;
	}
	
	/**
	 * Sets the choices.
	 *
	 * @param array $choices
	 */
	public function setChoices($choices) {
		$this->choices = $choices;
	}
}

class MultiChoiceField extends ChoiceField {
	public function clean($value) {
		if (!(is_array($value)))
			$value = array($value);
			
		foreach ($value as $selection) {
			parent::clean($selection);		
		}
		
		return $value;
	}
}

class DateTimeField extends Field {
	public static $DefaultDateTimeFormats = array(
		"%s", // Unix Epoch Time timestamp
		      // XXX: Add some other formats here.
	);
	
	protected $outputFormat;
	protected $inputFormats;
	
	public function __construct($required = true, $outputFormat = "%s", $inputFormats = null) {
		parent::__construct($required);
		
		$this->outputFormat = $outputFormat;
		$this->inputFormats = ($inputFormats != null) ? $inputFormats : self::$DefaultDateTimeFormats;
	}
	
	public function clean($value) {
		$value = parent::clean($value);
		
		foreach ($this->inputFormats as $inputFormat) {
			if ( ($datearr = strptime($value, $inputFormat)) ) {
				$tstamp = mktime(
					$datearr['tm_hour'], $datearr['tm_min'], $datearr['tm_sec'],
					$datearr['tm_mon']+1, $datearr['tm_mday'], $datearr['tm_year']+1900);
				
				return strftime($this->outputFormat, $tstamp);
			}
		}
		
		throw new ValidationError("Please ensure the value is a valid date time.");
	}
}

class DateField extends Field {
    protected $format;
    
    public function __construct($required = true, $format = "Y-m-d") {
        parent::__construct($required);
        
        $this->format = $format;    
    }
    
    public function clean($value) {
        $value = parent::clean($value);
        
        $dt = DateTime::createFromFormat($this->format, $value);
        
        if (!($dt))
            throw new ValidationError("Please ensure the value is a valid date.");
        
        return $dt;   
    }
}

class TimeField extends Field {
        protected $format;
    
    public function __construct($required = true, $format = "H:i") {
        parent::__construct($required);
        
        $this->format = $format;    
    }
    
    public function clean($value) {
        $value = parent::clean($value);
        $dt = DateTime::createFromFormat($this->format, $value);
        
        if (!($dt))
            throw new ValidationError("Please ensure the value is a valid time.");
        
        return $dt;   
    }
}

class BooleanField extends Field {
	public function clean($value) {
		$value = parent::clean($value);
		
		if (empty($value))
			return $value;
			
		return ($value == 'true');
	}
}
?>