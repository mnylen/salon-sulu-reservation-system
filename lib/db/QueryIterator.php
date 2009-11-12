<?php
require_once 'lib/db/Database.php';

/**
 * Iterator for the results of SQL queries.
 */
class QueryIterator implements Iterator {
	/**
	 * The name of the class the results should be fetched as.
	 *
	 * @var string
	 */
	private $classname;
	
	/**
	 * The <code>PDOStatement</code> to use.
	 *
	 * @var PDOStatement
	 */
	private $stmt;
	
	/**
	 * The data fetched to the iterator.
	 *
	 * @var array
	 */
	private $array = array();
	
	/**
	 * The current position of the iterator.
	 *
	 * @var int
	 */
	private $position = -1;
	
	/**
	 * Number of items to fetch when new items need to be fetched.
	 *
	 * @var int
	 */
	private $batchSize = 10;
	
	/**
	 * A flag indicating whether the statement has been executed or not.
	 *
	 * @var bool
	 */
	private $executed = false;
	
	/**
	 * Creates a new <code>QueryIterator</code> for the specified statement.
	 * 
	 * The statement should <strong>not</strong> be executed. <code>QueryIterator</code>
	 * itself does execute it before it needs to fetch any data.
	 *
	 * @param string $classname name of the class the results are fetched as
	 * @param PDOStatement $stmt the <code>PDOStatement</code> for fetching the results
	 */
	public function __construct($classname, PDOStatement $stmt) {
		if (!(class_exists($classname)))
			throw new InvalidArgumentException("Class '$classname' doesn't exist.");
		
		$this->classname = $classname;
		$this->stmt = $stmt;
		
		// Make the default fetch mode to be PDO::FETCH_CLASS -> $classname
		$this->stmt->setFetchMode(PDO::FETCH_CLASS, $classname);
	}
	
	/**
	 * Gets the current element in the iterator.
	 *
	 * @return mixed the current element in the iterator, which is an object instantiated from
	 *               the previously specified class; <code>null</code> if there's no current
	 * 				 element
	 */
	public function current() {
		if ($this->position == -1)
			$this->next();
		
		return $this->array[$this->position];
	}
	
	/**
	 * Gets the next element in the iterator.
	 *
	 * @return mixed the next element in the iterator, which is an object instantiated from
	 *               the previously specified class; <code>null</code> if there's no next
	 * 				 element
	 */
	public function next() {
		++$this->position;
		
		if ($this->position >= count($this->array))
			$this->fetchMore();
			
		if (!($this->valid()))
			return null;
		
		return $this->array[$this->position];
	}
	
	public function first() {
		$this->rewind();
		$elem = $this->next();
		$this->rewind();
		
		return $elem;
	}
    
    public function count() {
        $this->rewind();
        $count = 0;
        
        while ($this->next() !== null)
            $count++;
        
        $this->rewind();
        
        return $count;
    }
	
	/**
	 * Gets an element from the QueryIterator that has the specified
	 * property set to the given value.
	 *
	 * @param	string	$propertyName	name of the property
	 * @param	mixed 	$value			desired value for the property
	 * @return	mixed	first matching element or null if not found
	 */
	public function get($propertyName, $value) {
		$this->rewind();
				
		while ( ($elem = $this->next()) !== null) {
			if ($elem->{$propertyName} == $value) {
				$found = $elem;
				break;
			}
		}
		
		$this->rewind();
		return $found;
	}
	
	/**
	 * Determines whether the current iterator position is valid.
	 *
	 * @return bool <code>true</code> if the position is valid;
	 *              otherwise <code>false</code> is returned
	 */
	public function valid() {
		return isset($this->array[$this->position]);
	}
	
	/**
	 * Rewinds the iterator to the first element.
	 */
	public function rewind() {
		$this->position = -1;
	}
	
	/**
	 * Gets the key of the current item in iterator.
	 *
	 * @return int the key of the current item in iterator
	 */
	public function key() {
		return $this->position;
	}
	
	/**
	 * Fetches more rows from the result.
	 */
	private function fetchMore() {
		if (!($this->executed)) {
			$this->stmt->execute();
			$this->executed = true;
		}
		
		for ($i = 0; $i < $this->batchSize; $i++) {
			$obj = $this->stmt->fetch(PDO::FETCH_CLASS);

			if (!($obj))
				break;
				
			array_push($this->array, $obj);
		}
	}
	
	/**
	 * Updates the iterator to match any changes made to the database. It also
	 * rewinds to the first item.
	 *
	 */
	public function update() {
		$this->stmt->execute();
		$this->executed = true;
		
		$this->array = array();
		$this->position = -1;
		
	}
	
	/**
	 * Flattens the records in the query iterator as an array containing only the specified
	 * property name.
	 *
	 * @param unknown_type $propertyName
	 */
	public function flatten($propertyName) {
		$arr = array();
		$this->rewind();
		
		while ( ($obj = $this->next()) !== null )
			$arr[] = $obj->{$propertyName};
			
		$this->rewind();
		return $arr;
	}
	
	/**
	 * Creates a choices array to be used with ChoiceField from the
	 * elements in the QueryIterator. The choice value will be the
	 * value of the specified property name. The value will be the
	 * actual element.
	 *
	 * @param	string	$keyPropertyName	name of the property to use as choice value
	 * @return	array	array of choices
	 */
	public function choices($keyPropertyName) {
		$this->rewind();
		
		$choices = array();
		while ( ($elem = $this->next()) !== null )
			$choices[$elem->{$keyPropertyName}] = $elem;
		
		$this->rewind();
		return $choices;
	}
	
	/**
	 * Creates a new <code>QueryIterator</code> for the given SQL query, parameters
	 * and class name.
	 * 
	 * @param string $classname name of the class the results are fetched as
	 * @param string $sql the SQL query
	 * @param array $params array <code>name =&gt; value</code> pairs
	 * 
	 * @return QueryIterator the created query iterator
	 */
	public static function createIterator($classname, $sql, $params = array()) {
		$stmt = Database::getPDOObject()->prepare($sql);
		
		foreach ($params as $name => $value)
			$stmt->bindValue($name, $value);
		
		$stmt->execute();
		return new QueryIterator($classname, $stmt, true);
	}
}
?>