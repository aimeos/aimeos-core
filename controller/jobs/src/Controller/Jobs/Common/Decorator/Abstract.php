<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Provides common methods for controller decorators.
 *
 * @package Controller
 * @subpackage Jobs
 */
abstract class Controller_Jobs_Common_Decorator_Abstract
	implements Controller_Jobs_Common_Decorator_Interface
{
	private $_context;
	private $_arcavias;
	private $_controller;


	/**
	 * Initializes the controller decorator.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 * @param Controller_Jobs_Interface $controller Controller object
	 */
	public function __construct( MShop_Context_Item_Interface $context, Arcavias $arcavias,
		Controller_Jobs_Interface $controller )
	{
		$this->_context = $context;
		$this->_arcavias = $arcavias;
		$this->_controller = $controller;
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
		if ( ( $result = call_user_func_array( array( $this->_controller, $name ), $param ) ) === false ) {
			throw new Controller_Jobs_Exception( sprintf('Unable to call method "%1$s"', $name) );
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
		return $this->_controller->getName();
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->_controller->getDescription();
	}


	/**
	 * Executes the job.
	 *
	 * @throws Controller_Jobs_Exception If an error occurs
	 */
	public function run()
	{
		$this->_controller->run();
	}


	/**
	 * Returns the context object.
	 *
	 * @return MShop_Context_Item_Interface context object implementing MShop_Context_Item_Interface
	 */
	protected function _getContext()
	{
		return $this->_context;
	}


	/**
	 * Returns the Arcavias object.
	 *
	 * @return Arcavias Arcavias object
	 */
	protected function _getArcavias()
	{
		return $this->_arcavias;
	}
}