<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Service
 */


/**
 * Abstract class for all service provider implementations with some default methods.
 *
 * @package MShop
 * @subpackage Service
 */
abstract class MShop_Service_Provider_Abstract
implements MShop_Service_Provider_Interface
{
	private $_context;
	private $_serviceItem;
	private $_communication;
	private $_beGlobalConfig;


	/**
	 * Initializes the service provider object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 * @param MShop_Service_Item_Interface $serviceItem Service item with configuration for the provider
	 */
	public function __construct( MShop_Context_Item_Interface $context, MShop_Service_Item_Interface $serviceItem )
	{
		$this->_context = $context;
		$this->_serviceItem = $serviceItem;
	}


	/**
	 * Returns the price when using the provider.
	 * Usually, this is the lowest price that is available in the service item but can also be a calculated based on
	 * the basket content, e.g. 2% of the value as transaction cost.
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket object
	 * @return MShop_Price_Item_Interface Price item containing the price, shipping, rebate
	 */
	public function calcPrice( MShop_Order_Item_Base_Interface $basket )
	{
		$priceManager = MShop_Factory::createManager( $this->_context, 'price' );
		$prices = $this->_serviceItem->getRefItems( 'price', 'default', 'default' );

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
		return array();
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
		return array();
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing MW_Common_Critera_Attribute_Interface
	 */
	public function getConfigBE()
	{
		return array();
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the frontend.
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket object
	 * @return array List of attribute definitions implementing MW_Common_Critera_Attribute_Interface
	 */
	public function getConfigFE( MShop_Order_Item_Base_Interface $basket )
	{
		return array();
	}


	/**
	 * Returns the service item which also includes the configuration for the service provider.
	 *
	 * @return MShop_Service_Item_Interface Service item
	 */
	public function getServiceItem()
	{
		return $this->_serviceItem;
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
		$this->_beGlobalConfig = $config;
	}


	/**
	 * Checks if payment provider can be used based on the basket content.
	 * Checks for country, currency, address, RMS, etc. -> in separate decorators
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket object
	 * @return boolean True if payment provider can be used, false if not
	 */
	public function isAvailable( MShop_Order_Item_Base_Interface $basket )
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
	 * @param MShop_Order_Item_Interface $order Order invoice object
	 */
	public function query( MShop_Order_Item_Interface $order )
	{
		throw new MShop_Service_Exception( sprintf( 'Method "%1$s" for provider not available', 'query' ) );
	}


	/**
	 * Looks for new update files and updates the orders for which status updates were received.
	 * If batch processing of files isn't supported, this method can be empty.
	 *
	 * @return boolean True if the update was successful, false if async updates are not supported
	 * @throws MShop_Service_Exception If updating one of the orders failed
	 */
	public function updateAsync()
	{
		return false;
	}


	/**
	 * Updates the orders for which status updates were received via direct requests (like HTTP).
	 *
	 * @param mixed $additional Update information whose format depends on the payment provider
	 * @param string|null &$errmsg Error message shown to the user
	 * @return MShop_Order_Item_Interface|null Order item if update was successful, null if the given parameters are not valid for this provider
	 * @throws MShop_Service_Exception If updating one of the orders failed
	 */
	public function updateSync( $additional, &$errmsg = null )
	{
		return null;
	}


	/**
	 * Sets the communication object for a service provider.
	 *
	 * @param MW_Communication_Interface $communication Object of communication
	 */
	public function setCommunication( MW_Communication_Interface $communication )
	{
		$this->_communication = $communication;
	}


	/**
	 * Returns the communication object for the service provider.
	 *
	 * @return MW_Communication_Interface Object for communication
	 */
	protected function _getCommunication()
	{
		if( !isset( $this->_communication ) ) {
			$this->_communication = new MW_Communication_Curl();
		}

		return $this->_communication;
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
	 * @throws MShop_Service_Exception If the given holiday string is in the wrong format and can't be processed
	 */
	protected function _calcDateLimit( $timestamp, $skipdays = 0, $businessOnly = false, $publicHolidays = '' )
	{
		$holidays = array();

		if( is_string( $publicHolidays ) && $publicHolidays !== '' )
		{
			$holidays = explode( ',', str_replace( ' ', '', $publicHolidays ) );

			if( sort( $holidays ) === false ) {
				throw new MShop_Service_Exception( sprintf( 'Unable to sort public holidays: "%1$s"', $publicHolidays ) );
			}

			$holidays = array_flip( $holidays );

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

				if( date( 'N', $ts ) > 5 && !isset( $holidays[ date( 'Y-m-d', $ts ) ] ) ) {
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
	protected function _checkConfig( array $config, array $attributes )
	{
		$errors = array();

		foreach ( $config as $key => $def )
		{
			if( $def['required'] === true && ( !isset( $attributes[$key] ) || $attributes[$key] === '' ) )
			{
				$errors[$key] = sprintf( 'Required attribute "%1$s" in provider configuration not available', $key );
				continue;
			}

			if( isset( $attributes[$key] ) )
			{
				switch( $def['type'] )
				{
					case 'boolean':
						if( $attributes[$key] != '0' && $attributes[$key] != '1' ) {
							$errors[$key] = 'Not a true/false value'; continue 2;
						}
						break;
					case 'string':
						if( is_string( $attributes[$key] ) === false ) {
							$errors[$key] = 'Not a string'; continue 2;
						}
						break;
					case 'integer':
						if( ctype_digit( $attributes[$key] ) === false ) {
							$errors[$key] = 'Not an integer number'; continue 2;
						}
						break;
					case 'decimal':
					case 'float':
						if( is_numeric( $attributes[$key] ) === false ) {
							$errors[$key] = 'Not a number'; continue 2;
						}
						break;
					case 'datetime':
						$pattern = '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$';
						if( preg_match( $pattern, $attributes[$key] ) !== 1 ) {
							$errors[$key] = 'Invalid date and time'; continue 2;
						}
						break;
					case 'map':
						if( !is_array( $attributes[$key] ) ) {
							$errors[$key] = 'Not a valid map'; continue 2;
						}
						break;
					default:
						throw new MShop_Service_Exception( sprintf( 'Invalid characters in attribute for provider configuration. Attribute is not of type "%1$s".', $def['type'] ) );
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
	 * @param array $keys List of key names that should be tested for in the order to test
	 * @param string $default Returned value if the key wasn't was found
	 * @return mixed Value of the first key that matches or null if none was found
	 */
	protected function _getConfigValue( array $keys, $default = null )
	{
		$srvconfig = $this->getServiceItem()->getConfig();

		foreach( $keys as $key )
		{
			if( isset( $srvconfig[$key] ) ) {
				return $srvconfig[$key];
			}

			if( isset( $this->_beGlobalConfig[$key] ) ) {
				return $this->_beGlobalConfig[$key];
			}
		}

		return $default;
	}


	/**
	 * Returns the context item.
	 *
	 * @return MShop_Context_Item_Interface Context item
	 */
	protected function _getContext()
	{
		return $this->_context;
	}


	/**
	 * Returns the order item for the given ID.
	 *
	 * @param string $id Unique order ID
	 * @return MShop_Order_Item_Interface $item Order object
	 */
	protected function _getOrder( $id )
	{
		return MShop_Factory::createManager( $this->_context, 'order' )->getItem( $id );
	}


	/**
	 * Returns the base order which is equivalent to the basket.
	 *
	 * @param string $baseId Order base ID stored in the order item
	 * @param integer $parts Bitmap of the basket parts that should be loaded
	 * @return MShop_Order_Item_Base_Interface Basket, optional with addresses, products, services and coupons
	 */
	protected function _getOrderBase( $baseId, $parts = MShop_Order_Manager_Base_Abstract::PARTS_SERVICE )
	{
		return MShop_Factory::createManager( $this->_context, 'order/base' )->load( $baseId, $parts );
	}


	/**
	 * Saves the order item.
	 *
	 * @param MShop_Order_Item_Interface $item Order object
	 */
	protected function _saveOrder( MShop_Order_Item_Interface $item )
	{
		MShop_Factory::createManager( $this->_context, 'order' )->saveItem( $item );
	}


	/**
	 * Saves the base order which is equivalent to the basket and its dependent objects.
	 *
	 * @param MShop_Order_Item_Base_Interface $base Order base object with associated items
	 * @param integer $parts Bitmap of the basket parts that should be stored
	 */
	protected function _saveOrderBase( MShop_Order_Item_Base_Interface $base, $parts = MShop_Order_Manager_Base_Abstract::PARTS_SERVICE )
	{
		MShop_Factory::createManager( $this->_context, 'order/base' )->store( $base, $parts );
	}


	/**
	 * Sets the attributes in the given service item.
	 *
	 * @param MShop_Order_Item_Base_Service_Interface $orderServiceItem Order service item that will be added to the basket
	 * @param array $attributes Attribute key/value pairs entered by the customer during the checkout process
	 * @param string $type Type of the configuration values (delivery or payment)
	 */
	protected function _setAttributes( MShop_Order_Item_Base_Service_Interface $orderServiceItem, array $attributes, $type )
	{
		$manager = MShop_Factory::createManager( $this->_context, 'order/base/service/attribute' );

		foreach( $attributes as $key => $value )
		{
			$item = $manager->createItem();
			$item->setCode( $key );
			$item->setValue( $value );
			$item->setType( $type );

			$orderServiceItem->setAttributeItem( $item );
		}
	}
}
