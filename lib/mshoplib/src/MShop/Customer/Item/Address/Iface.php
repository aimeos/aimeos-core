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
interface Iface extends \Aimeos\MShop\Common\Item\Address\Iface
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
	 * Returns the position of the address item.
	 *
	 * @return integer Position of the address item
	 */
	public function getPosition();


	/**
	 * Sets the Position of the address item.
	 *
	 * @param integer $position New position of the address item
	 * @return void
	 */
	public function setPosition( $position );


	/**
	 * Copies the values of the order address item into the address item.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Address\Iface $item Order address item
	 * @return \Aimeos\MShop\Common\Item\Address\Iface The address item for method chaining
	 */
	public function copyFrom( \Aimeos\MShop\Order\Item\Base\Address\Iface $item );

}
