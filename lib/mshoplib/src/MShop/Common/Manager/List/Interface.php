<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
 * @version $Id $
 */


/**
 * Default list manager implementation
 * @package MShop
 * @subpackage Common
 */
interface MShop_Common_Manager_List_Interface
	extends MShop_Common_Manager_Interface
{
	/**
	 * Creates the common list manager using the given context object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 * @param array $config array with SQL statements
	 * @param array $searchConfig array with search configuration
	 * @param MShop_Common_Manager_Type_Interface Common type manager
	 *
	 * @throws MShop_Common_Exception if no configuration is available
	 */
	public function __construct( MShop_Context_Item_Interface $context,
		array $config = array(), array $searchConfig = array(), $typeManager = null );


	/**
	 * Creates new common list item object.
	 *
	 * @param integer $id Id of the item which should be moved
	 * @param integer|null $ref Id where the given Id should be inserted before (null for appending)
	 */
	public function moveItem( $id, $ref = null );

}
