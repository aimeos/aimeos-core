<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Service
 */


/**
 * Download check decorator for service providers.
 *
 * @package MShop
 * @subpackage Service
 */
class MShop_Service_Provider_Decorator_Download
	extends MShop_Service_Provider_Decorator_Abstract
{
	private $_beConfig = array(
		'download.all' => array(
			'code' => 'download.all',
			'internalcode'=> 'download.all',
			'label'=> 'Check products: "1" = all must be downloads, "0" = at least one is no download',
			'type'=> 'boolean',
			'internaltype'=> 'boolean',
			'default'=> null,
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
			$list[$key] = new MW_Common_Criteria_Attribute_Default( $config );
		}

		return $list;
	}


	/**
	 * Checks if the service provider should be available.
	 *
	 * Tests products in basket if they have a download attribute. The method
	 * returns true if "download.all" is "1" and all products contain the
	 * attribute resp. if "download.all" is "0" and at least one product
	 * contains no download attribute.
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket object
	 * @return boolean True if payment provider can be used, false if not
	 */
	public function isAvailable( MShop_Order_Item_Base_Interface $basket )
	{
		$val = (bool) $this->_getConfigValue( array( 'download.all' ) );

		foreach( $basket->getProducts() as $product )
		{
			if( ((bool) count( $product->getAttributes( 'download' ) )) !== $val ) {
				return !$val;
			}
		}

		return $this->_getProvider()->isAvailable( $basket );
	}
}