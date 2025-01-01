<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2025
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
	private array $beConfig = array(
		'category.include' => array(
			'code' => 'category.include',
			'internalcode' => 'category.include',
			'label' => 'Code of allowed category and sub-categories for the service item',
			'default' => '',
			'required' => false,
		),
		'category.exclude' => array(
			'code' => 'category.exclude',
			'internalcode' => 'category.exclude',
			'label' => 'Code of category and sub-categories not allowed for the service item',
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
	 * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
	{
		return array_replace( parent::getConfigBE(), $this->getConfigItems( $this->beConfig ) );
	}


	/**
	 * Checks if ordered products are in the configured categories to display the service provider.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
	 * @return bool True if payment provider can be used, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Iface $basket ) : bool
	{
		$prodIds = $this->getProductIds( $basket );

		if( $this->checkCategories( $prodIds, 'category.include' ) === false
			|| $this->checkCategories( $prodIds, 'category.exclude' ) === true
		) {
			return false;
		}

		return $this->getProvider()->isAvailable( $basket );
	}


	/**
	 * Checks if at least one of the product is in the configured categories
	 *
	 * @param array $prodIds List of product IDs
	 * @param string $key Configuration key (category.include or category.exclude)
	 * @return bool|null True if one catalog code is part of the config, false if not, null for no configuration
	 */
	protected function checkCategories( array $prodIds, string $key ) : ?bool
	{
		if( ( $codes = $this->getConfigValue( $key ) ) == null ) {
			return null;
		}

		$configCatalogIds = $this->getCatalogIds( explode( ',', $codes ) );

		if( empty( $treeCatalogIds = $this->getTreeCatalogIds( $configCatalogIds ) ) ) {
			return false;
		}

		$types = ['default', 'promotion'];
		$manager = \Aimeos\MShop::create( $this->context(), 'product' );

		// Fetch hidden product too (null for filter)
		$filter = $manager->filter( null )->slice( 0, 1 );
		$filter->add( 'product.id', '==', $prodIds )
			->add( $filter->make( 'product:has', ['catalog', $types, $treeCatalogIds] ), '!=', null );

		return !$manager->search( $filter )->isEmpty();
	}


	/**
	 * Returns the catalog IDs for the given catalog codes
	 *
	 * @param array $codes List of catalog codes
	 * @return array List of catalog IDs
	 */
	protected function getCatalogIds( array $codes ) : array
	{
		// Fetch hidden categories too (null for filter)
		$manager = \Aimeos\MShop::create( $this->context(), 'catalog' );
		$filter = $manager->filter( null )->add( ['catalog.code' => $codes] )->slice( 0, count( $codes ) );

		return $manager->search( $filter )->keys()->all();
	}


	/**
	 * Returns the catalog IDs from the given catalog item and its children
	 *
	 * @param \Aimeos\MShop\Catalog\Item\Iface $catalogItem Catalog node object
	 * @return array List of catalog IDs
	 */
	protected function getNodeCatalogIds( \Aimeos\MShop\Catalog\Item\Iface $catalogItem ) : array
	{
		$catalogIds = [$catalogItem->getId()];

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
	 * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object with ordered products included
	 * @return array List of proudct IDs
	 */
	protected function getProductIds( \Aimeos\MShop\Order\Item\Iface $basket ) : array
	{
		$productIds = [];

		foreach( $basket->getProducts() as $product )
		{
			$productIds[] = $product->getProductId();

			if( $parentid = $product->getParentProductId() ) {
				$productIds[] = $parentid;
			}
		}

		return array_unique( $productIds );
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
		$catalogManager = \Aimeos\MShop::create( $this->context(), 'catalog' );

		foreach( $catalogIds as $catId )
		{
			$treeNode = $catalogManager->getTree( $catId );
			$ids = array_merge( $ids, $this->getNodeCatalogIds( $treeNode ) );
		}

		return array_unique( $ids );
	}
}
