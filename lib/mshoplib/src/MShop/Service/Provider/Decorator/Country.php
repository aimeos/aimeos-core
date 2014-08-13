<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Service
 */


/**
 * Country-limiting decorator for service providers.
 *
 * @package MShop
 * @subpackage Service
 */
class MShop_Service_Provider_Decorator_Country
	extends MShop_Service_Provider_Decorator_Abstract
{
	private $_beConfig = array(
		'country.billing-include' => array(
			'code' => 'country.billing-include',
			'internalcode'=> 'country.billing-include',
			'label'=> 'List of countries allowed for the billing address',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> false,
		),
		'country.billing-exclude' => array(
			'code' => 'country.billing-exclude',
			'internalcode'=> 'country.billing-exclude',
			'label'=> 'List of countries not allowed for the billing address',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> false,
		),
		'country.delivery-include' => array(
			'code' => 'country.delivery-include',
			'internalcode'=> 'country.delivery-include',
			'label'=> 'List of countries allowed for the delivery address',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> false,
		),
		'country.delivery-exclude' => array(
			'code' => 'country.delivery-exclude',
			'internalcode'=> 'country.delivery-exclude',
			'label'=> 'List of countries not allowed for the delivery address',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> false,
		),
	);


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigBE( array $attributes )
	{
		$error = $this->_getProvider()->checkConfigBE( $attributes );
		$error += $this->_checkConfig( $this->_beConfig, $attributes );

		return $error;
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing MW_Common_Critera_Attribute_Interface
	 */
	public function getConfigBE()
	{
		$list = $this->_getProvider()->getConfigBE();

		foreach( $this->_beConfig as $key => $config ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default( $config );
		}

		return $list;
	}


	/**
	 * Checks if the country code is allowed for the service provider.
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket object
	 * @return boolean True if payment provider can be used, false if not
	 */
	public function isAvailable( MShop_Order_Item_Base_Interface $basket )
	{
		$addresses = $basket->getAddresses();

		$paymentType = MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT;
		$deliveryType = MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY;


		if( isset( $addresses[$deliveryType] ) )
		{
			$code = strtoupper( $addresses[$deliveryType]->getCountryId() );

			if( $this->_checkCountryCode( $code, 'country.delivery-include' ) === false
				|| $this->_checkCountryCode( $code, 'country.delivery-exclude' ) === true
			) {
				return false;
			}
		}
		else if( isset( $addresses[$paymentType] ) ) // use billing address if no delivery address is available
		{
			$code = strtoupper( $addresses[$paymentType]->getCountryId() );

			if( $this->_checkCountryCode( $code, 'country.delivery-include' ) === false
				|| $this->_checkCountryCode( $code, 'country.delivery-exclude' ) === true
			) {
				return false;
			}
		}

		if( isset( $addresses[$paymentType] ) )
		{
			$code = strtoupper( $addresses[$paymentType]->getCountryId() );

			if( $this->_checkCountryCode( $code, 'country.billing-include' ) === false
				|| $this->_checkCountryCode( $code, 'country.billing-exclude' ) === true
			) {
				return false;
			}
		}

		return $this->_getProvider()->isAvailable( $basket );
	}


	/**
	 * Checks if the country code is in the list of codes specified by the given key
	 *
	 * @param string $code Two letter ISO country code in upper case
	 * @param string $key Configuration key referring to the country code configuration
	 */
	protected function _checkCountryCode( $code, $key )
	{
		if( ( $str = $this->_getConfigValue( array( $key ) ) ) === null ) {
			return null;
		}

		return in_array( $code, explode( ',', str_replace( ' ', '', strtoupper( $str ) ) ) );
	}
}