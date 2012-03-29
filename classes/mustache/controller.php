<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Mustache base controller.
 *
 * @package    Mustache
 * @category   Controllers
 * @author     Ronni Egeriis Persson
 * @copyright  (c) 2011 Ronni Egeriis Persson
 * @license    MIT
 */
abstract class Mustache_Controller extends Controller_Template
{
	/**
	 * @var  Mustache_View  Holds the current view
	 */
	public $view;
	protected $data_cache = 'no-cache';
	
	
	/**
	 * Check if the HTTP request is made through AJAX. If that's the case, it will 
	 * respond with the view's data as JSON instead of the view/template markup.
	 *
	 * See: http://kohanaframework.org/3.2/guide/api/Controller_Template#after
	 *
	 * @return  void
	 */
	public function after()
	{
		if ($this->is_ajax()) $this->expose_view();
		parent::after();
	}
	
  protected function expose_data() {
    return $this->view->expose_data();
  }
  
	/**
	 * Method used to set JSON Content-Type header and respond with JSON data.
	 * This method calls exit, thus no other methods are called after this.
	 *
	 * @return void
	 */
	protected function expose_view()
	{
		$out = json_encode($this->expose_data());
		$contentLength = function_exists('mb_strlen') ? mb_strlen($out, '8bit') : strlen($out);
		
		$this->response->headers('Cache-control', $this->data_cache);
		$this->response->headers('Content-Type', 'application/json');
		$this->response->send_headers();
		
		$this->response->body($out);
		echo $this->response->body();
		exit();
	}
	
	/**
	 * Checks if current HTTP request is made through AJAX.
	 *
	 * @return boolean
	 */
	protected function is_ajax()
	{
		if (empty($_SERVER['HTTP_X_REQUESTED_WITH'])) return false;
		return ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
	}
}