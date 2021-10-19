<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2021
 * @package MShop
 * @subpackage Coupon
 */


namespace Aimeos\MShop\Coupon\Provider\Decorator;


/**
 * Category decorator for coupon provider.
 *
 * @package MShop
 * @subpackage Coupon
 */
class Category
	extends \Aimeos\MShop\Coupon\Provider\Decorator\Base
	implements \Aimeos\MShop\Coupon\Provider\Decorator\Iface
{
	private $beConfig = array(
		'category.code' => array(
			'code' => 'category.code',
			'internalcode' => 'category.code',
			'label' => 'Comma separated category codes',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => '',
			'required' => true,
		),
		'category.only' => array(
			'code' => 'category.only',
			'internalcode' => 'category.only',
			'label' => 'Rebate is applied only to products of that category',
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
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 * @return \Aimeos\MShop\Price\Item\Iface New price that should be used
	 */
	public function calcPrice( \Aimeos\MShop\Order\Item\Base\Iface $base ) : \Aimeos\MShop\Price\Item\Iface
	{
		if( $this->getConfigValue( 'category.only' ) == true )
		{
			$prodIds = [];

			foreach( $base->getProducts() as $product ) {
				$prodIds[$product->getProductId()][] = $product;
			}

			$manager = \Aimeos\MShop::create( $this->getContext(), 'catalog' );
			$listManager = \Aimeos\MShop::create( $this->getContext(), 'catalog/lists' );

			$codes = explode( ',', $this->getConfigValue( 'category.code' ) );
			$price = \Aimeos\MShop::create( $this->getContext(), 'price' )->create();

			$filter = $manager->filter( true )->add( ['catalog.code' => $codes] )->slice( 0, count( $codes ) );
			$catIds = $manager->search( $filter )->keys()->toArray();

			$filter = $listManager->filter( true )->slice( 0, count( $prodIds ) )->add( [
				'catalog.lists.parentid' => $catIds,
				'catalog.lists.refid' => array_keys( $prodIds ),
				'catalog.lists.type' => ['default', 'promotion'],
				'catalog.lists.domain' => 'product',
			] );

			foreach( $listManager->search( $filter ) as $listItem )
			{
				if( isset( $prodIds[$listItem->getRefId()] ) )
				{
					foreach( $prodIds[$listItem->getRefId()] as $product ) {
						$price = $price->addItem( $product->getPrice(), $product->getQuantity() );
					}
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
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
	{
		return $this->getConfigItems( $this->beConfig );
	}


	/**
	 * Checks for requirements.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 * @return bool True if the requirements are met, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $base ) : bool
	{
		if( ( $value = $this->getConfigValue( 'category.code' ) ) !== null )
		{
			$manager = \Aimeos\MShop::create( $this->getContext(), 'catalog' );
			$filter = $manager->filter( null )->add( ['catalog.code' => explode( ',', $value )] )->slice( 0, 1 );
			$expr = [];

			foreach( $base->getProducts() as $product )
			{
				$func = $filter->make( 'catalog:has', ['product', 'default', $product->getProductId()] );
				$expr[] = $filter->is( $func, '!=', null );
			}

			if( $manager->search( $filter->add( $filter->or( $expr ) ) )->isEmpty() ) {
				return false;
			}
		}

		return parent::isAvailable( $base );
	}
}
