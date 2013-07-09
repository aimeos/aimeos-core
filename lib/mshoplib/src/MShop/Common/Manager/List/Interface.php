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
	 * Creates new common list item object.
	 *
	 * @param integer $id Id of the item which should be moved
	 * @param integer|null $ref Id where the given Id should be inserted before (null for appending)
	 */
	public function moveItem( $id, $ref = null );

}
