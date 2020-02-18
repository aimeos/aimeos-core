<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2020
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
	 * Returns the maximum rebate allowed when using the provider
	 *
	 * The result depends on the configured restrictions and it must be less or
	 * equal to the passed value.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 * @param float Rebate value that would be applied to the basket
	 * @return float New rebate value that will be used
	 */
	public function calcRebate( \Aimeos\MShop\Order\Item\Base\Iface $base, float $rebate ) : float
	{
		if( ( $value = $this->getConfigValue( 'category.only' ) ) == true )
		{
			$sum = 0;
			$prodIds = $refIds = [];

			foreach( $base->getProducts() as $product ) {
				$prodIds[$product->getProductId()][] = $product;
			}

			$manager = \Aimeos\MShop::create( $this->getContext(), 'catalog' );
			$listManager = \Aimeos\MShop::create( $this->getContext(), 'catalog/lists' );

			$catItem = $manager->findItem( $this->getConfigValue( 'category.code' ) );

			$search = $listManager->createSearch( true )->setSlice( 0, count( $prodIds ) );
			$search->setConditions( $search->combine( '&&', [
				$search->compare( '==', 'catalog.lists.parentid', $catItem->getId() ),
				$search->compare( '==', 'catalog.lists.refid', array_keys( $prodIds ) ),
				$search->compare( '==', 'catalog.lists.type', ['default', 'promotion'] ),
				$search->compare( '==', 'catalog.lists.domain', 'product' ),
				$search->getConditions()
			] ) );

			foreach( $listManager->searchItems( $search ) as $listItem )
			{
				if( isset( $prodIds[$listItem->getRefId()] ) )
				{
					foreach( $prodIds[$listItem->getRefId()] as $product ) {
						$sum += ( $product->getPrice()->getValue() + $product->getPrice()->getCosts() ) * $product->getQuantity();
					}
				}
			}

			$rebate = $sum < $rebate ? $sum : $rebate;
		}

		return $this->getProvider()->calcRebate( $base, $rebate );
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
			$search = $manager->createSearch( true )->setSlice( 0, 1 );
			$expr = [];

			foreach( $base->getProducts() as $product )
			{
				$func = $search->createFunction( 'catalog:has', ['product', 'default', $product->getProductId()] );
				$expr[] = $search->compare( '!=', $func, null );
			}

			$search->setConditions( $search->combine( '&&', [
				$search->compare( '==', 'catalog.code', explode( ',', $value ) ),
				$search->combine( '||', $expr ),
				$search->getConditions(),
			] ) );

			if( $manager->searchItems( $search )->isEmpty() ) {
				return false;
			}
		}

		return parent::isAvailable( $base );
	}
}
