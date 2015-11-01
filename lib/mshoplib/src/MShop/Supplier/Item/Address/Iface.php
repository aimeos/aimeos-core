<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Supplier
 */


namespace Aimeos\MShop\Supplier\Item\Address;


/**
 * Interface for provider supplier address DTO objects used by the shop.
 * @package MShop
 * @subpackage Supplier
 */
interface Iface extends \Aimeos\MShop\Common\Item\Address\Iface, \Aimeos\MShop\Common\Item\Position\Iface
{
	/**
	 * Returns the reference id regarding to the product suppliercode of the address.
	 *
	 * @return string Address reference id
	 */
	public function getRefId();


	/**
	 * Sets the new reference id regarding to the product suppliercode of the address.
	 *
	 * @param string $refid New reference id of the address
	 * @return void
	 */
	public function setRefId( $refid );


	/**
	 * Copies the values of the order address item into the address item.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Address\Iface $item Order address item
	 * @return \Aimeos\MShop\Common\Item\Address\Iface The address item for method chaining
	 */
	public function copyFrom( \Aimeos\MShop\Order\Item\Base\Address\Iface $item );

}
