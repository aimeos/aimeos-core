<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\Type;


/**
 * Interface for all type manager implementations.
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface, \Aimeos\MShop\Common\Manager\Find\Iface
{
	/**
	 * Adds or updates a type item object.
	 *
	 * @param \Aimeos\MShop\Common\Item\Type\Iface $item Type item object which should be saved
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\Type\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Type\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Common\Item\Type\Iface;
}
