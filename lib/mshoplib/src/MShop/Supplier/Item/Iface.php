<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Supplier
 */


namespace Aimeos\MShop\Supplier\Item;


/**
 * Interface for supplier DTO objects used by the shop.
 *
 * @package MShop
 * @subpackage Supplier
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\AddressRef\Iface,
		\Aimeos\MShop\Common\Item\ListsRef\Iface, \Aimeos\MShop\Common\Item\Status\Iface
{
	/**
	 * Returns the label of the supplier item.
	 *
	 * @return string label of the supplier item
	 */
	public function getLabel() : string;

	/**
	 * Sets the new label of the supplier item.
	 *
	 * @param string $value label of the supplier item
	 * @return \Aimeos\MShop\Supplier\Item\Iface Supplier item for chaining method calls
	 */
	public function setLabel( string $value ) : \Aimeos\MShop\Supplier\Item\Iface;

	/**
	 * Returns the code of the supplier item.
	 *
	 * @return string Code of the supplier item
	 */
	public function getCode() : string;

	/**
	 * Sets the new code of the supplier item.
	 *
	 * @param string $value Code of the supplier item
	 * @return \Aimeos\MShop\Supplier\Item\Iface Supplier item for chaining method calls
	 */
	public function setCode( string $value ) : \Aimeos\MShop\Supplier\Item\Iface;
}
