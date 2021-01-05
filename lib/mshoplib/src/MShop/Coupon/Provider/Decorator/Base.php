<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
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
		\Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MShop\Coupon\Item\Iface $couponItem, string $code )
	{
		$this->provider = $provider;

		parent::__construct( $context, $couponItem, $code );
	}


	/**
	 * Returns the price the discount should be applied to
	 *
	 * The result depends on the configured restrictions and it must be less or
	 * equal to the passed price.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 * @return \Aimeos\MShop\Price\Item\Iface New price that should be used
	 */
	public function calcPrice( \Aimeos\MShop\Order\Item\Base\Iface $base ) : \Aimeos\MShop\Price\Item\Iface
	{
		return $this->provider->calcPrice( $base );
	}


	/**
	 * Updates the result of a coupon to the order base instance.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 * @return \Aimeos\MShop\Coupon\Provider\Iface Provider object for method chaining
	 */
	public function update( \Aimeos\MShop\Order\Item\Base\Iface $base ) : \Aimeos\MShop\Coupon\Provider\Iface
	{
		if( $this->getObject()->isAvailable( $base ) ) {
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
	 * @return bool True of coupon can be granted, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $base ) : bool
	{
		return $this->provider->isAvailable( $base );
	}


	/**
	 * Injects the reference of the outmost object
	 *
	 * @param \Aimeos\MShop\Coupon\Provider\Iface $object Reference to the outmost provider or decorator
	 * @return \Aimeos\MShop\Coupon\Provider\Iface Coupon object for chaining method calls
	 */
	public function setObject( \Aimeos\MShop\Coupon\Provider\Iface $object ) : \Aimeos\MShop\Coupon\Provider\Iface
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
	protected function getProvider() : \Aimeos\MShop\Coupon\Provider\Iface
	{
		return $this->provider;
	}
}
