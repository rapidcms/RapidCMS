<?php
/**
 * This class stores the filters. it has properties to store the name of the 
 * filter as well the functions that should run whent he filters are stored.
 * the filters property is an array. The key is the name of the
 * function and value is the arguments. Currently we do not make any use of the
 * arguments.
 * 
 * @param string $name name of the filter
 * @return object
 */
class filter
{
	// This is the name of the filter
	public $name;
	
	// array holding a list of functions $this->functions["name"]
	public $functions;
	
	public function __construct($name)
	{
		$this->name = $name;
	}
	
	public function add($function, $args)
	{
		$this->functions[$function] = $args;
	}
}

/**
 * This class is the main hook class. You don't create an object, but instead
 * call it using class::method.
 * 
 * @param type name description
 * @return object
 */

class hooks
{
	// contains instances of filter
	public $filters;
	public $directory;
	
	public function __construct()
	{
		$this->directory = $_SERVER['DOCUMENT_ROOT'] . RAPID_DIR . "/modules/";
		$this->load();
	}
	
	public function load()
	{
		$files = glob($this->directory . "*.php");
		
		foreach($files as $file)
		{  
			require_once($file);  
		} 
	}
	// $name is the filter name
	// $function is the functions to add to $name
	// $arg is number of arguments
	public function apply_filter($name, $function, $args='0') 
	{
		// if there are no functions ($name doesn't exist yet then make one
		if (count($this->filters[$name]->functions) < 1)
		{
			$this->filters[$name] = new filter ($name);
		}
		// add the function to the filter
		$this->filters[$name]->add($function, $args);
	
	}
	
	public function add_filter($name, $value='')
	{
		if (is_array($this->filters[$name]->functions))
		{
			foreach ($this->filters[$name]->functions as $function => $args)
			{
				$value = call_user_func($function, $value);
			}
		}
		return $value;
	}
	
	public function do_action($name, $function) 
	{
		$this->apply_filter($name, $function);
	}
	
	public function add_action($name)
	{
		$this->add_filter($name);
	}
}
?>