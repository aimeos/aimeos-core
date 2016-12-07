<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Coupon
 */


namespace Aimeos\MShop\Coupon\Provider\Factory;


/**
 * Factory interface for coupon provider.
 *
 * @package MShop
 * @subpackage Coupon
 */
interface Iface
	extends \Aimeos\MShop\Coupon\Provider\Iface
{
	/**
	 * Initializes the coupon model.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 * @param \Aimeos\MShop\Coupon\Item\Iface $item Coupon item to set
	 * @param string $code Coupon code entered by the customer
	 * @return null
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MShop\Coupon\Item\Iface $item, $code );


	/**
	 * Sets the reference of the outside object.
	 *
	 * Each coupon provider can be enhanced by one or more coupon decorators that
	 * provide additional functionality. To enable inner decorators or the provider
	 * to call public methods of themselves that are also enhanced by outer
	 * decorators, a reference to the most outer object is required.
	 *
	 * Think of a coupon provider that is wrapped by a decorator whose addCoupon()
	 * method is called:
	 * <code>
	 * $provider = new ExampleProvider();
	 * $decorator = new ExampleDecorator( $provider );
	 * $decorator->addCoupon();
	 * </code>
	 *
	 * The provider wants to check if the coupon code should be available before
	 * it adds the coupon to the basket. Therefore it has to call the isAvailable()
	 * method of the most outer object (the decorator) to be sure the result is
	 * correct:
	 * * ExampleDecorator::addCoupon() calls ExampleProvider::addCoupon()
	 * * ExampleProvider::addCoupon() calls ExampleDecorator::isAvailable()
	 * * ExampleDecorator::isAvailable() calls ExampleProvider::isAvailable()
	 * * ExampleProvider::isAvailable() returns true
	 * * ExampleDecorator::isAvailable() return false because it's implementation denies availability
	 *
	 * The result will be "false" in this case. If the provider would have no
	 * access to the outmost object (the decorator) it would only be able to
	 * call isAvailable() if itself which would lead to a result of "true".
	 *
	 * @param \Aimeos\MShop\Coupon\Provider\Iface $object Reference to the outside provider or decorator
	 * @return void
	 */
	public function setObject( \Aimeos\MShop\Coupon\Provider\Iface $object );
}
