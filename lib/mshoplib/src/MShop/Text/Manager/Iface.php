<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
 * @package MShop
 * @subpackage Text
 */


namespace Aimeos\MShop\Text\Manager;


/**
 * Interface for all text manager classes.
 *
 * @package MShop
 * @subpackage Text
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface, \Aimeos\MShop\Common\Manager\ListRef\Iface
{
	/**
	 * Updates or adds a text item object.
	 * This method doesn't update the type string that belongs to the type ID
	 *
	 * @param \Aimeos\MShop\Text\Item\Iface $item Text item which should be saved
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Text\Item\Iface Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Text\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Text\Item\Iface;
}
