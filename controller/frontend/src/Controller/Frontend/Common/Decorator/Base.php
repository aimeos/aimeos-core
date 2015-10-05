<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Frontend
 */


namespace Aimeos\Controller\Frontend\Common\Decorator;


/**
 * Provides common methods for controller decorators.
 *
 * @package Controller
 * @subpackage Frontend
 */
abstract class Base
	implements \Aimeos\Controller\Frontend\Common\Decorator\Iface
{
	private $context = null;
	private $controller = null;


	/**
	 * Initializes the controller decorator.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @param \Aimeos\Controller\Frontend\Iface $controller Controller object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\Controller\Frontend\Iface $controller )
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
	 * @throws \Aimeos\Controller\Frontend\Exception If method call failed
	 */
	public function __call( $name, array $param )
	{
		if( ( $result = call_user_func_array( array( $this->controller, $name ), $param ) ) === false )
		{
			$cntl = get_class( $this->controller );
			throw new \Aimeos\Controller\Frontend\Exception( sprintf( 'Unable to call method "%1$s::%2$s"', $cntl, $name ) );
		}

		return $result;
	}


	/**
	 * Returns the context item
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface Context item object
	 */
	protected function getContext()
	{
		return $this->context;
	}


	/**
	 * Returns the frontend controller
	 *
	 * @return \Aimeos\Controller\Frontend\Common\Iface Frontend controller object
	 */
	protected function getController()
	{
		return $this->controller;
	}
}
