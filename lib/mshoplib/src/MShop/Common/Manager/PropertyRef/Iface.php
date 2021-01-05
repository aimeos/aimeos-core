<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\PropertyRef;


/**
 * Interface for all manager implementations using property items
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
{
	/**
	 * Creates a new property item object
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Common\Item\Property\Iface New property item object
	 */
	public function createPropertyItem( array $values = [] ) : \Aimeos\MShop\Common\Item\Property\Iface;
}
