<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	private $object;


	/**
	 * Initializes a new coupon provider object using the given context object.
	 *
	 * @param \Aimeos\MShop\Coupon\Provider\Iface $provider Coupon provider interface
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @param \Aimeos\MShop\Coupon\Item\Iface $couponItem Coupon item with configuration for the provider
	 * @param string $code Coupon code entered by the customer
	 */
	public function __construct(  \Aimeos\MShop\Coupon\Provider\Iface $provider,
		\Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MShop\Coupon\Item\Iface $couponItem, $code )
	{
		$this->provider = $provider;

		parent::__construct( $context, $couponItem, $code );
	}


	/**
	 * Adds the result of a coupon to the order base instance.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 */
	public function addCoupon( \Aimeos\MShop\Order\Item\Base\Iface $base )
	{
		$this->provider->addCoupon( $base );
	}


	/**
	 * Updates the result of a coupon to the order base instance.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 */
	public function updateCoupon( \Aimeos\MShop\Order\Item\Base\Iface $base )
	{
		$this->provider->updateCoupon( $base );
	}


	/**
	 * Removes the result of a coupon from the order base instance.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 */
	public function deleteCoupon( \Aimeos\MShop\Order\Item\Base\Iface $base )
	{
		$this->provider->deleteCoupon( $base );
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
	 * Sets the reference of the outside object.
	 *
	 * @param \Aimeos\MShop\Coupon\Provider\Iface $object Reference to the outside provider or decorator
	 */
	public function setObject( \Aimeos\MShop\Coupon\Provider\Iface $object )
	{
		$this->provider->setObject( $object );
		$this->object = $object;
	}


	/**
	 * Returns the outmost decorator or a reference to the provider itself.
	 *
	 * @return \Aimeos\MShop\Coupon\Provider\Iface Outmost object
	 */
	protected function getObject()
	{
		if( isset( $this->object ) ) {
			return $this->object;
		}

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