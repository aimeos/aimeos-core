<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2023
 * @package MShop
 * @subpackage Coupon
 */


namespace Aimeos\MShop\Coupon\Provider\Decorator;


/**
 * Decorator for restricting coupons to suppliers
 *
 * @package MShop
 * @subpackage Coupon
 */
class Supplier
	extends \Aimeos\MShop\Coupon\Provider\Decorator\Base
	implements \Aimeos\MShop\Coupon\Provider\Decorator\Iface
{
	private array $beConfig = array(
		'supplier.code' => array(
			'code' => 'supplier.code',
			'internalcode' => 'supplier.code',
			'label' => 'Comma separated supplier codes',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => '',
			'required' => true,
		),
		'supplier.only' => array(
			'code' => 'supplier.only',
			'internalcode' => 'supplier.only',
			'label' => 'Rebate is applied only to products of that supplier',
			'type' => 'boolean',
			'internaltype' => 'boolean',
			'default' => false,
			'required' => false,
		),
	);


	/**
	 * Returns the price the discount should be applied to
	 *
	 * The result depends on the configured restrictions and it must be less or
	 * equal to the passed price.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $base Basic order of the customer
	 * @return \Aimeos\MShop\Price\Item\Iface New price that should be used
	 */
	public function calcPrice( \Aimeos\MShop\Order\Item\Iface $base ) : \Aimeos\MShop\Price\Item\Iface
	{
		if( $this->getConfigValue( 'supplier.only' ) == true )
		{
			$prodIds = [];
			$context = $this->context();
			$types = ['default', 'promotion'];

			$catManager = \Aimeos\MShop::create( $context, 'supplier' );
			$prodManager = \Aimeos\MShop::create( $context, 'product' );

			$codes = explode( ',', $this->getConfigValue( 'supplier.code', '' ) );
			$filter = $catManager->filter( true )->add( ['supplier.code' => $codes] )->slice( 0, count( $codes ) );

			$catIds = $catManager->search( $filter )->keys()->all();
			$price = \Aimeos\MShop::create( $context, 'price' )->create();

			foreach( $base->getProducts() as $product )
			{
				$prodIds[$product->getProductId()][] = $product;

				if( $parentid = $product->getParentProductId() ) {
					$prodIds[$parentid][] = $product;
				}
			}

			$filter = $prodManager->filter( true )->slice( 0, count( $prodIds ) );
			$filter->add( $filter->is( $filter->make( 'product:has', ['supplier', $types, $catIds] ), '!=', null ) )
				->add( $filter->is( 'product.id', '==', array_keys( $prodIds ) ) );

			foreach( $prodManager->search( $filter ) as $item )
			{
				foreach( $prodIds[$item->getId()] ?? [] as $product ) {
					$price = $price->addItem( $product->getPrice(), $product->getQuantity() );
				}
			}

			return $price;
		}

		return $this->getProvider()->calcPrice( $base );
	}


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigBE( array $attributes ) : array
	{
		return $this->checkConfig( $this->beConfig, $attributes );
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
	{
		return array_replace( parent::getConfigBE(), $this->getConfigItems( $this->beConfig ) );
	}


	/**
	 * Checks for requirements.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $base Basic order of the customer
	 * @return bool True if the requirements are met, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Iface $base ) : bool
	{
		if( ( $codes = $this->getConfigValue( 'supplier.code' ) ) !== null )
		{
			$expr = [];
			$context = $this->context();
			$types = ['default', 'promotion'];

			$catManager = \Aimeos\MShop::create( $context, 'supplier' );
			$prodManager = \Aimeos\MShop::create( $context, 'product' );

			$filter = $catManager->filter( true )->add( ['supplier.code' => explode( ',', $codes )] );
			$catIds = $catManager->search( $filter )->keys()->all();

			$filter = $prodManager->filter( true );

			foreach( $base->getProducts() as $product ) {
				$expr[] = $filter->is( $filter->make( 'product:has', ['supplier', $types, $catIds] ), '!=', null );
			}

			if( $prodManager->search( $filter->add( $filter->or( $expr ) ) )->isEmpty() ) {
				return false;
			}
		}

		return parent::isAvailable( $base );
	}
}
