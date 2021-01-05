<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * Category-limiting decorator for service providers
 *
 * This decorator interacts with the ServiceUpdate and Autofill basket plugins!
 * If the delivery/payment option isn't available any more, the ServiceUpdate
 * plugin will remove it from the basket and the Autofill plugin will add one
 * of the available options again.
 *
 * @package MShop
 * @subpackage Service
 */
class Category
	extends \Aimeos\MShop\Service\Provider\Decorator\Base
	implements \Aimeos\MShop\Service\Provider\Decorator\Iface
{
	private $beConfig = array(
		'category.include' => array(
			'code' => 'category.include',
			'internalcode' => 'category.include',
			'label' => 'Code of allowed category and sub-categories for the service item',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => '',
			'required' => false,
		),
		'category.exclude' => array(
			'code' => 'category.exclude',
			'internalcode' => 'category.exclude',
			'label' => 'Code of category and sub-categories not allowed for the service item',
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
		$catalogIds = $this->getRefCatalogIds( $this->getProductIds( $basket ) );

		if( $this->checkCategories( $catalogIds, 'category.include' ) === false
			|| $this->checkCategories( $catalogIds, 'category.exclude' ) === true
		) {
			return false;
		}

		return $this->getProvider()->isAvailable( $basket );
	}


	/**
	 * Checks if at least one of the given categories is configured
	 *
	 * @param array $catalogIds List of category IDs
	 * @param string $key Configuration key (category.include or category.exclude)
	 * @return bool|null True if one catalog code is part of the config, false if not, null for no configuration
	 */
	protected function checkCategories( array $catalogIds, string $key ) : ?bool
	{
		if( ( $codes = $this->getConfigValue( array( $key ) ) ) == null ) {
			return null;
		}

		$configCatalogIds = $this->getCatalogIds( explode( ',', $codes ) );
		$treeCatalogIds = $this->getTreeCatalogIds( $configCatalogIds );

		return ( array_intersect( $catalogIds, $treeCatalogIds ) !== [] );
	}


	/**
	 * Returns the catalog IDs for the given catalog codes
	 *
	 * @param array $catalogCodes List of catalog codes
	 * @return array List of catalog IDs
	 */
	protected function getCatalogIds( array $catalogCodes ) : array
	{
		$catalogManager = \Aimeos\MShop::create( $this->getContext(), 'catalog' );

		$search = $catalogManager->filter( true );
		$expr = array(
			$search->compare( '==', 'catalog.code', $catalogCodes ),
			$search->getConditions(),
		);
		$search->setConditions( $search->and( $expr ) );

		return $catalogManager->search( $search )->keys()->toArray();
	}


	/**
	 * Returns the catalog IDs from the given catalog item and its children
	 *
	 * @param \Aimeos\MShop\Catalog\Item\Iface $catalogItem Catalog node object
	 * @return array List of catalog IDs
	 */
	protected function getNodeCatalogIds( \Aimeos\MShop\Catalog\Item\Iface $catalogItem ) : array
	{
		$catalogIds = array( $catalogItem->getId() );

		foreach( $catalogItem->getChildren() as $childNode )
		{
			if( $childNode->getStatus() > 0 ) {
				$catalogIds = array_merge( $catalogIds, $this->getNodeCatalogIds( $childNode ) );
			}
		}

		return $catalogIds;
	}


	/**
	 * Returns the products IDs from the products in the basket
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object with ordered products included
	 * @return array List of proudct IDs
	 */
	protected function getProductIds( \Aimeos\MShop\Order\Item\Base\Iface $basket ) : array
	{
		$productIds = [];

		foreach( $basket->getProducts() as $product ) {
			$productIds[] = $product->getProductId();
		}

		return $productIds;
	}


	/**
	 * Returns the catalog IDs which references the given product IDs
	 *
	 * @param array $productIds List of product IDs
	 * @return array List of catalog IDs
	 */
	protected function getRefCatalogIds( array $productIds ) : array
	{
		if( empty( $productIds ) ) {
			return [];
		}

		$manager = \Aimeos\MShop::create( $this->getContext(), 'catalog' );
		$search = $manager->filter();
		$expr = [];

		foreach( $productIds as $id )
		{
			$func = $search->make( 'catalog:has', ['product', 'default', $id] );
			$expr[] = $search->compare( '!=', $func, null );
		}

		$search->setConditions( $search->or( $expr ) );
		return $manager->search( $search )->keys()->toArray();
	}


	/**
	 * Returns the catalog codes for the given catalog IDs
	 *
	 * @param array $catalogIds List of catalog IDs
	 * @return array List of catalog codes
	 */
	protected function getTreeCatalogIds( array $catalogIds ) : array
	{
		$ids = [];
		$catalogManager = \Aimeos\MShop::create( $this->getContext(), 'catalog' );

		foreach( $catalogIds as $catId )
		{
			$treeNode = $catalogManager->getTree( $catId );
			$ids = array_merge( $ids, $this->getNodeCatalogIds( $treeNode ) );
		}

		return array_unique( $ids );
	}
}
