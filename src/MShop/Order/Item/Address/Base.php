<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Address;


/**
 * Abstract class with constants for all order address items.
 *
 * @package MShop
 * @subpackage Order
 */
abstract class Base extends \Aimeos\MShop\Common\Item\Address\Base
{
	/**
	 * Delivery address.
	 */
	const TYPE_DELIVERY = 'delivery';

	/**
	 * Billing address.
	 */
	const TYPE_PAYMENT = 'payment';


	/**
	 * Checks if the given address type is valid
	 *
	 * @param string $value Address type defined in \Aimeos\MShop\Order\Item\Address\Base
	 * @return string Sanitized address type
	 * @throws \Aimeos\MShop\Order\Exception If type is invalid
	 * @deprecated 2025.01
	 */
	protected function checkType( string $value ) : string
	{
		switch( $value )
		{
			case \Aimeos\MShop\Order\Item\Address\Base::TYPE_DELIVERY:
			case \Aimeos\MShop\Order\Item\Address\Base::TYPE_PAYMENT:
				return (string) $value;
		}

		throw new \Aimeos\MShop\Order\Exception( sprintf( 'Address type "%1$s" not within allowed range', $value ) );
	}
}
