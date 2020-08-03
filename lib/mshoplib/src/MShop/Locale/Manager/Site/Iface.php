<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
 * @package MShop
 * @subpackage Locale
 */


namespace Aimeos\MShop\Locale\Manager\Site;


/**
 * Interface for Locale site manager.
 *
 * @package MShop
 * @subpackage Locale
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface, \Aimeos\MShop\Common\Manager\Find\Iface
{
	/**
	 * Returns a list of item IDs, that are in the path of given item ID.
	 *
	 * @param string $id ID of item to get the path for
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @return \Aimeos\Map List of IDs as keys and items implementing \Aimeos\MShop\Locale\Item\Site\Iface
	 */
	public function getPath( string $id, array $ref = [] ) : \Aimeos\Map;

	/**
	 * Returns a node and its descendants depending on the given resource.
	 *
	 * @param string|null $id Retrieve nodes starting from the given ID
	 * @param string[] $ref List of domains (e.g. text, media, etc.) whose referenced items should be attached to the objects
	 * @param int $level One of the level constants from \Aimeos\MW\Tree\Manager\Base
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Site node, maybe with subnodes
	 */
	public function getTree( string $id = null, array $ref = [],
		int $level = \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE ) : \Aimeos\MShop\Locale\Item\Site\Iface;

	/**
	 * Adds a new item object.
	 *
	 * @param \Aimeos\MShop\Locale\Item\Site\Iface $item Item which should be inserted
	 * @param string|null $parentId ID of the parent item where the item should be inserted into
	 * @param string|null $refId ID of the item where the item should be inserted before (null to append)
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface $item Updated item including the generated ID
	 */
	public function insertItem( \Aimeos\MShop\Locale\Item\Site\Iface $item, string $parentId = null,
		string $refId = null ) : \Aimeos\MShop\Locale\Item\Site\Iface;

	/**
	 * Moves an existing item to the new parent in the storage.
	 *
	 * @param string $id ID of the item that should be moved
	 * @param string|null $oldParentId ID of the old parent item which currently contains the item that should be removed
	 * @param string|null $newParentId ID of the new parent item where the item should be moved to
	 * @param string|null $refId ID of the item where the item should be inserted before (null to append)
	 * @return \Aimeos\MShop\Locale\Manager\Site\Iface Manager object for chaining method calls
	 */
	public function moveItem( string $id, string $oldParentId = null, string $newParentId = null,
		string $refId = null ) : \Aimeos\MShop\Locale\Manager\Site\Iface;

	/**
	 * Adds a new site to the storage or updates an existing one.
	 *
	 * @param \Aimeos\MShop\Locale\Item\Site\Iface $item New site item for saving to the storage
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Locale\Item\Site\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Locale\Item\Site\Iface;
}
