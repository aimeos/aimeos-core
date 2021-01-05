<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	private $beConfig = array(
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
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
	{
		return array_merge( $this->getProvider()->getConfigBE(), $this->getConfigItems( $this->beConfig ) );
	}


	/**
	 * Checks if the the basket weight is ok for the service provider.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object
	 * @return bool True if payment provider can be used, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $basket ) : bool
	{
		$prodMap = [];

		// basket can contain a product several times in different basket items
		// product IDs are only those of articles, selections and bundles, not of the variants and bundled products
		foreach( $basket->getProducts() as $orderProduct )
		{
			$qty = $orderProduct->getQuantity();
			$code = $orderProduct->getProductCode();
			$prodMap[$code] = ( isset( $prodMap[$code] ) ? $prodMap[$code] + $qty : $qty );

			foreach( $orderProduct->getProducts() as $prodItem ) // calculate bundled products
			{
				$qty = $prodItem->getQuantity();
				$code = $prodItem->getProductCode();
				$prodMap[$code] = ( isset( $prodMap[$code] ) ? $prodMap[$code] + $qty : $qty );
			}
		}

		if( $this->checkWeightScale( $this->getWeight( $prodMap ) ) === false ) {
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
	 * Returns the weight of the products
	 *
	 * @param array $prodMap Associative list of product codes as keys and quantities as values
	 * @return float Sumed up product weight multiplied with its quantity
	 */
	protected function getWeight( array $prodMap ) : float
	{
		$weight = 0;

		$manager = \Aimeos\MShop::create( $this->getContext(), 'product' );
		$search = $manager->filter( true )->slice( 0, count( $prodMap ) );
		$expr = array(
			$search->compare( '==', 'product.code', array_keys( $prodMap ) ),
			$search->getConditions(),
		);
		$search->setConditions( $search->and( $expr ) );

		foreach( $manager->search( $search, ['product/property'] ) as $product )
		{
			foreach( $product->getPropertyItems( 'package-weight' ) as $property ) {
				$weight += ( (float) $property->getValue() ) * $prodMap[$product->getCode()];
			}
		}

		return (float) $weight;
	}
}
