<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
 */


/**
 * Common interface for items containing referenced list items.
 *
 * @package MShop
 * @subpackage Common
 */
interface MShop_Common_Item_ListRef_Interface
	extends MShop_Common_Item_Interface
{
	/**
	 * Returns the list items attached, optionally filtered by domain and list type.
	 * The reference parameter in searchItems() must have been set accordingly
	 * to the requested domain to get the items. Otherwise, no items will be
	 * returned by this method.
	 *
	 * @param string $domain Name of the domain (e.g. product, text, etc.)
	 * @param array|string|null $type Name/Names of the list item type or null for all
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
	 * @param array|string|null $type Name/Names of the item type or null for all
	 * @param array|string|null $listtype Name/Names of the list item type or null for all
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
