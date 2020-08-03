<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
 * @package MShop
 * @subpackage Locale
 */


namespace Aimeos\MShop\Locale\Manager\Currency;


/**
 * Interface for Locale currency manager.
 *
 * @package MShop
 * @subpackage Locale
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface
{
	/**
	 * Saves a currency item to the storage.
	 *
	 * @param \Aimeos\MShop\Locale\Item\Currency\Iface $item Currency item to save in the storage
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Locale\Item\Currency\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Locale\Item\Currency\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Locale\Item\Currency\Iface;
}
