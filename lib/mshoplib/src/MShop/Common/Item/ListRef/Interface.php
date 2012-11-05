<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
 * @version $Id: Interface.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Common interface for items containing referenced list items.
 *
 * @package MShop
 * @subpackage Common
 */
interface MShop_Common_Item_ListRef_Interface
{
	/**
	 * Returns the list items attached, optionally filtered by domain and list type.
	 * The reference parameter in searchItems() must have been set accordingly
	 * to the requested domain to get the items. Otherwise, no items will be
	 * returned by this method.
	 *
	 * @param string $domain Name of the domain (e.g. product, text, etc.)
	 * @param string|null $type Name of the list type
	 * @return array List of items implementing MShop_Common_Item_List_Interface
	 */
	public function getListItems( $domain, $type = null );

	/**
	 * Returns the product, text, etc. items, optionally filtered by type.
	 * The reference parameter in searchItems() must have been set accordingly
	 * to the requested domain to get the items. Otherwise, no items will be
	 * returned by this method.
	 *
	 * @param string $domain Name of the domain (e.g. product, text, etc.)
	 * @param string|null $type Name of the item type
	 * @param string|null $listtype Name of the list item type
	 * @return array List of items implementing MShop_Common_Item_Interface
	 */
	public function getRefItems( $domain, $type = null, $listtype = null );

	/**
	 * Returns the localized name of the item or the internal label if no name is available.
	 *
	 * @return string Name or label of the item
	 */
	public function getName();
}
