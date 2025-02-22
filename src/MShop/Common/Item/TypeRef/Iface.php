<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2025
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\TypeRef;


/**
 * Common interface for items having types.
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
{
	/**
	 * Returns the type item of the item if available.
	 *
	 * @return \Aimeos\MShop\Type\Item\Iface|null Type item or NULL if not available
	 */
	public function getTypeItem() : ?\Aimeos\MShop\Type\Item\Iface;

	/**
	 * Returns the type of the item.
	 *
	 * @return string Type of the item
	 */
	public function getType() : string;

	/**
	 * Sets the new type of the item.
	 *
	 * @param string $type Type of the item
	 * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
	 */
	public function setType( string $type ) : \Aimeos\MShop\Common\Item\Iface;
}
