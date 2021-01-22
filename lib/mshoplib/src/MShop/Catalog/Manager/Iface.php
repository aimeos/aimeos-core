<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Catalog
 */


namespace Aimeos\MShop\Catalog\Manager;


/**
 * Shop catalog interface with methods for managing categories, groups and products.
 * @package MShop
 * @subpackage Catalog
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface, \Aimeos\MShop\Common\Manager\Find\Iface, \Aimeos\MShop\Common\Manager\ListsRef\Iface
{
	/**
	 * Returns a list of item IDs, that are in the path of given item ID.
	 *
	 * @param string $id ID of item to get the path for
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @return \Aimeos\Map Associative list of catalog items implementing \Aimeos\MShop\Catalog\Item\Iface with IDs as keys
	 */
	public function getPath( string $id, array $ref = [] ) : \Aimeos\Map;


	/**
	 * Returns a node and its descendants depending on the given resource.
	 *
	 * @param string|null $id Retrieve nodes starting from the given ID
	 * @param string[] List of domains (e.g. text, media, etc.) whose referenced items should be attached to the objects
	 * @param int $level One of the level constants from \Aimeos\MW\Tree\Manager\Base
	 * @param \Aimeos\MW\Criteria\Iface|null $criteria Optional criteria object with conditions
	 * @return \Aimeos\MShop\Catalog\Item\Iface Catalog item, maybe with subnodes
	 */
	public function getTree( string $id = null, array $ref = [],
		int $level = \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE, \Aimeos\MW\Criteria\Iface $criteria = null );


	/**
	 * Adds a new item object.
	 *
	 * @param \Aimeos\MShop\Catalog\Item\Iface $item Item which should be inserted
	 * @param string|null $parentId ID of the parent item where the item should be inserted into
	 * @param string|null $refId ID of the item where the item should be inserted before (null to append)
	 * @return \Aimeos\MShop\Catalog\Item\Iface Inserted catalog item
	 */
	public function insert( \Aimeos\MShop\Catalog\Item\Iface $item, string $parentId = null,
		string $refId = null ) : \Aimeos\MShop\Catalog\Item\Iface;


	/**
	 * Moves an existing item to the new parent in the storage.
	 *
	 * @param string $id ID of the item that should be moved
	 * @param string|null $oldParentId ID of the old parent item which currently contains the item that should be removed
	 * @param string|null $newParentId ID of the new parent item where the item should be moved to
	 * @param string|null $refId ID of the item where the item should be inserted before (null to append)
	 * @return \Aimeos\MShop\Catalog\Manager\Iface Manager object for chaining method calls
	 */
	public function move( string $id, string $oldParentId = null, string $newParentId = null,
		string $refId = null ) : \Aimeos\MShop\Catalog\Manager\Iface;
}
