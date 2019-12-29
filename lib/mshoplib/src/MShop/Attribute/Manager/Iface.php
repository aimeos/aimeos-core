<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Attribute
 */


namespace Aimeos\MShop\Attribute\Manager;


/**
 * Interface for attribute manager.
 *
 * @package MShop
 * @subpackage Attribute
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface, \Aimeos\MShop\Common\Manager\Find\Iface,
		\Aimeos\MShop\Common\Manager\ListRef\Iface, \Aimeos\MShop\Common\Manager\PropertyRef\Iface
{
	/**
	 * Saves an attribute item to the storage.
	 *
	 * @param \Aimeos\MShop\Attribute\Item\Iface $item Attribute item
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Attribute\Item\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Attribute\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Attribute\Item\Iface;
}
