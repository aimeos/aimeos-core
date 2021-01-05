<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2021
 * @package MShop
 * @subpackage Coupon
 */


namespace Aimeos\MShop\Coupon\Provider\Decorator;


/**
 * Decorator to allow using a coupon by a customer only once
 *
 * @package MShop
 * @subpackage Coupon
 */
class Once
	extends \Aimeos\MShop\Coupon\Provider\Decorator\Base
	implements \Aimeos\MShop\Coupon\Provider\Decorator\Iface
{
	/**
	 * Checks for requirements.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 * @return bool True if the requirements are met, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $base ) : bool
	{
		$addresses = $base->getAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );

		if( ( $address = reset( $addresses ) ) !== false )
		{
			$manager = \Aimeos\MShop::create( $this->getContext(), 'order' );

			$search = $manager->filter()->slice( 0, 1 );
			$expr = [
				$search->compare( '==', 'order.base.address.email', $address->getEmail() ),
				$search->compare( '==', 'order.base.coupon.code', $this->getCode() ),
				$search->compare( '>=', 'order.statuspayment', \Aimeos\MShop\Order\Item\Base::PAY_PENDING ),
			];
			$search->setConditions( $search->and( $expr ) );

			if( !$manager->search( $search )->isEmpty() ) {
				return false;
			}
		}

		return parent::isAvailable( $base );
	}
}
