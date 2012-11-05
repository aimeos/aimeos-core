<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Frontend
 * @version $Id: Abstract.php 866 2012-06-28 16:14:02Z fblasel $
 */


/**
 * Provides common methods for controller decorators.
 *
 * @package Controller
 * @subpackage Frontend
 */
abstract class Controller_Frontend_Common_Decorator_Abstract
	implements Controller_Frontend_Common_Decorator_Interface
{
	private $_context = null;
	private $_controller = null;


	/**
	 * Initializes the controller decorator.
	 *
	 * @param MShop_Context_Interface $context Context object with required objects
	 * @param Controller_Frontend_Interface $controller Controller object
	 */
	public function __construct( MShop_Context_Item_Interface $context, Controller_Frontend_Interface $controller )
	{
		$this->_context = $context;
		$this->_controller = $controller;
	}


	/**
	 * Passes unknown methods to wrapped objects.
	 *
	 * @param string $name Name of the method
	 * @param array $param List of method parameter
	 * @return mixed Returns the value of the called method
	 * @throws Controller_Frontend_Exception If method call failed
	 */
	public function __call( $name, array $param )
	{
		if ( ( $result = call_user_func_array( array( $this->_controller, $name ), $param ) ) === false )
		{
			$method = sprintf('%1$s::%2$s', get_class($this->_controller), $name );
			throw new Controller_Frontend_Exception( sprintf('Unable to call method "%1$s"', $method) );
		}

		return $result;
	}

}
