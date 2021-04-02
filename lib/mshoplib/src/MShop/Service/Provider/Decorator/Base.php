<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	private $provider;


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

		$this->provider = $provider;
	}


	/**
	 * Returns the price when using the provider.
	 * Usually, this is the lowest price that is available in the service item but can also be a calculated based on
	 * the basket content, e.g. 2% of the value as transaction cost.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object
	 * @return \Aimeos\MShop\Price\Item\Iface Price item containing the price, shipping, rebate
	 */
	public function calcPrice( \Aimeos\MShop\Order\Item\Base\Iface $basket ) : \Aimeos\MShop\Price\Item\Iface
	{
		return $this->provider->calcPrice( $basket );
	}


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigBE( array $attributes ) : array
	{
		return $this->provider->checkConfigBE( $attributes );
	}


	/**
	 * Checks the frontend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes entered by the customer during the checkout process
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigFE( array $attributes ) : array
	{
		return $this->provider->checkConfigFE( $attributes );
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
	{
		return $this->provider->getConfigBE();
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the frontend.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigFE( \Aimeos\MShop\Order\Item\Base\Iface $basket ) : array
	{
		return $this->provider->getConfigFE( $basket );
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
	public function injectGlobalConfigBE( array $config ) : \Aimeos\MShop\Service\Provider\Iface
	{
		parent::injectGlobalConfigBE( $config );

		$this->provider->injectGlobalConfigBE( $config );
		return $this;
	}


	/**
	 * Checks if payment provider can be used based on the basket content.
	 * Checks for country, currency, address, scoring, etc. should be implemented in separate decorators
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object
	 * @return bool True if payment provider can be used, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $basket ) : bool
	{
		return $this->provider->isAvailable( $basket );
	}


	/**
	 * Checks what features the payment provider implements.
	 *
	 * @param int $what Constant from abstract class
	 * @return bool True if feature is available in the payment provider, false if not
	 */
	public function isImplemented( int $what ) : bool
	{
		return $this->provider->isImplemented( $what );
	}


	/**
	 * Cancels the authorization for the given order if supported.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
	 * @return \Aimeos\MShop\Order\Item\Iface Updated order item object
	 */
	public function cancel( \Aimeos\MShop\Order\Item\Iface $order ) : \Aimeos\MShop\Order\Item\Iface
	{
		return $this->provider->cancel( $order );
	}


	/**
	 * Captures the money later on request for the given order if supported.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
	 * @return \Aimeos\MShop\Order\Item\Iface Updated order item object
	 */
	public function capture( \Aimeos\MShop\Order\Item\Iface $order ) : \Aimeos\MShop\Order\Item\Iface
	{
		return $this->provider->capture( $order );
	}


	/**
	 * Processes the order
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object to process
	 * @param array $params Request parameter if available
	 * @return \Aimeos\MShop\Common\Helper\Form\Standard|null Form object or null
	 */
	public function process( \Aimeos\MShop\Order\Item\Iface $order, array $params = [] ) : ?\Aimeos\MShop\Common\Helper\Form\Iface
	{
		return $this->provider->process( $order, $params );
	}


	/**
	 * Sends the details of all orders to the ERP system for further processing
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface[] $orders List of order invoice objects
	 * @return \Aimeos\Map Updated order item objects
	 */
	public function processBatch( iterable $orders ) : \Aimeos\Map
	{
		return $this->provider->processBatch( $orders );
	}


	/**
	 * Refunds the money for the given order if supported.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
	 * @return \Aimeos\MShop\Order\Item\Iface Updated order item object
	 */
	public function refund( \Aimeos\MShop\Order\Item\Iface $order ) : \Aimeos\MShop\Order\Item\Iface
	{
		return $this->provider->refund( $order );
	}


	/**
	 * Executes the payment again for the given order if supported.
	 *
	 * This requires support of the payment gateway and token based payment
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
	 * @return \Aimeos\MShop\Order\Item\Iface Updated order item object
	 */
	public function repay( \Aimeos\MShop\Order\Item\Iface $order ) : \Aimeos\MShop\Order\Item\Iface
	{
		return $this->provider->repay( $order );
	}


	/**
	 * Queries for status updates for the given order if supported.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
	 * @return \Aimeos\MShop\Order\Item\Iface Updated order item object
	 */
	public function query( \Aimeos\MShop\Order\Item\Iface $order ) : \Aimeos\MShop\Order\Item\Iface
	{
		return $this->provider->query( $order );
	}


	/**
	 * Sets the payment attributes in the given service.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Service\Iface $orderServiceItem Order service item that will be added to the basket
	 * @param array $attributes Attribute key/value pairs entered by the customer during the checkout process
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface Order service item with attributes added
	 */
	public function setConfigFE( \Aimeos\MShop\Order\Item\Base\Service\Iface $orderServiceItem,
		array $attributes ) : \Aimeos\MShop\Order\Item\Base\Service\Iface
	{
		return $this->provider->setConfigFE( $orderServiceItem, $attributes );
	}


	/**
	 * Looks for new update files and updates the orders for which status updates were received.
	 * If batch processing of files isn't supported, this method can be empty.
	 *
	 * @return bool True if the update was successful, false if async updates are not supported
	 * @throws \Aimeos\MShop\Service\Exception If updating one of the orders failed
	 */
	public function updateAsync() : bool
	{
		return $this->provider->updateAsync();
	}


	/**
	 * Updates the order status sent by payment gateway notifications
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param \Psr\Http\Message\ResponseInterface $response Response object
	 * @return \Psr\Http\Message\ResponseInterface Response object
	 */
	public function updatePush( \Psr\Http\Message\ServerRequestInterface $request,
		\Psr\Http\Message\ResponseInterface $response ) : \Psr\Http\Message\ResponseInterface
	{
		return $this->provider->updatePush( $request, $response );
	}


	/**
	 * Updates the orders for whose status updates have been received by the confirmation page
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object with parameters and request body
	 * @param \Aimeos\MShop\Order\Item\Iface $orderItem Order item that should be updated
	 * @return \Aimeos\MShop\Order\Item\Iface Updated order item
	 * @throws \Aimeos\MShop\Service\Exception If updating the orders failed
	 */
	public function updateSync( \Psr\Http\Message\ServerRequestInterface $request,
		\Aimeos\MShop\Order\Item\Iface $orderItem ) : \Aimeos\MShop\Order\Item\Iface
	{
		return $this->provider->updateSync( $request, $orderItem );
	}


	/**
	 * Returns the provider object.
	 *
	 * @return \Aimeos\MShop\Service\Provider\Iface Service provider object
	 */
	protected function getProvider() : \Aimeos\MShop\Service\Provider\Iface
	{
		return $this->provider;
	}


	/**
	 * Passes unknown methods to wrapped objects.
	 *
	 * @param string $name Name of the method
	 * @param array $param List of method parameter
	 * @return mixed Returns the value of the called method
	 * @throws \Aimeos\MShop\Service\Exception If method call failed
	 */
	public function __call( string $name, array $param )
	{
		return @call_user_func_array( array( $this->provider, $name ), $param );
	}
}
