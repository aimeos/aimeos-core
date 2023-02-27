<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider;


/**
 * Abstract class for all service provider implementations with some default methods.
 *
 * @package MShop
 * @subpackage Service
 */
abstract class Base
	implements Iface, \Aimeos\Macro\Iface
{
	use \Aimeos\Macro\Macroable;

	private \Aimeos\MShop\ContextIface $context;
	private \Aimeos\MShop\Service\Item\Iface $serviceItem;
	private ?\Aimeos\MShop\Service\Provider\Iface $object;
	private array $beGlobalConfig;


	/**
	 * Initializes the service provider object.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object with required objects
	 * @param \Aimeos\MShop\Service\Item\Iface $serviceItem Service item with configuration for the provider
	 */
	public function __construct( \Aimeos\MShop\ContextIface $context, \Aimeos\MShop\Service\Item\Iface $serviceItem )
	{
		$this->context = $context;
		$this->serviceItem = $serviceItem;
	}


	/**
	 * Returns the price when using the provider.
	 * Usually, this is the lowest price that is available in the service item but can also be a calculated based on
	 * the basket content, e.g. 2% of the value as transaction cost.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
	 * @param array $options Selected options by customer from frontend
	 * @return \Aimeos\MShop\Price\Item\Iface Price item containing the price, shipping, rebate
	 */
	public function calcPrice( \Aimeos\MShop\Order\Item\Iface $basket, array $options = [] ) : \Aimeos\MShop\Price\Item\Iface
	{
		$manager = \Aimeos\MShop::create( $this->context, 'price' );
		$prices = $this->serviceItem->getRefItems( 'price', 'default', 'default' );

		return $prices->isEmpty() ? $manager->create() : $manager->getLowestPrice( $prices, 1 );
	}


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid resp. null for attributes whose values are OK
	 */
	public function checkConfigBE( array $attributes ) : array
	{
		return [];
	}


	/**
	 * Checks the frontend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes entered by the customer during the checkout process
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid resp. null for attributes whose values are OK
	 */
	public function checkConfigFE( array $attributes ) : array
	{
		return [];
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
	{
		return [];
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the frontend.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
	 * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
	 */
	public function getConfigFE( \Aimeos\MShop\Order\Item\Iface $basket ) : array
	{
		return [];
	}


	/**
	 * Returns the service item which also includes the configuration for the service provider.
	 *
	 * @return \Aimeos\MShop\Service\Item\Iface Service item
	 */
	public function getServiceItem() : \Aimeos\MShop\Service\Item\Iface
	{
		return $this->serviceItem;
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
	 * @return \Aimeos\MShop\Service\Provider\Iface Provider object for chaining method calls
	 */
	public function injectGlobalConfigBE( array $config ) : \Aimeos\MShop\Service\Provider\Iface
	{
		$this->beGlobalConfig = $config;
		return $this;
	}


	/**
	 * Checks if payment provider can be used based on the basket content.
	 * Checks for country, currency, address, RMS, etc. -> in separate decorators
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
	 * @return bool True if payment provider can be used, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Iface $basket ) : bool
	{
		return true;
	}


	/**
	 * Checks what features the payment provider implements.
	 *
	 * @param int $what Constant from abstract class
	 * @return bool True if feature is available in the payment provider, false if not
	 */
	public function isImplemented( int $what ) : bool
	{
		return false;
	}


	/**
	 * Queries for status updates for the given order if supported.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
	 * @return \Aimeos\MShop\Order\Item\Iface Updated order item object
	 */
	public function query( \Aimeos\MShop\Order\Item\Iface $order ) : \Aimeos\MShop\Order\Item\Iface
	{
		$msg = $this->context->translate( 'mshop', 'Method "%1$s" for provider not available' );
		throw new \Aimeos\MShop\Service\Exception( sprintf( $msg, 'query' ) );
	}


	/**
	 * Injects the outer object into the decorator stack
	 *
	 * @param \Aimeos\MShop\Service\Provider\Iface $object First object of the decorator stack
	 * @return \Aimeos\MShop\Service\Provider\Iface Service object for chaining method calls
	 */
	public function setObject( \Aimeos\MShop\Service\Provider\Iface $object ) : \Aimeos\MShop\Service\Provider\Iface
	{
		$this->object = $object;
		return $this;
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
		return false;
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
		return $response->withStatus( 501, 'Not implemented' );
	}


	/**
	 * Updates the orders for whose status updates have been received by the confirmation page
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object with parameters and request body
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order item that should be updated
	 * @return \Aimeos\MShop\Order\Item\Iface Updated order item
	 * @throws \Aimeos\MShop\Service\Exception If updating the orders failed
	 */
	public function updateSync( \Psr\Http\Message\ServerRequestInterface $request,
		\Aimeos\MShop\Order\Item\Iface $order ) : \Aimeos\MShop\Order\Item\Iface
	{
		return $order;
	}


	/**
	 * Creates service attributes from the passed data
	 *
	 * @param array $map Attribute key/value pairs entered by the customer during the checkout process
	 * @param string $type Type of the configuration values (delivery or payment)
	 * @return array List of \Aimeos\MShop\Order\Item\Service\Attribute\Iface objects
	 */
	protected function attributes( array $map, string $type ) : array
	{
		$list = [];
		$manager = \Aimeos\MShop::create( $this->context, 'order/service' );

		foreach( $map as $key => $value ) {
			$list[] = $manager->createAttributeItem()->setType( $type )->setCode( $key )->setValue( $value );
		}

		return $list;
	}


	/**
	 * Checks required fields and the types of the given data map
	 *
	 * @param array $criteria Multi-dimensional associative list of criteria configuration
	 * @param array $map Values to check agains the criteria
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid resp. null for attributes whose values are OK
	 */
	protected function checkConfig( array $criteria, array $map ) : array
	{
		$helper = new \Aimeos\MShop\Common\Helper\Config\Standard( $this->getConfigItems( $criteria ) );
		return $helper->check( $map );
	}


	/**
	 * Returns the context item.
	 *
	 * @return \Aimeos\MShop\ContextIface Context item
	 */
	protected function context() : \Aimeos\MShop\ContextIface
	{
		return $this->context;
	}


	/**
	 * Returns the service related data from the customer account if available
	 *
	 * @param string $customerId Unique customer ID the service token belongs to
	 * @param string $key Key of the value that should be returned
	 * @return array|string|null Service data or null if none is available
	 */
	protected function data( string $customerId, string $key )
	{
		if( $customerId )
		{
			$item = \Aimeos\MShop::create( $this->context, 'customer' )->get( $customerId, ['service'] );

			if( $listItem = $item->getListItem( 'service', 'default', $this->getServiceItem()->getId() ) ) {
				return $listItem->getConfigValue( $key );
			}
		}

		return null;
	}


	/**
	 * Returns the calculated amount of the price item
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Price item
	 * @param bool $costs Include costs per item
	 * @param bool $tax Include tax
	 * @param int $precision Number for decimal digits
	 * @return string Formatted money amount
	 */
	protected function getAmount( \Aimeos\MShop\Price\Item\Iface $price, bool $costs = true, bool $tax = true,
		int $precision = null ) : string
	{
		$amount = $price->getValue();

		if( $costs === true ) {
			$amount += $price->getCosts();
		}

		if( $tax === true && $price->getTaxFlag() === false )
		{
			$tmp = clone $price;

			if( $costs === false )
			{
				$tmp->clear();
				$tmp->setValue( $price->getValue() );
				$tmp->setTaxRate( $price->getTaxRate() );
				$tmp->setQuantity( $price->getQuantity() );
			}

			$amount += $tmp->getTaxValue();
		}

		return number_format( $amount, $precision !== null ? $precision : $price->getPrecision(), '.', '' );
	}


	/**
	 * Returns the order service matching the given code from the basket
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
	 * @param string $type Service type constant from \Aimeos\MShop\Order\Item\Service\Base
	 * @param string $code Code of the service item that should be returned
	 * @return \Aimeos\MShop\Order\Item\Service\Iface Order service item
	 * @throws \Aimeos\MShop\Order\Exception If no service for the given type and code is found
	 */
	protected function getBasketService( \Aimeos\MShop\Order\Item\Iface $basket, string $type,
		string $code ) : \Aimeos\MShop\Order\Item\Service\Iface
	{
		$msg = $this->context->translate( 'mshop', 'Service not available' );

		return map( $basket->getService( $type ) )->find( function( $service ) use ( $code ) {
				return $service->getCode() === $code;
		}, new \Aimeos\MShop\Service\Exception( $msg ) );
	}


	/**
	 * Returns the criteria attribute items for the backend configuration
	 *
	 * @return \Aimeos\Base\Criteria\Attribute\Iface[] List of criteria attribute items
	 */
	protected function getConfigItems( array $configList ) : array
	{
		$list = [];

		foreach( $configList as $key => $config ) {
			$list[$key] = new \Aimeos\Base\Criteria\Attribute\Standard( $config );
		}

		return $list;
	}


	/**
	 * Returns the configuration value that matches one of the given keys.
	 *
	 * The config of the service item and (optionally) the global config
	 * is tested in the order of the keys. The first one that matches will
	 * be returned.
	 *
	 * @param array|string $keys Key name or list of key names that should be tested for in the order to test
	 * @param mixed $default Returned value if the key wasn't was found
	 * @return mixed Value of the first key that matches or null if none was found
	 */
	protected function getConfigValue( $keys, $default = null )
	{
		foreach( (array) $keys as $key )
		{
			if( ( $value = $this->getServiceItem()->getConfigValue( $key ) ) !== null ) {
				return $value;
			}

			if( isset( $this->beGlobalConfig[$key] ) ) {
				return $this->beGlobalConfig[$key];
			}
		}

		return $default;
	}


	/**
	 * Logs the given message with the passed log level
	 *
	 * @param mixed $msg Message or object
	 * @param int $level Log level (default: ERR)
	 * @return self Same object for fluid method calls
	 */
	protected function log( $msg, int $level = \Aimeos\Base\Logger\Iface::ERR ) : self
	{
		$facility = basename( str_replace( '\\', '/', get_class( $this ) ) );
		$trace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 2 );
		$trace = array_pop( $trace ) ?: [];
		$name = ( $trace['class'] ?? '' ) . '::' . ( $trace['function'] ?? '' );

		if( !is_scalar( $msg ) ) {
			$msg = print_r( $msg, true );
		}

		$this->context->logger()->log( $name . ': ' . $msg, $level, 'core/service/' . $facility );
		return $this;
	}


	/**
	 * Returns the first object of the decorator stack
	 *
	 * @return \Aimeos\MShop\Service\Provider\Iface First object of the decorator stack
	 */
	protected function object() : \Aimeos\MShop\Service\Provider\Iface
	{
		return $this->object ?? $this;
	}


	/**
	 * Returns the configuration value that matches given key
	 *
	 * @param string $key Key name
	 * @return mixed Value for the key
	 * @throws \Aimeos\MShop\Service\Exception If configuration key isn't found
	 */
	protected function require( string $key )
	{
		if( ( $value = $this->getConfigValue( $key ) ) === null )
		{
			$msg = $this->context()->translate( 'mshop', 'Missing configuration "%1$s"' );
			throw new \Aimeos\MShop\Service\Exception( sprintf( $msg, $key ) );
		}

		return $value;
	}


	/**
	 * Saves the order item.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $item Order object
	 * @return \Aimeos\MShop\Order\Item\Iface Order object including the generated ID
	 */
	protected function save( \Aimeos\MShop\Order\Item\Iface $item ) : \Aimeos\MShop\Order\Item\Iface
	{
		return \Aimeos\MShop::create( $this->context, 'order' )->save( $item );
	}


	/**
	 * Adds the service data to the customer account if available
	 *
	 * @param string $customerId Unique customer ID the service token belongs to
	 * @param string $key Key of the value that should be added
	 * @param string|array $data Service data to store
	 * @param \Aimeos\MShop\Service\Provider\Iface Provider object for chaining method calls
	 */
	protected function setData( string $customerId, string $key, $data ) : \Aimeos\MShop\Service\Provider\Iface
	{
		if( $customerId )
		{
			$manager = \Aimeos\MShop::create( $this->context, 'customer' );
			$item = $manager->get( $customerId, ['service'] );
			$serviceId = $this->getServiceItem()->getId();

			if( ( $listItem = $item->getListItem( 'service', 'default', $serviceId, false ) ) === null )
			{
				$listManager = \Aimeos\MShop::create( $this->context, 'customer/lists' );
				$listItem = $listManager->create()->setRefId( $serviceId );
			}

			$manager->save( $item->addListItem( 'service', $listItem->setConfigValue( $key, $data ) ) );
		}

		return $this;
	}


	/**
	 * Throws an exception with the given message
	 *
	 * @param string $msg Message
	 * @param string|null $domain Translation domain
	 * @param int $code Custom error code
	 */
	protected function throw( string $msg, string $domain = null, int $code = 0 )
	{
		if( $domain ) {
			$msg = $this->context->translate( $domain, $msg );
		}

		throw new \Aimeos\MShop\Service\Exception( $msg, $code );
	}
}
