<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * Decorator for service providers adding additional costs
 *
 * This decorator interacts with the ServiceUpdate and Autofill basket plugins!
 * If the delivery/payment option isn't available any more, the ServiceUpdate
 * plugin will remove it from the basket and the Autofill plugin will add one
 * of the available options again.
 *
 * @package MShop
 * @subpackage Service
 */
class Weight
	extends \Aimeos\MShop\Service\Provider\Decorator\Base
	implements \Aimeos\MShop\Service\Provider\Decorator\Iface
{
	private array $beConfig = array(
		'weight.min' => array(
			'code' => 'weight.min',
			'internalcode' => 'weight.min',
			'label' => 'Minimum weight of the package',
			'type' => 'number',
			'internaltype' => 'float',
			'default' => '',
			'required' => false,
		),
		'weight.max' => array(
			'code' => 'weight.max',
			'internalcode' => 'weight.max',
			'label' => 'Maximum weight of the package',
			'type' => 'number',
			'internaltype' => 'float',
			'default' => '',
			'required' => false,
		),
	);


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 *    known by the provider but aren't valid
	 */
	public function checkConfigBE( array $attributes ) : array
	{
		$error = $this->getProvider()->checkConfigBE( $attributes );
		$error += $this->checkConfig( $this->beConfig, $attributes );

		return $error;
	}


	/**
	 * Returns the configuration attribute definitions of the provider
	 *
	 * This will generate a list of available fields and rules for the value of
	 * each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
	{
		return array_replace( parent::getConfigBE(), $this->getConfigItems( $this->beConfig ) );
	}


	/**
	 * Checks if the the basket weight is ok for the service provider.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
	 * @return bool True if payment provider can be used, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Iface $basket ) : bool
	{
		if( $this->checkWeightScale( $this->getWeight( $this->getQuantities( $basket ) ) ) === false ) {
			return false;
		}

		return $this->getProvider()->isAvailable( $basket );
	}


	/**
	 * Checks if the country code is in the list of codes specified by the given key
	 *
	 * @param float $basketWeight The basket weight
	 * @return bool True if the current basket weight is within the providers weight range
	 */
	protected function checkWeightScale( float $basketWeight ) : bool
	{
		$min = $this->getConfigValue( array( 'weight.min' ) );
		$max = $this->getConfigValue( array( 'weight.max' ) );

		if( $min !== null && ( (float) $min ) > $basketWeight ) {
			return false;
		}

		if( $max !== null && ( (float) $max ) < $basketWeight ) {
			return false;
		}

		return true;
	}


	/**
	 * Returns the product quantities
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
	 * @return array Associative list of product codes as keys and quantities as values
	 */
	protected function getQuantities( \Aimeos\MShop\Order\Item\Iface $basket ) : array
	{
		$prodMap = [];

		// basket can contain a product several times in different basket items
		foreach( $basket->getProducts() as $orderProduct )
		{
			$code = $orderProduct->getProductCode();
			$prodMap[$code] = ( $prodMap[$code] ?? 0 ) + $orderProduct->getQuantity();

			foreach( $orderProduct->getProducts() as $prodItem ) // calculate bundled products
			{
				$code = $prodItem->getProductCode();
				$prodMap[$code] = ( $prodMap[$code] ?? 0 ) + $prodItem->getQuantity();
			}
		}

		return $prodMap;
	}


	/**
	 * Returns the weight of the products
	 *
	 * @param array $prodMap Associative list of product codes as keys and quantities as values
	 * @return float Sumed up product weight multiplied with its quantity
	 */
	protected function getWeight( array $prodMap ) : float
	{
		$weight = 0;
		$manager = \Aimeos\MShop::create( $this->context(), 'product' );
		$search = $manager->filter()->add( ['product.code' => array_keys( $prodMap )] )->slice( 0, count( $prodMap ) );

		foreach( $manager->search( $search, ['product/property' => ['package-weight']] ) as $product )
		{
			foreach( $product->getProperties( 'package-weight' ) as $value ) {
				$weight += $value * $prodMap[$product->getCode()];
			}
		}

		return $weight;
	}
}
