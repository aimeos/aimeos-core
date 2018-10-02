<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
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
{
	private $object;
	private $context;
	private $serviceItem;
	private $communication;
	private $beGlobalConfig;


	/**
	 * Initializes the service provider object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @param \Aimeos\MShop\Service\Item\Iface $serviceItem Service item with configuration for the provider
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MShop\Service\Item\Iface $serviceItem )
	{
		$this->context = $context;
		$this->serviceItem = $serviceItem;
	}


	/**
	 * Catch unknown methods
	 *
	 * @param string $name Name of the method
	 * @param array $param List of method parameter
	 * @throws \Aimeos\MShop\Common\Manager\Exception If method call failed
	 */
	public function __call( $name, array $param )
	{
		throw new \Aimeos\MShop\Service\Exception( sprintf( 'Unable to call method "%1$s"', $name ) );
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
		$priceManager = \Aimeos\MShop\Factory::createManager( $this->context, 'price' );
		$prices = $this->serviceItem->getRefItems( 'price', 'default', 'default' );

		if( count( $prices ) > 0 ) {
			return $priceManager->getLowestPrice( $prices, 1 );
		}

		return $priceManager->createItem();
	}


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid resp. null for attributes whose values are OK
	 */
	public function checkConfigBE( array $attributes )
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
	public function checkConfigFE( array $attributes )
	{
		return [];
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigBE()
	{
		return [];
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
		return [];
	}


	/**
	 * Returns the service item which also includes the configuration for the service provider.
	 *
	 * @return \Aimeos\MShop\Service\Item\Iface Service item
	 */
	public function getServiceItem()
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
	 */
	public function injectGlobalConfigBE( array $config )
	{
		$this->beGlobalConfig = $config;
	}


	/**
	 * Checks if payment provider can be used based on the basket content.
	 * Checks for country, currency, address, RMS, etc. -> in separate decorators
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object
	 * @return boolean True if payment provider can be used, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $basket )
	{
		return true;
	}


	/**
	 * Checks what features the payment provider implements.
	 *
	 * @param integer $what Constant from abstract class
	 * @return boolean True if feature is available in the payment provider, false if not
	 */
	public function isImplemented( $what )
	{
		return false;
	}


	/**
	 * Queries for status updates for the given order if supported.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
	 */
	public function query( \Aimeos\MShop\Order\Item\Iface $order )
	{
		throw new \Aimeos\MShop\Service\Exception( sprintf( 'Method "%1$s" for provider not available', 'query' ) );
	}


	/**
	 * Injects the outer object into the decorator stack
	 *
	 * @param \Aimeos\MShop\Plugin\Provider\Iface $object First object of the decorator stack
	 * @return \Aimeos\MShop\Plugin\Provider\Iface Plugin object for chaining method calls
	 */
	public function setObject( \Aimeos\MShop\Plugin\Provider\Iface $object )
	{
		$this->object = $object;
		return $this;
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
		return false;
	}


	/**
	 * Updates the order status sent by payment gateway notifications
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface Request object
	 * @return \Psr\Http\Message\ResponseInterface Response object
	 */
	public function updatePush( \Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response )
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
	public function updateSync( \Psr\Http\Message\ServerRequestInterface $request, \Aimeos\MShop\Order\Item\Iface $order )
	{
		return $order;
	}


	/**
	 * Sets the communication object for a service provider.
	 *
	 * @param \Aimeos\MW\Communication\Iface $communication Object of communication
	 * @deprecated To be removed in 2019.01
	 */
	public function setCommunication( \Aimeos\MW\Communication\Iface $communication )
	{
		$this->communication = $communication;
	}


	/**
	 * Returns the communication object for the service provider.
	 *
	 * @return \Aimeos\MW\Communication\Iface Object for communication
	 * @deprecated To be removed in 2019.01
	 */
	protected function getCommunication()
	{
		if( !isset( $this->communication ) ) {
			$this->communication = new \Aimeos\MW\Communication\Curl();
		}

		return $this->communication;
	}


	/**
	 * Calculates the last date behind the given timestamp depending on the other paramters.
	 *
	 * This method is used to calculate the date for comparing the order date to
	 * if e.g. credit card payments should be captured or direct debit should be
	 * checked after the given amount of days from external payment providers.
	 * This method can calculate with business/working days only if requested
	 * and use the given list of public holidays to take them into account.
	 *
	 * @param integer $timestamp Timestamp to use as starting point for the backward calculation
	 * @param integer $skipdays Number of days to calculate backwards
	 * @param boolean $businessOnly True if only business days should be used for calculation, false if not
	 * @param string $publicHolidays Comma separated list of public holidays in YYYY-MM-DD format
	 * @return string Date in YYY-MM-DD format to be compared to the order date
	 * @throws \Aimeos\MShop\Service\Exception If the given holiday string is in the wrong format and can't be processed
	 */
	protected function calcDateLimit( $timestamp, $skipdays = 0, $businessOnly = false, $publicHolidays = '' )
	{
		$holidays = $this->getPublicHolidays( $publicHolidays );

		if( !empty( $holidays ) )
		{
			for( $i = 0; $i <= $skipdays; $i++ )
			{
				$date = date( 'Y-m-d', $timestamp - $i * 86400 );

				if( isset( $holidays[$date] ) ) {
					$skipdays++;
				}
			}
		}

		if( $businessOnly === true )
		{
			// adds days for weekends
			for( $i = 0; $i <= $skipdays; $i++ )
			{
				$ts = $timestamp - $i * 86400;

				if( date( 'N', $ts ) > 5 && !isset( $holidays[date( 'Y-m-d', $ts )] ) ) {
					$skipdays++;
				}
			}
		}

		return date( 'Y-m-d', $timestamp - $skipdays * 86400 );
	}


	/**
	 * Checks required fields and the types of the config array.
	 *
	 * @param array $config Config parameters
	 * @param array $attributes Attributes for the config array
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid resp. null for attributes whose values are OK
	 */
	protected function checkConfig( array $config, array $attributes )
	{
		$errors = [];

		foreach( $config as $key => $def )
		{
			if( $def['required'] === true && ( !isset( $attributes[$key] ) || $attributes[$key] === '' ) )
			{
				$errors[$key] = sprintf( 'Configuration for "%1$s" is missing', $key );
				continue;
			}

			if( isset( $attributes[$key] ) )
			{
				switch( $def['type'] )
				{
					case 'boolean':
						if( !is_string( $attributes[$key] ) || $attributes[$key] !== '0' && $attributes[$key] !== '1' ) {
							$errors[$key] = sprintf( 'Not a true/false value' ); continue 2;
						}
						break;
					case 'string':
					case 'text':
						if( is_string( $attributes[$key] ) === false ) {
							$errors[$key] = sprintf( 'Not a string' ); continue 2;
						}
						break;
					case 'integer':
						if( ctype_digit( $attributes[$key] ) === false ) {
							$errors[$key] = sprintf( 'Not an integer number' ); continue 2;
						}
						break;
					case 'number':
						if( is_numeric( $attributes[$key] ) === false ) {
							$errors[$key] = sprintf( 'Not a number' ); continue 2;
						}
						break;
					case 'date':
						$pattern = '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/';
						if( !is_string( $attributes[$key] ) || preg_match( $pattern, $attributes[$key] ) !== 1 ) {
							$errors[$key] = sprintf( 'Not a date' ); continue 2;
						}
						break;
					case 'datetime':
						$pattern = '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:[0-5][0-9](:[0-5][0-9])?$/';
						if( !is_string( $attributes[$key] ) || preg_match( $pattern, $attributes[$key] ) !== 1 ) {
							$errors[$key] = sprintf( 'Not a date and time' ); continue 2;
						}
						break;
					case 'time':
						$pattern = '/^([0-2])?[0-9]:[0-5][0-9](:[0-5][0-9])?$/';
						if( !is_string( $attributes[$key] ) || preg_match( $pattern, $attributes[$key] ) !== 1 ) {
							$errors[$key] = sprintf( 'Not a time' ); continue 2;
						}
						break;
					case 'list':
					case 'select':
						if( !is_array( $def['default'] ) || !isset( $def['default'][$attributes[$key]] )
							&& !in_array( $attributes[$key], $def['default'] )
						) {
							$errors[$key] = sprintf( 'Not a listed value' ); continue 2;
						}
						break;
					case 'map':
						if( !is_array( $attributes[$key] ) ) {
							$errors[$key] = sprintf( 'Not a key/value map' ); continue 2;
						}
						break;
					default:
						throw new \Aimeos\MShop\Service\Exception( sprintf( 'Invalid type "%1$s"', $def['type'] ) );
				}
			}

			$errors[$key] = null;
		}

		return $errors;
	}


	/**
	 * Returns the criteria attribute items for the backend configuration
	 *
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] List of criteria attribute items
	 */
	protected function getConfigItems( array $configList )
	{
		$list = [];

		foreach( $configList as $key => $config ) {
			$list[$key] = new \Aimeos\MW\Criteria\Attribute\Standard( $config );
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
		$srvconfig = $this->getServiceItem()->getConfig();

		foreach( (array) $keys as $key )
		{
			if( isset( $srvconfig[$key] ) ) {
				return $srvconfig[$key];
			}

			if( isset( $this->beGlobalConfig[$key] ) ) {
				return $this->beGlobalConfig[$key];
			}
		}

		return $default;
	}


	/**
	 * Returns the context item.
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface Context item
	 */
	protected function getContext()
	{
		return $this->context;
	}


	/**
	 * Returns the calculated amount of the price item
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Price item
	 * @param boolean $costs Include costs per item
	 * @param boolean $tax Include tax
	 * @return string Formatted money amount
	 */
	protected function getAmount( \Aimeos\MShop\Price\Item\Iface $price, $costs = true, $tax = true )
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

		return number_format( $amount, 2, '.', '' );
	}


	/**
	 * Returns the first object of the decorator stack
	 *
	 * @return \Aimeos\MShop\Plugin\Provider\Iface First object of the decorator stack
	 */
	protected function getObject()
	{
		if( $this->object !== null ) {
			return $this->object;
		}

		return $this;
	}


	/**
	 * Returns the order item for the given ID.
	 *
	 * @param string $id Unique order ID
	 * @return \Aimeos\MShop\Order\Item\Iface $item Order object
	 */
	protected function getOrder( $id )
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'order' );

		$search = $manager->createSearch( true );
		$expr = [
			$search->getConditions(),
			$search->compare( '==', 'order.id', $id ),
			$search->compare( '==', 'order.base.service.code', $this->serviceItem->getCode() ),
		];
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new \Aimeos\MShop\Service\Exception( sprintf( 'No order for ID "%1$s" found', $id ) );
		}

		return $item;
	}


	/**
	 * Returns the base order which is equivalent to the basket.
	 *
	 * @param string $baseId Order base ID stored in the order item
	 * @param integer $parts Bitmap of the basket parts that should be loaded
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Basket, optional with addresses, products, services and coupons
	 */
	protected function getOrderBase( $baseId, $parts = \Aimeos\MShop\Order\Item\Base\Base::PARTS_SERVICE )
	{
		return \Aimeos\MShop\Factory::createManager( $this->context, 'order/base' )->load( $baseId, $parts );
	}


	/**
	 * Saves the order item.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $item Order object
	 * @return \Aimeos\MShop\Order\Item\Iface Order object including the generated ID
	 */
	protected function saveOrder( \Aimeos\MShop\Order\Item\Iface $item )
	{
		return \Aimeos\MShop\Factory::createManager( $this->context, 'order' )->saveItem( $item );
	}


	/**
	 * Returns the service related data from the customer account if available
	 *
	 * @param string $customerId Unique customer ID the service token belongs to
	 * @param string $type Type of the value that should be returned
	 * @return string|null Service data or null if none is available
	 */
	protected function getCustomerData( $customerId, $type )
	{
		if( $customerId == null ) {
			return;
		}

		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'customer' );
		$item = $manager->getItem( $customerId, ['service'] );
		$serviceId = $this->getServiceItem()->getId();

		if( ( $listItem = $item->getListItem( 'service', 'default', $serviceId ) ) !== null )
		{
			$config = $listItem->getConfig();
			return ( isset( $config[$type] ) ? $config[$type] : null );
		}
	}


	/**
	 * Saves the base order which is equivalent to the basket and its dependent objects.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Order base object with associated items
	 * @param integer $parts Bitmap of the basket parts that should be stored
	 */
	protected function saveOrderBase( \Aimeos\MShop\Order\Item\Base\Iface $base, $parts = \Aimeos\MShop\Order\Item\Base\Base::PARTS_SERVICE )
	{
		\Aimeos\MShop\Factory::createManager( $this->context, 'order/base' )->store( $base, $parts );
	}


	/**
	 * Sets the attributes in the given service item.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Service\Iface $orderServiceItem Order service item that will be added to the basket
	 * @param array $attributes Attribute key/value pairs entered by the customer during the checkout process
	 * @param string $type Type of the configuration values (delivery or payment)
	 */
	protected function setAttributes( \Aimeos\MShop\Order\Item\Base\Service\Iface $orderServiceItem, array $attributes, $type )
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/service/attribute' );

		foreach( $attributes as $key => $value )
		{
			$item = $manager->createItem();
			$item->setCode( $key );
			$item->setValue( $value );
			$item->setType( $type );

			$orderServiceItem->setAttributeItem( $item );
		}
	}


	/**
	 * Adds the service data to the customer account if available
	 *
	 * @param string $customerId Unique customer ID the service token belongs to
	 * @param string $type Type of the value that should be added
	 * @param string|array $data Service data to store
	 */
	protected function setCustomerData( $customerId, $type, $data )
	{
		if( $customerId == null ) {
			return;
		}

		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'customer' );
		$item = $manager->getItem( $customerId, ['service'] );
		$serviceId = $this->getServiceItem()->getId();

		if( ( $listItem = $item->getListItem( 'service', 'default', $serviceId, false ) ) === null )
		{
			$listManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'customer/lists' );
			$listTypeManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'customer/lists/type' );

			$listItem = $listManager->createItem();
			$listItem->setTypeId( $listTypeManager->findItem( 'default', [], 'service' )->getId() );
			$listItem->setRefId( $serviceId );
		}

		$listItem->setConfig( array_merge( $listItem->getConfig(), [$type => $data] ) );
		$manager->saveItem( $item->addListItem( 'service', $listItem ) );
	}


	/**
	 * Returns the public holidays in ISO format
	 *
	 * @param string $list Comma separated list of public holidays in YYYY-MM-DD format
	 * @return array List of dates in YYYY-MM-DD format
	 * @throws \Aimeos\MShop\Service\Exception If the given holiday string is in the wrong format and can't be processed
	 */
	private function getPublicHolidays( $list )
	{
		$holidays = [];

		if( is_string( $list ) && $list !== '' )
		{
			$holidays = explode( ',', str_replace( ' ', '', $list ) );

			if( sort( $holidays ) === false ) {
				throw new \Aimeos\MShop\Service\Exception( sprintf( 'Unable to sort public holidays: "%1$s"', $list ) );
			}

			$holidays = array_flip( $holidays );
		}

		return $holidays;
	}
}
