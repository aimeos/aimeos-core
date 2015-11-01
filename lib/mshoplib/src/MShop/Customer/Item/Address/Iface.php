<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Customer
 */


namespace Aimeos\MShop\Customer\Item\Address;


/**
 * Interface for provider common address DTO objects used by the shop.
 * @package MShop
 * @subpackage Customer
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
}
