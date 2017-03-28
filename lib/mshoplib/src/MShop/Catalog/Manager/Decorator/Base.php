<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Catalog
 */


namespace Aimeos\MShop\Catalog\Manager\Decorator;


/**
 * Provides common methods for catalog manager decorators.
 *
 * @package MShop
 * @subpackage Catalog
 */
abstract class Base
	extends \Aimeos\MShop\Common\Manager\Decorator\Base
	implements \Aimeos\MShop\Common\Manager\Decorator\Iface, \Aimeos\MShop\Catalog\Manager\Iface
{
	/**
	 * Returns a list of items starting with the given category that are in the path to the root node
	 *
	 * @param string $id ID of item to get the path for
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @return array Associative list of items implementing \Aimeos\MShop\Catalog\Item\Iface with IDs as keys
	 */
	public function getPath( $id, array $ref = [] )
	{
		return $this->getManager()->getPath( $id, $ref );
	}


	/**
	 * Returns a node and its descendants depending on the given resource.
	 *
	 * @param string|null $id Retrieve nodes starting from the given ID
	 * @param string[] List of domains (e.g. text, media, etc.) whose referenced items should be attached to the objects
	 * @param integer $level One of the level constants from \Aimeos\MW\Tree\Manager\Base
	 * @param \Aimeos\MW\Criteria\Iface|null $criteria Optional criteria object with conditions
	 * @return \Aimeos\MW\Tree\Node\Iface Node, maybe with subnodes
	 */
	public function getTree( $id = null, array $ref = [], $level = \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE, \Aimeos\MW\Criteria\Iface $criteria = null )
	{
		return $this->getManager()->getTree( $id, $ref, $level, $criteria );
	}


	/**
	 * Adds a new item object.
	 *
	 * @param \Aimeos\MShop\Catalog\Item\Iface $item Item which should be inserted
	 * @param string|null $parentId ID of the parent item where the item should be inserted into
	 * @param string|null $refId ID of the item where the item should be inserted before (null to append)
	 */
	public function insertItem( \Aimeos\MShop\Catalog\Item\Iface $item, $parentId = null, $refId = null )
	{
		$this->getManager()->insertItem( $item, $parentId, $refId );
	}


	/**
	 * Moves an existing item to the new parent in the storage.
	 *
	 * @param string $id ID of the item that should be moved
	 * @param string $oldParentId ID of the old parent item which currently contains the item that should be removed
	 * @param string $newParentId ID of the new parent item where the item should be moved to
	 * @param string|null $refId ID of the item where the item should be inserted before (null to append)
	 */
	public function moveItem( $id, $oldParentId, $newParentId, $refId = null )
	{
		$this->getManager()->moveItem( $id, $oldParentId, $newParentId, $refId );
	}
}
