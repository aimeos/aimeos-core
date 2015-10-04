<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of checkout service summary HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Checkout_Standard_Summary_Service_Standard
	extends Client_Html_Common_Summary_Service_Standard
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
		/** client/html/checkout/standard/summary/service/decorators/excludes
		 * Excludes decorators added by the "common" option from the checkout standard summary service html client
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
		 *  client/html/checkout/standard/summary/service/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("Client_Html_Common_Decorator_*") added via
		 * "client/html/common/decorators/default" to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2015.08
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/checkout/standard/summary/service/decorators/global
		 * @see client/html/checkout/standard/summary/service/decorators/local
		 */

		/** client/html/checkout/standard/summary/service/decorators/global
		 * Adds a list of globally available decorators only to the checkout standard summary service html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("Client_Html_Common_Decorator_*") around the html client.
		 *
		 *  client/html/checkout/standard/summary/service/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "Client_Html_Common_Decorator_Decorator1" only to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2015.08
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/checkout/standard/summary/service/decorators/excludes
		 * @see client/html/checkout/standard/summary/service/decorators/local
		 */

		/** client/html/checkout/standard/summary/service/decorators/local
		 * Adds a list of local decorators only to the checkout standard summary service html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("Client_Html_Checkout_Decorator_*") around the html client.
		 *
		 *  client/html/checkout/standard/summary/service/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "Client_Html_Checkout_Decorator_Decorator2" only to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2015.08
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/checkout/standard/summary/service/decorators/excludes
		 * @see client/html/checkout/standard/summary/service/decorators/global
		 */

		return $this->createSubClient( 'checkout/standard/summary/service/' . $type, $name );
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
		$view = parent::setViewParams( $view, $tags, $expire );

		if( !isset( $this->cache ) )
		{
			$target = $view->config( 'client/html/checkout/standard/url/target' );
			$cntl = $view->config( 'client/html/checkout/standard/url/controller', 'checkout' );
			$action = $view->config( 'client/html/checkout/standard/url/action', 'index' );
			$config = $view->config( 'client/html/checkout/standard/url/config', array() );

			$view->summaryUrlServicePayment = $view->url( $target, $cntl, $action, array( 'c_step' => 'payment' ), array(), $config );
			$view->summaryUrlServiceDelivery = $view->url( $target, $cntl, $action, array( 'c_step' => 'delivery' ), array(), $config );

			$this->cache = $view;
		}

		return $this->cache;
	}
}