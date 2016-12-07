<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * Download check decorator for service providers.
 *
 * @package MShop
 * @subpackage Service
 */
class Download
	extends \Aimeos\MShop\Service\Provider\Decorator\Base
	implements \Aimeos\MShop\Service\Provider\Decorator\Iface
{
	private $beConfig = array(
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
		$error = $this->getProvider()->checkConfigBE( $attributes );
		$error += $this->checkConfig( $this->beConfig, $attributes );

		return $error;
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigBE()
	{
		$list = $this->getProvider()->getConfigBE();

		foreach( $this->beConfig as $key => $config ) {
			$list[$key] = new \Aimeos\MW\Criteria\Attribute\Standard( $config );
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
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object
	 * @return boolean True if payment provider can be used, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $basket )
	{
		$val = (bool) $this->getConfigValue( array( 'download.all' ) );

		foreach( $basket->getProducts() as $product )
		{
			if( ((bool) count( $product->getAttributes( 'download' ) )) !== $val ) {
				return !$val;
			}
		}

		return $this->getProvider()->isAvailable( $basket );
	}
}