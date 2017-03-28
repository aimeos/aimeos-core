<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * Base decorator methods for service provider.
 *
 * @package MShop
 * @subpackage Service
 */
abstract class Base
	extends \Aimeos\MShop\Service\Provider\Base
{
	private $object;


	/**
	 * Initializes a new service provider object using the given context object.
	 *
	 * @param \Aimeos\MShop\Service\Provider\Iface $provider Service provider or decorator
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @param \Aimeos\MShop\Service\Item\Iface $serviceItem Service item with configuration for the provider
	 */
	public function __construct( \Aimeos\MShop\Service\Provider\Iface $provider,
		\Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MShop\Service\Item\Iface $serviceItem )
	{
		parent::__construct( $context, $serviceItem );

		$this->object = $provider;
	}


	/**
	 * Returns the price when using the provider.
	 * Usually, this is the lowest price that is available in the service item but can also be a calculated based on
	 * the basket content, e.g. 2% of the value as transaction cost.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object
	 * @return \Aimeos\MShop\Price\Item\Iface Price item containing the price, shipping, rebate
	 */
	public function calcPrice( \Aimeos\MShop\Order\Item\Base\Iface $basket )
	{
		return $this->object->calcPrice( $basket );
	}


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigBE( array $attributes )
	{
		return $this->object->checkConfigBE( $attributes );
	}


	/**
	 * Checks the frontend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes entered by the customer during the checkout process
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigFE( array $attributes )
	{
		return $this->object->checkConfigFE( $attributes );
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigBE()
	{
		return $this->object->getConfigBE();
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the frontend.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigFE( \Aimeos\MShop\Order\Item\Base\Iface $basket )
	{
		return $this->object->getConfigFE( $basket );
	}


	/**
	 * Injects additional global configuration for the backend.
	 *
	 * It's used for adding additional backend configuration from the application
	 * like the URLs to redirect to.
	 *
	 * Supported redirect URLs are:
	 * - payment.url-success
	 * - payment.url-failure
	 * - payment.url-cancel
	 * - payment.url-update
	 *
	 * @param array $config Associative list of config keys and their value
	 */
	public function injectGlobalConfigBE( array $config )
	{
		parent::injectGlobalConfigBE( $config );

		$this->object->injectGlobalConfigBE( $config );
	}


	/**
	 * Checks if payment provider can be used based on the basket content.
	 * Checks for country, currency, address, scoring, etc. should be implemented in separate decorators
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object
	 * @return boolean True if payment provider can be used, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $basket )
	{
		return $this->object->isAvailable( $basket );
	}


	/**
	 * Checks what features the payment provider implements.
	 *
	 * @param integer $what Constant from abstract class
	 * @return boolean True if feature is available in the payment provider, false if not
	 */
	public function isImplemented( $what )
	{
		return $this->object->isImplemented( $what );
	}


	/**
	 * Cancels the authorization for the given order if supported.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
	 * @return void
	 */
	public function cancel( \Aimeos\MShop\Order\Item\Iface $order )
	{
		$this->object->cancel( $order );
	}


	/**
	 * Captures the money later on request for the given order if supported.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
	 * @return void
	 */
	public function capture( \Aimeos\MShop\Order\Item\Iface $order )
	{
		$this->object->capture( $order );
	}


	/**
	 * Processes the order
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object to process
	 * @param array $params Request parameter if available
	 * @return \Aimeos\MShop\Common\Item\Helper\Form\Standard|null Form object or null
	 */
	public function process( \Aimeos\MShop\Order\Item\Iface $order, array $params = [] )
	{
		return $this->object->process( $order, $params );
	}


	/**
	 * Refunds the money for the given order if supported.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
	 * @return void
	 */
	public function refund( \Aimeos\MShop\Order\Item\Iface $order )
	{
		$this->object->refund( $order );
	}


	/**
	 * Queries for status updates for the given order if supported.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
	 */
	public function query( \Aimeos\MShop\Order\Item\Iface $order )
	{
		$this->object->query( $order );
	}


	/**
	 * Sets the communication object for a service provider.
	 *
	 * @param \Aimeos\MW\Communication\Iface $communication Object of communication
	 */
	public function setCommunication( \Aimeos\MW\Communication\Iface $communication )
	{
		parent::setCommunication( $communication );

		$this->object->setCommunication( $communication );
	}


	/**
	 * Sets the payment attributes in the given service.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Service\Iface $orderServiceItem Order service item that will be added to the basket
	 * @param array $attributes Attribute key/value pairs entered by the customer during the checkout process
	 */
	public function setConfigFE( \Aimeos\MShop\Order\Item\Base\Service\Iface $orderServiceItem, array $attributes )
	{
		$this->object->setConfigFE( $orderServiceItem, $attributes );
	}


	/**
	 * Looks for new update files and updates the orders for which status updates were received.
	 * If batch processing of files isn't supported, this method can be empty.
	 *
	 * @return boolean True if the update was successful, false if async updates are not supported
	 * @throws \Aimeos\MShop\Service\Exception If updating one of the orders failed
	 */
	public function updateAsync()
	{
		return $this->object->updateAsync();
	}


	/**
	 * Updates the orders for which status updates were received via direct requests (like HTTP).
	 *
	 * @param array $params Associative list of request parameters
	 * @param string|null $body Information sent within the body of the request
	 * @param string|null &$response Response body for notification requests
	 * @param array &$header Response headers for notification requests
	 * @return \Aimeos\MShop\Order\Item\Iface|null Order item if update was successful, null if the given parameters are not valid for this provider
	 * @throws \Aimeos\MShop\Service\Exception If updating one of the orders failed
	 */
	public function updateSync( array $params = [], $body = null, &$response = null, array &$header = [] )
	{
		return $this->object->updateSync( $params, $body, $response, $header );
	}


	/**
	 * Returns the provider object.
	 *
	 * @return \Aimeos\MShop\Service\Provider\Iface Service provider object
	 */
	protected function getProvider()
	{
		return $this->object;
	}


	/**
	 * Passes unknown methods to wrapped objects.
	 *
	 * @param string $name Name of the method
	 * @param array $param List of method parameter
	 * @return mixed Returns the value of the called method
	 * @throws \Aimeos\MShop\Service\Exception If method call failed
	 */
	public function __call( $name, array $param )
	{
		return @call_user_func_array( array( $this->object, $name ), $param );
	}
}
