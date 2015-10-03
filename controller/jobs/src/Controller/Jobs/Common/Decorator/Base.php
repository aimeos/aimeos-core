<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Provides common methods for controller decorators.
 *
 * @package Controller
 * @subpackage Jobs
 */
abstract class Controller_Jobs_Common_Decorator_Base
	implements Controller_Jobs_Common_Decorator_Iface
{
	private $context;
	private $aimeos;
	private $controller;


	/**
	 * Initializes the controller decorator.
	 *
	 * @param MShop_Context_Item_Iface $context Context object with required objects
	 * @param Controller_Jobs_Iface $controller Controller object
	 */
	public function __construct( MShop_Context_Item_Iface $context, Aimeos $aimeos,
		Controller_Jobs_Iface $controller )
	{
		$this->context = $context;
		$this->aimeos = $aimeos;
		$this->controller = $controller;
	}


	/**
	 * Passes unknown methods to wrapped objects.
	 *
	 * @param string $name Name of the method
	 * @param array $param List of method parameter
	 * @return mixed Returns the value of the called method
	 * @throws Controller_Jobs_Exception If method call failed
	 */
	public function __call( $name, array $param )
	{
		if( ( $result = call_user_func_array( array( $this->controller, $name ), $param ) ) === false ) {
			throw new Controller_Jobs_Exception( sprintf( 'Unable to call method "%1$s"', $name ) );
		}

		return $result;
	}


	/**
	 * Returns the localized name of the job.
	 *
	 * @return string Name of the job
	 */
	public function getName()
	{
		return $this->controller->getName();
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->controller->getDescription();
	}


	/**
	 * Executes the job.
	 *
	 * @throws Controller_Jobs_Exception If an error occurs
	 */
	public function run()
	{
		$this->controller->run();
	}


	/**
	 * Returns the context object.
	 *
	 * @return MShop_Context_Item_Iface context object implementing MShop_Context_Item_Iface
	 */
	protected function getContext()
	{
		return $this->context;
	}


	/**
	 * Returns the Aimeos object.
	 *
	 * @return Aimeos Aimeos object
	 */
	protected function getAimeos()
	{
		return $this->aimeos;
	}
}