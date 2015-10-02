<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Frontend
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
	private $context = null;
	private $controller = null;


	/**
	 * Initializes the controller decorator.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 * @param Controller_Frontend_Interface $controller Controller object
	 */
	public function __construct( MShop_Context_Item_Interface $context, Controller_Frontend_Interface $controller )
	{
		$this->context = $context;
		$this->controller = $controller;
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
		if( ( $result = call_user_func_array( array( $this->controller, $name ), $param ) ) === false )
		{
			$cntl = get_class( $this->controller );
			throw new Controller_Frontend_Exception( sprintf( 'Unable to call method "%1$s::%2$s"', $cntl, $name ) );
		}

		return $result;
	}


	/**
	 * Returns the context item
	 *
	 * @return MShop_Context_Item_Interface Context item object
	 */
	protected function getContext()
	{
		return $this->context;
	}


	/**
	 * Returns the frontend controller
	 *
	 * @return Controller_Frontend_Common_Interface Frontend controller object
	 */
	protected function getController()
	{
		return $this->controller;
	}
}
