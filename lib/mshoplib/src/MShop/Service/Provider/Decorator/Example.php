<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Service
 */


/**
 * Example decorator for service provider.
 *
 * @package MShop
 * @subpackage Service
 */
class MShop_Service_Provider_Decorator_Example
extends MShop_Service_Provider_Decorator_Abstract
{

	private $_beConfig = array(
		'country' => array(
			'code' => 'country',
			'internalcode'=> 'country',
			'label'=> 'Country',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> true,
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
	 * Checks if payment provider can be used based on the basket content.
	 * Checks for country, currency, address, scoring, etc. should be implemented in separate decorators
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket object
	 * @return boolean True if payment provider can be used, false if not
	 */
	public function isAvailable( MShop_Order_Item_Base_Interface $basket )
	{
		if ( $basket->getLocale()->getLanguageId() === 'en' ) {
			return $this->_getProvider()->isAvailable( $basket );
		}
		return false;
	}
}