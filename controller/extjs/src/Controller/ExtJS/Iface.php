<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage ExtJS
 */


namespace Aimeos\Controller\ExtJS;


/**
 * ExtJS controller interface.
 *
 * @package Controller
 * @subpackage ExtJS
 */
interface Iface
{
	/**
	 * Executes tasks before processing the items.
	 *
	 * @param \stdClass $params Associative list of parameters
	 * @return array Associative list with success value
	 */
	public function init( \stdClass $params );

	/**
	 * Executes tasks after processing the items.
	 *
	 * @param \stdClass $params Associative list of parameters
	 * @return array Associative list with success value
	 */
	public function finish( \stdClass $params );

	/**
	 * Deletes a list of an items.
	 *
	 * @param \stdClass $params Associative array containing the required values
	 */
	public function deleteItems( \stdClass $params );

	/**
	 * Creates a new item or updates an existing one or a list thereof.
	 *
	 * @param \stdClass $params Associative array containing the required values
	 */
	public function saveItems( \stdClass $params );

	/**
	 * Retrieves all items matching the given criteria.
	 *
	 * @param \stdClass $params Associative array containing the parameters
	 * @return array List of associative arrays with item properties and total number of items
	 */
	public function searchItems( \stdClass $params );

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
