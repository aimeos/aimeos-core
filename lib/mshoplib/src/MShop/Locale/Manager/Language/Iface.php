<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Locale
 */


namespace Aimeos\MShop\Locale\Manager\Language;


/**
 * Interface for locale language manager.
 *
 * @package MShop
 * @subpackage Locale
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface
{
	/**
	 * Saves the language object to the storage.
	 *
	 * @param \Aimeos\MShop\Locale\Item\Language\Iface $item Language object
	 * @param boolean $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Locale\Item\Language\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Locale\Item\Language\Iface $item, $fetch = true );
}
