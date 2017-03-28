<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * Decorator for service providers adding additional costs.
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
			'type' => 'string',
			'internaltype' => 'float',
			'default' => '',
			'required' => false,
		),
		'weight.max' => array(
			'code' => 'weight.max',
			'internalcode' => 'weight.max',
			'label' => 'Maximum weight of the package',
			'type' => 'string',
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
	public function checkConfigBE( array $attributes )
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
	 * @return array List of attribute definitions implementing MW_Common_Critera_Attribute_Interface
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
	 * Checks if the the basket weight is ok for the service provider.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object
	 * @return boolean True if payment provider can be used, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $basket )
	{
		$prodMap = [];

		// basket can contain a product several times in different basket items
		// product IDs are only those of articles, selections and bundles, not of the variants and bundled products
		foreach( $basket->getProducts() as $basketItem )
		{
			$qty = $basketItem->getQuantity();
			$code = $basketItem->getProductCode();
			$prodMap[$code] = ( isset( $prodMap[$code] ) ? $prodMap[$code] + $qty : $qty );

			foreach( $basketItem->getProducts() as $prodItem ) // calculate bundled products
			{
				$qty = $prodItem->getQuantity();
				$code = $prodItem->getProductCode();
				$prodMap[$code] = ( isset( $prodMap[$code] ) ? $prodMap[$code] + $qty : $qty );
			}
		}

		if ($this->checkWeightScale( $this->getWeight( $prodMap ) ) === false) {
			return false;
		}

		return $this->getProvider()->isAvailable( $basket );
	}


	/**
	 * Checks if the country code is in the list of codes specified by the given key
	 *
	 * @param float $basketWeight The basket weight
	 * @return boolean True if the current basket weight is within the providers weight range
	 */
	protected function checkWeightScale( $basketWeight )
	{
		$min = $this->getConfigValue( array( 'weight.min' ) );
		$max = $this->getConfigValue( array( 'weight.max' ) );

		if( $min !== null && ( (float) $min) > $basketWeight ) {
			return false;
		}

		if( $max !== null && ( (float) $max) < $basketWeight ) {
			return false;
		}

		return true;
	}


	/**
	 * Returns the weight of the products
	 *
	 * @param array $prodMap Associative list of product codes as keys and quantities as values
	 * @return double Sumed up product weight multiplied with its quantity
	 */
	protected function getWeight( array $prodMap )
	{
		$weight = 0;
		$prodIds = [];
		$context = $this->getContext();


		$manager = \Aimeos\MShop\Factory::createManager( $context, 'product' );
		$search = $manager->createSearch( true );
		$expr = array(
			$search->compare( '==', 'product.code', array_keys( $prodMap ) ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 0x7fffffff ); // if more than 100 products are in the basket

		foreach( $manager->searchItems( $search ) as $id => $product ) {
			$prodIds[$id] = $product->getCode();
		}


		$propertyManager = \Aimeos\MShop\Factory::createManager( $context, 'product/property' );
		$search = $propertyManager->createSearch( true );
		$expr = array(
			$search->compare( '==', 'product.property.parentid', array_keys( $prodIds ) ),
			$search->compare( '==', 'product.property.type.code', 'package-weight' ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 0x7fffffff ); // if more than 100 products are in the basket

		foreach( $propertyManager->searchItems( $search ) as $property ) {
			$weight += ((float) $property->getValue()) * $prodMap[$prodIds[$property->getParentId()]];
		}


		return (double) $weight;
	}
}