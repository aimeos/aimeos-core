<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * Provides common methods for controller decorators.
 *
 * @package Controller
 * @subpackage ExtJS
 */
abstract class Controller_ExtJS_Common_Decorator_Abstract
	implements Controller_ExtJS_Common_Decorator_Interface
{
	private $_context = null;
	private $_controller = null;


	/**
	 * Initializes the controller decorator.
	 *
	 * @param MShop_Context_Interface $context Context object with required objects
	 * @param Controller_ExtJS_Interface $controller Controller object
	 */
	public function __construct( MShop_Context_Item_Interface $context, Controller_ExtJS_Interface $controller )
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
	 * @throws Controller_ExtJS_Exception If method call failed
	 */
	public function __call( $name, array $param )
	{
		if ( ( $result = call_user_func_array( array( $this->_controller, $name ), $param ) ) === false ) {
			throw new Controller_ExtJS_Exception( sprintf('Unable to call method "%1$s"', $name) );
		}

		return $result;
	}


	/**
	 * Deletes a list of an items.
	 *
	 * @param stdClass $params Associative array containing the required values
	 */
	public function deleteItems( stdClass $params )
	{
		$this->_controller->deleteItems( $params );
	}


	/**
	 * Creates a new item or updates an existing one or a list thereof.
	 *
	 * @param stdClass $params Associative array containing the required values
	 */
	public function saveItems( stdClass $params )
	{
		return $this->_controller->saveItems( $params );
	}


	/**
	 * Retrieves all items matching the given criteria.
	 *
	 * @param stdClass $params Associative array containing the parameters
	 * @return array List of associative arrays with item properties and total number of items
	 */
	public function searchItems( stdClass $params )
	{
		return $this->_controller->searchItems( $params );
	}


	/**
	 * Returns the service description of the class.
	 * It describes the class methods and its parameters including their types
	 *
	 * @return array Associative list of class/method names, their parameters and types
	 */
	public function getServiceDescription()
	{
		return $this->_controller->getServiceDescription();
	}


	/**
	 * Returns the schema of the item.
	 *
	 * @return array Associative list of "name" and "properties" list (including
	 * "description", "type" and "optional")
	 */
	public function getItemSchema()
	{
		return $this->_controller->getItemSchema();
	}


	/**
	 * Returns the schema of the available search criteria and operators.
	 *
	 * @return array Associative list of "criteria" list (including "description",
	 * "type" and "optional") and "operators" list (including "compare", "combine" and "sort")
	 */
	public function getSearchSchema()
	{
		return $this->_controller->getSearchSchema();
	}

}