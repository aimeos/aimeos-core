<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2018
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
	 * @return boolean True if the requirements are met, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $base )
	{
		$addresses = $base->getAddresses();
		$type = \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT;

		if( isset( $addresses[$type] ) )
		{
			$address = $addresses[$type];
			$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'order' );

			$search = $manager->createSearch();
			$expr = [
				$search->compare( '==', 'order.base.address.email', $address->getEmail() ),
				$search->compare( '==', 'order.base.coupon.code', $this->getCode() ),
				$search->compare( '>=', 'order.statuspayment', \Aimeos\MShop\Order\Item\Base::PAY_PENDING ),
			];
			$search->setConditions( $search->combine( '&&', $expr ) );
			$search->setSlice( 0, 1 );

			if( count( $manager->searchItems( $search ) ) > 0 ) {
				return false;
			}
		}

		return parent::isAvailable( $base );
	}
}
