<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\ListsRef;


/**
 * Interface for all manager implementations using lists items
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
{
	/**
	 * Creates a new lists item object
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface New lists item object
	 */
	public function createListItem( array $values = [] ) : \Aimeos\MShop\Common\Item\Lists\Iface;
}
