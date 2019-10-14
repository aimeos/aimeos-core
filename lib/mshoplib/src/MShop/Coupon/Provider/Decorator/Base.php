<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Coupon
 */


namespace Aimeos\MShop\Coupon\Provider\Decorator;


/**
 * Base decorator methods for coupon provider.
 *
 * @package MShop
 * @subpackage Coupon
 */
abstract class Base
	extends \Aimeos\MShop\Coupon\Provider\Base
{
	private $provider;


	/**
	 * Initializes a new coupon provider object using the given context object.
	 *
	 * @param \Aimeos\MShop\Coupon\Provider\Iface $provider Coupon provider interface
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @param \Aimeos\MShop\Coupon\Item\Iface $couponItem Coupon item with configuration for the provider
	 * @param string $code Coupon code entered by the customer
	 */
	public function __construct( \Aimeos\MShop\Coupon\Provider\Iface $provider,
		\Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MShop\Coupon\Item\Iface $couponItem, $code )
	{
		$this->provider = $provider;

		parent::__construct( $context, $couponItem, $code );
	}


	/**
	 * Updates the result of a coupon to the order base instance.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 * @return \Aimeos\MShop\Coupon\Provider\Iface Provider object for method chaining
	 */
	public function update( \Aimeos\MShop\Order\Item\Base\Iface $base )
	{
		if( $this->isAvailable( $base ) ) {
			$this->provider->update( $base );
		} else {
			$base->setCoupon( $this->getCode(), [] );
		}

		return $this;
	}


	/**
	 * Tests if a coupon should be granted.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 * @return boolean True of coupon can be granted, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $base )
	{
		return $this->provider->isAvailable( $base );
	}


	/**
	 * Injects the reference of the outmost object
	 *
	 * @param \Aimeos\MShop\Coupon\Provider\Iface $object Reference to the outmost provider or decorator
	 * @return \Aimeos\MShop\Coupon\Provider\Iface Coupon object for chaining method calls
	 */
	public function setObject( \Aimeos\MShop\Coupon\Provider\Iface $object )
	{
		parent::setObject( $object );

		$this->provider->setObject( $object );

		return $this;
	}


	/**
	 * Returns the stored provider object.
	 *
	 * @return \Aimeos\MShop\Coupon\Provider\Iface Coupon provider
	 */
	protected function getProvider()
	{
		return $this->provider;
	}
}
