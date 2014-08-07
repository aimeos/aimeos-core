<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS controller interface.
 *
 * @package Controller
 * @subpackage ExtJS
 */
interface Controller_ExtJS_Common_Load_Text_Interface
{
	/**
	 * Initializes the controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 * @return void
	 */
	public function __construct( MShop_Context_Item_Interface $context );

	/**
	 * Returns the service description of the class.
	 * It describes the class methods and its parameters including their types
	 *
	 * @return array Associative list of class/method names, their parameters and types
	 */
	public function getServiceDescription();

	/**
	 * Returns the schema of the item.
	 *
	 * @return array Associative list of "name" and "properties" list (including "description", "type" and "optional")
	 */
	public function getItemSchema();

	/**
	 * Returns the schema of the available search criteria and operators.
	 *
	 * @return array Associative list of "criteria" list (including "description", "type" and "optional") and "operators" list (including "compare", "combine" and "sort")
	 */
	public function getSearchSchema();
}
