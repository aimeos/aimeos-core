<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\ListsRef;


/**
 * Common interface for items containing referenced list items.
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface extends \Aimeos\MShop\Common\Item\Iface
{
	/**
	 * Adds a new or overwrite an existing list item which references the given domain item (created if it doesn't exist)
	 *
	 * @param string $domain Name of the domain (e.g. media, text, etc.)
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface $listItem List item referencing the new domain item
	 * @param \Aimeos\MShop\Common\Item\Iface|null $refItem New item added to the given domain or null if no item should be referenced
	 * @return \Aimeos\MShop\Common\Item\ListsRef\Iface Self object for method chaining
	 */
	public function addListItem( string $domain, \Aimeos\MShop\Common\Item\Lists\Iface $listItem, \Aimeos\MShop\Common\Item\Iface $refItem = null ) : \Aimeos\MShop\Common\Item\ListsRef\Iface;

	/**
	 * Removes a list item which references the given domain item (removed as well if it exists)
	 *
	 * @param string $domain Name of the domain (e.g. media, text, etc.)
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface $listItem List item referencing the domain item
	 * @param \Aimeos\MShop\Common\Item\Iface|null $refItem Existing item removed from the given domain or null if item shouldn't be removed
	 * @return \Aimeos\MShop\Common\Item\ListsRef\Iface Self object for method chaining
	 */
	public function deleteListItem( string $domain, \Aimeos\MShop\Common\Item\Lists\Iface $listItem, \Aimeos\MShop\Common\Item\Iface $refItem = null ) : \Aimeos\MShop\Common\Item\ListsRef\Iface;

	/**
	 * Removes a list of list items which references their domain items (removed as well if it exists)
	 *
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface[] $items Existing list items
	 * @param bool $all True to delete referenced items as well, false for list items only
	 * @return \Aimeos\MShop\Common\Item\ListsRef\Iface Self object for method chaining
	 */
	public function deleteListItems( iterable $items, bool $all = false ) : \Aimeos\MShop\Common\Item\ListsRef\Iface;

	/**
	 * Returns the domains for which items are available
	 *
	 * @return string[] List of domain names
	 */
	public function getDomains() : array;

	/**
	 * Returns the deleted list items which include the domain items if available
	 *
	 * @param string|null $domain Domain name to get the deleted list items for
	 * @return \Aimeos\Map Associative list of domains as keys list items as values or list items only
	 */
	public function getListItemsDeleted( string $domain = null ) : \Aimeos\Map;

	/**
	 * Returns the list item for the given reference ID, domain and list type
	 *
	 * @param string $domain Name of the domain (e.g. product, text, etc.)
	 * @param string $listtype Name of the list item type
	 * @param string $refId Unique ID of the referenced item
	 * @param bool $active True to return only active items, false to return all
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface|null Matching list item or null if none
	 */
	public function getListItem( string $domain, string $listtype, string $refId, bool $active = true ) : ?\Aimeos\MShop\Common\Item\Lists\Iface;

	/**
	 * Returns the list items attached, optionally filtered by domain and list type.
	 * The reference parameter in search() must have been set accordingly
	 * to the requested domain to get the items. Otherwise, no items will be
	 * returned by this method.
	 *
	 * @param array|string|null $domain Name/Names of the domain (e.g. product, text, etc.) or null for all
	 * @param array|string|null $listtype Name/Names of the list item type or null for all
	 * @param array|string|null $type Name/Names of the item type or null for all
	 * @param bool $active True to return only active items, false to return all
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Common\Item\Lists\Iface
	 */
	public function getListItems( $domain = null, $listtype = null, $type = null, bool $active = true ) : \Aimeos\Map;

	/**
	 * Returns the product, text, etc. items, optionally filtered by type.
	 * The reference parameter in search() must have been set accordingly
	 * to the requested domain to get the items. Otherwise, no items will be
	 * returned by this method.
	 *
	 * @param array|string|null $domain Name/Names of the domain (e.g. product, text, etc.) or null for all
	 * @param array|string|null $type Name/Names of the item type or null for all
	 * @param array|string|null $listtype Name/Names of the list item type or null for all
	 * @param bool $active True to return only active items, false to return all
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Common\Item\Iface
	 */
	public function getRefItems( $domain = null, $type = null, $listtype = null, bool $active = true ) : \Aimeos\Map;

	/**
	 * Returns the localized text type of the item or the internal label if no name is available.
	 *
	 * @param string $type Text type to be returned
	 * @param string|null $langId Two letter ISO Language code of the text
	 * @return string Specified text type or label of the item
	 */
	public function getName( string $type = 'name', string $langId = null ) : string;
}
