<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MW
 * @subpackage View
 */


/**
 * Common abstract class for all view helper classes.
 *
 * @method string|array config(string $name = null, string|array $default = null) Returns the config value for the given key
 * @method string date(string $date) Returns the formatted date
 * @method MW_View_Helper_Interface encoder() Returns the encoder object
 * @method string formparam(string|array $names) Returns the name for the HTML form parameter
 * @method MW_Mail_Message_Interface mail() Returns the e-mail message object
 * @method string number(integer|float|decimal $number, integer $decimals = 2) Returns the formatted number
 * @method string|array param(string|null $name, string|array $default) Returns the parameter value
 * @method string translate(string $domain, string $singular, string $plural = '', integer $number = 1) Returns the translated string or the original one if no translation is available
 * @method string url(string|null $target, string|null $controller = null, string|null $action = null, array $params = array(), array $trailing = array(), array $config = array()) Returns the URL assembled from the given arguments
 *
 * @package MW
 * @subpackage View
 */
abstract class MW_View_Helper_Abstract
{
	private $_view;


	/**
	 * Initializes the view helper classes.
	 *
	 * @param MW_View_Interface $view View instance with registered view helpers
	 */
	public function __construct( MW_View_Interface $view )
	{
		$this->_view = $view;
	}


	/**
	 * Calls the view helper with the given name and arguments and returns it's output.
	 *
	 * @param string $name Name of the view helper
	 * @param array $args Arguments passed to the view helper
	 * @return mixed Output depending on the view helper
	 */
	public function __call( $name, array $args )
	{
		return call_user_func_array( array( $this->_view, $name ), $args );
	}


	/**
	 * Sets a new view object for changing views afterwards
	 *
	 * @param MW_View_Interface $view View object
	 */
	public function setView( MW_View_Interface $view )
	{
		$this->_view = $view;
	}


	/**
	 * Returns the view object.
	 *
	 * @return MW_View_Interface View object
	 */
	protected function _getView()
	{
		return $this->_view;
	}
}