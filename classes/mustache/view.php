<?php defined('SYSPATH') or die('No direct script access.');

class Mustache_View extends Kohana_View
{
	protected static $templates;
	protected $_name;

	public function __construct($name, array $data = array())
	{
		$this->_name = $name;
		parent::__construct($name, $data);
	}
	
	public function render($file = NULL)
	{
		if ($file !== NULL)
		{
			$this->set_filename($file);
		}

		if (empty($this->_file))
		{
			throw new View_Exception('You must set the file to use within your view before rendering');
		}


		$m = new Mustache;
		return $m->render(self::load_template($this->_file), $this, self::$templates);
	}
	
	public function expose_data()
	{
		return array('data' => $this->_data, 'view' => $this->_name);
	}
	
	public function getName() {
		return $this->_name;
	}

	
	/**
	 * @return Mustache_View 
	 */
	public static function factory($file = NULL, array $data = NULL)
	{
		return new self($file, $data);
	}
	
	/**
	 * Load a template and cache this.
	 * @param string name Name of template
	 * @return string Returns markup
	 * @throws Exception If no template was find with the given name
	 */
	protected static function load_template($file)
	{
		if (isset(self::$templates[$file])) return self::$templates[$file];
		
		$markup = @file_get_contents($file);
		if (empty($markup)) throw new Exception('No template found with path: ' . $file);
		
		self::save_template($file, $markup);
		self::find_partials($markup);
		
		return $markup;
	}
	
	/**
	 * @return  Mustache_View
	 * @throws  View_Exception
	 */
	public function set_filename($file)
	{
		$this->_name = str_replace('/','_', $file);
		if (($path = Kohana::find_file('views', $file, 'mustache')) === FALSE)
		{
			throw new View_Exception('The requested view :file could not be found', array(
				':file' => $file,
			));
		}

		// Store the file path locally
		$this->_file = $path;

		return $this;
	}
	
	/** 
	 * Cache the template (only cached within same page load)
	 * @param string name Name of template
	 * @param string markup Mustache markup
	 * @return void
	 */
	protected static function save_template($name, $markup)
	{
		if (!self::$templates) self::$templates = array();
		self::$templates[$name] = $markup;
	}
	
	/**
	 * Find partials in markup and auto-load
	 * @param string markup The markup to be parsed for partials
	 * @return void
	 */
	protected static function find_partials($markup)
	{
		if (preg_match_all('/{{\>([\s\S]*?)}}/', $markup, $matches, PREG_SET_ORDER) == false) return $markup;
	
		foreach ($matches as $match)
		{
			self::load_template($match[1]);
		}
	}
	
	public static function capture($kohana_view_filename, array $kohana_view_data) {
		throw new View_Exception('Cannot call ' . __METHOD__ . '.');
	}
}