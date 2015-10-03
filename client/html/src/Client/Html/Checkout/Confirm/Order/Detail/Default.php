<?php

/**
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @copyright Aimeos (aimeos.org), 2015
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of confirm detail basket HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Checkout_Confirm_Order_Detail_Default
	extends Client_Html_Common_Summary_Detail_Default
{
	private $cache;


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return Client_Html_Iface Sub-client object
	 */
	public function getSubClient( $type, $name = null )
	{
		/** client/html/checkout/confirm/order/detail/decorators/excludes
		 * Excludes decorators added by the "common" option from the checkout confirm order detail html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "client/html/common/decorators/default" before they are wrapped
		 * around the html client.
		 *
		 *  client/html/checkout/confirm/order/detail/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("Client_Html_Common_Decorator_*") added via
		 * "client/html/common/decorators/default" to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2015.08
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/checkout/confirm/order/detail/decorators/global
		 * @see client/html/checkout/confirm/order/detail/decorators/local
		 */

		/** client/html/checkout/confirm/order/detail/decorators/global
		 * Adds a list of globally available decorators only to the checkout confirm order detail html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("Client_Html_Common_Decorator_*") around the html client.
		 *
		 *  client/html/checkout/confirm/order/detail/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "Client_Html_Common_Decorator_Decorator1" only to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2015.08
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/checkout/confirm/order/detail/decorators/excludes
		 * @see client/html/checkout/confirm/order/detail/decorators/local
		 */

		/** client/html/checkout/confirm/order/detail/decorators/local
		 * Adds a list of local decorators only to the checkout confirm order detail html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("Client_Html_Checkout_Decorator_*") around the html client.
		 *
		 *  client/html/checkout/confirm/order/detail/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "Client_Html_Checkout_Decorator_Decorator2" only to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2015.08
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/checkout/confirm/order/detail/decorators/excludes
		 * @see client/html/checkout/confirm/order/detail/decorators/global
		 */

		return $this->createSubClient( 'checkout/confirm/order/detail/' . $type, $name );
	}


	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param MW_View_Iface $view The view object which generates the HTML output
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return MW_View_Iface Modified view object
	 */
	protected function setViewParams( MW_View_Iface $view, array &$tags = array(), &$expire = null )
	{
		if( !isset( $this->cache ) )
		{
			$view = parent::setViewParams( $view );

			$view->summaryTaxRates = $this->getTaxRates( $view->summaryBasket );

			$this->cache = $view;
		}

		return $this->cache;
	}
}