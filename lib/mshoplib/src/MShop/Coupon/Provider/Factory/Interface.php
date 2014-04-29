<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Coupon
 */


/**
 * Factory interface for coupon provider.
 *
 * @package MShop
 * @subpackage Coupon
 */
interface MShop_Coupon_Provider_Factory_Interface
	extends MShop_Coupon_Provider_Interface
{
	/**
	 * Initializes the coupon model.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 * @param MShop_Coupon_Item_Interface $item Coupon item to set
	 * @param string $code Coupon code entered by the customer
	 */
	public function __construct( MShop_Context_Item_Interface $context, MShop_Coupon_Item_Interface $item, $code );


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
	 * @param MShop_Coupon_Provider_Interface $object Reference to the outside provider or decorator
	 */
	public function setObject( MShop_Coupon_Provider_Interface $object );
}
