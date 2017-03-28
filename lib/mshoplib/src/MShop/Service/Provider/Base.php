<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
		if( isset( $params['orderid'] ) ) {
			return $this->getOrder( $params['orderid'] );
		}

		return null;
	}


	/**
	 * Sets the communication object for a service provider.
	 *
	 * @param \Aimeos\MW\Communication\Iface $communication Object of communication
	 */
	public function setCommunication( \Aimeos\MW\Communication\Iface $communication )
	{
		$this->communication = $communication;
	}


	/**
	 * Returns the communication object for the service provider.
	 *
	 * @return \Aimeos\MW\Communication\Iface Object for communication
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
						if( $attributes[$key] != '0' && $attributes[$key] != '1' ) {
							$errors[$key] = sprintf( 'Not a true/false value' ); continue 2;
						}
						break;
					case 'string':
						if( is_string( $attributes[$key] ) === false ) {
							$errors[$key] = sprintf( 'Not a string' ); continue 2;
						}
						break;
					case 'integer':
						if( ctype_digit( $attributes[$key] ) === false ) {
							$errors[$key] = sprintf( 'Not an integer number' ); continue 2;
						}
						break;
					case 'decimal': // deprecated
					case 'float': // deprecated
					case 'number':
						if( is_numeric( $attributes[$key] ) === false ) {
							$errors[$key] = sprintf( 'Not a number' ); continue 2;
						}
						break;
					case 'date':
						$pattern = '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/';
						if( preg_match( $pattern, $attributes[$key] ) !== 1 ) {
							$errors[$key] = sprintf( 'Not a date' ); continue 2;
						}
						break;
					case 'datetime':
						$pattern = '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/';
						if( preg_match( $pattern, $attributes[$key] ) !== 1 ) {
							$errors[$key] = sprintf( 'Not a date and time' ); continue 2;
						}
						break;
					case 'time':
						$pattern = '/^[0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/';
						if( preg_match( $pattern, $attributes[$key] ) !== 1 ) {
							$errors[$key] = sprintf( 'Not a date and time' ); continue 2;
						}
						break;
					case 'select':
						if( !is_array( $def['default'] ) || !in_array( $attributes[$key], $def['default'] ) ) {
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
	 * Returns the order item for the given ID.
	 *
	 * @param string $id Unique order ID
	 * @return \Aimeos\MShop\Order\Item\Iface $item Order object
	 */
	protected function getOrder( $id )
	{
		return \Aimeos\MShop\Factory::createManager( $this->context, 'order' )->getItem( $id );
	}


	/**
	 * Returns the base order which is equivalent to the basket.
	 *
	 * @param string $baseId Order base ID stored in the order item
	 * @param integer $parts Bitmap of the basket parts that should be loaded
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Basket, optional with addresses, products, services and coupons
	 */
	protected function getOrderBase( $baseId, $parts = \Aimeos\MShop\Order\Manager\Base\Base::PARTS_SERVICE )
	{
		return \Aimeos\MShop\Factory::createManager( $this->context, 'order/base' )->load( $baseId, $parts );
	}


	/**
	 * Saves the order item.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $item Order object
	 */
	protected function saveOrder( \Aimeos\MShop\Order\Item\Iface $item )
	{
		\Aimeos\MShop\Factory::createManager( $this->context, 'order' )->saveItem( $item );
	}


	/**
	 * Saves the base order which is equivalent to the basket and its dependent objects.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Order base object with associated items
	 * @param integer $parts Bitmap of the basket parts that should be stored
	 */
	protected function saveOrderBase( \Aimeos\MShop\Order\Item\Base\Iface $base, $parts = \Aimeos\MShop\Order\Manager\Base\Base::PARTS_SERVICE )
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
