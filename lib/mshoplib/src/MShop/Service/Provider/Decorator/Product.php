<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * Product-limiting decorator for service providers
 *
 * This decorator interacts with the ServiceUpdate and Autofill basket plugins!
 * If the delivery/payment option isn't available any more, the ServiceUpdate
 * plugin will remove it from the basket and the Autofill plugin will add one
 * of the available options again.
 *
 * @package MShop
 * @subpackage Service
 */
class Product
	extends \Aimeos\MShop\Service\Provider\Decorator\Base
	implements \Aimeos\MShop\Service\Provider\Decorator\Iface
{
	private $beConfig = array(
		'product.include' => array(
			'code' => 'product.include',
			'internalcode' => 'product.include',
			'label' => 'Codes of allowed products for the service item',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => '',
			'required' => false,
		),
		'product.exclude' => array(
			'code' => 'product.exclude',
			'internalcode' => 'product.exclude',
			'label' => 'Codes of the products not allowed for the service item',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => '',
			'required' => false,
		),
	);


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigBE( array $attributes ) : array
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
	public function getConfigBE() : array
	{
		return array_merge( $this->getProvider()->getConfigBE(), $this->getConfigItems( $this->beConfig ) );
	}


	/**
	 * Checks if the products are withing the allowed code is allowed for the service provider.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object
	 * @return bool True if payment provider can be used, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $basket ) : bool
	{
		$codes = $this->getProductCodes( $basket );

		if( $this->checkProducts( $codes, 'product.include' ) === false
			|| $this->checkProducts( $codes, 'product.exclude' ) === true
		) {
			return false;
		}

		return $this->getProvider()->isAvailable( $basket );
	}


	/**
	 * Checks if at least one of the given categories is configured
	 *
	 * @param array $catalogIds List of product IDs
	 * @param string $key Configuration key (product.include or product.exclude)
	 * @return bool|null True if one catalog code is part of the config, false if not, null for no configuration
	 */
	protected function checkProducts( array $prodcodes, string $key ) : ?bool
	{
		if( ( $codes = $this->getConfigValue( array( $key ) ) ) == null ) {
			return null;
		}

		return array_intersect( $prodcodes, explode( ',', $codes ) ) !== [];
	}


	/**
	 * Returns the products codes for the products in the basket
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object with ordered products included
	 * @return array List of product codes
	 */
	protected function getProductCodes( \Aimeos\MShop\Order\Item\Base\Iface $basket ) : array
	{
		$codes = [];

		foreach( $basket->getProducts() as $product )
		{
			$codes[] = $product->getProductCode();

			foreach( $product->getProducts() as $subproduct ) {
				$codes[] = $subproduct->getProductCode();
			}
		}

		return $codes;
	}
}
