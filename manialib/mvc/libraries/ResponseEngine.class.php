<?php
/**
 * @author Maxime Raoust
 * @copyright 2009-2010 NADEO 
 * @package ManiaLib_MVC
 */

/**
 * Response engine
 * 
 * Allows to pass values between filters, controllers and views
 * 
 * @package ManiaLib_MVC
 */
class ResponseEngine
{
	private static $instance;

	private $vars = array();
	private $body = '';
	private $views = array();
	private $dialog;

	/**
	 * Gets the instance
	 * @return ResponseEngine
	 */
	public static function getInstance()
	{
		if (!self::$instance)
		{
			$class = __CLASS__;
			self::$instance = new $class;
		}
		return self::$instance;
	}

	private function __construct() {}

	public function __set($name, $value)
	{
		$this->vars[$name] = $value;
	}
	
	public function __get($name)
	{
		if(array_key_exists($name, $this->vars))
		{
			return $this->vars[$name];
		}
		else
		{
			return null;
		}
	}
	
	public function get($name, $default=null)
	{
		if(array_key_exists($name, $this->vars))
		{
			return $this->vars[$name];
		}
		else
		{
			return $default;
		}
	}
	
	public function registerDialog($controllerName, $actionName)
	{
		if($this->dialog)
		{
			throw new DialogAlreadyRegistered;
		}
		$this->dialog = array($controllerName, $actionName);
	}
	
	public function registerView($controllerName, $actionName)
	{
		$this->views[] = array($controllerName, $actionName);
	}
	
	public function resetViews()
	{
		$this->views = array();
	}
	
	public function appendBody($content)
	{
		$this->body .= $content;
	}
	
	public function render()
	{
		View::render('header');
		if($this->dialog)
		{
			View::render($this->dialog[0], $this->dialog[1]);
			Manialink::disableLinks();
		}
		foreach($this->views as $view)
		{
			View::render($view[0], $view[1]);
		}
		View::render('footer');
		
		header('Content-Type: text/xml; charset=utf-8');
		echo $this->body;
	}
}

/**
 * @package ManiaLib_MVC
 */
class ResponseEngineException extends MVCException {}

/**
 * @package ManiaLib_MVC
 */
class DialogAlreadyRegistered extends ResponseEngineException {}

?>