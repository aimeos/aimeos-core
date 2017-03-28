<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	extends \Aimeos\MShop\Common\Manager\Factory\Iface, \Aimeos\MShop\Common\Manager\Find\Iface
{
	/**
	 * Returns a list of item IDs, that are in the path of given item ID.
	 *
	 * @param integer $id ID of item to get the path for
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface[] Associative list of items implementing \Aimeos\MShop\Locale\Item\Site\Iface with IDs as keys
	 */
	public function getPath( $id, array $ref = [] );


	/**
	 * Returns a node and its descendants depending on the given resource.
	 *
	 * @param integer|null $id Retrieve nodes starting from the given ID
	 * @param array List of domains (e.g. text, media, etc.) whose referenced items should be attached to the objects
	 * @param integer $level One of the level constants from \Aimeos\MW\Tree\Manager\Base
	 * @return \Aimeos\MW\Tree\Node\Iface Node, maybe with subnodes
	 */
	public function getTree( $id = null, array $ref = [], $level = \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );


	/**
	 * Adds a new item object.
	 *
	 * @param \Aimeos\MShop\Locale\Item\Site\Iface $item Item which should be inserted
	 * @param integer|null $parentId ID of the parent item where the item should be inserted into
	 * @param integer|null $refId ID of the item where the item should be inserted before (null to append)
	 * @return null
	 */
	public function insertItem( \Aimeos\MShop\Locale\Item\Site\Iface $item, $parentId = null, $refId = null );


	/**
	 * Moves an existing item to the new parent in the storage.
	 *
	 * @param integer $id ID of the item that should be moved
	 * @param integer $oldParentId ID of the old parent item which currently contains the item that should be removed
	 * @param integer $newParentId ID of the new parent item where the item should be moved to
	 * @param integer|null $refId ID of the item where the item should be inserted before (null to append)
	 * @return null
	 */
	public function moveItem( $id, $oldParentId, $newParentId, $refId = null );
}
