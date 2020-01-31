<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
 * @package MShop
 * @subpackage Tag
 */


namespace Aimeos\MShop\Tag\Manager;


/**
 * Default tag manager implementation
 *
 * @package MShop
 * @subpackage Tag
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface
{
	/**
	 * Inserts the new tag items for tag item
	 *
	 * @param \Aimeos\MShop\Tag\Item\Iface $item Tag item which should be saved
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Tag\Item\Iface Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Tag\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Tag\Item\Iface;
}
