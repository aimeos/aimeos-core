<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * 'Category-limiting decorator for service providers.
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
			'internalcode'=> 'category.include',
			'label'=> 'Code of allowed category and sub-categories for the service item',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> false,
		),
		'category.exclude' => array(
			'code' => 'category.exclude',
			'internalcode'=> 'category.exclude',
			'label'=> 'Code of category and sub-categories not allowed for the service item',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
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
	 * Checks if the products are withing the allowed code is allowed for the service provider.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object
	 * @return boolean True if payment provider can be used, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $basket )
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
	 * @return boolean|null True if one catalog code is part of the config, false if not, null for no configuration
	 */
	protected function checkCategories( array $catalogIds, $key )
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
	protected function getCatalogIds( array $catalogCodes )
	{
		$catalogManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'catalog' );

		$search = $catalogManager->createSearch( true );
		$expr = array(
			$search->compare( '==', 'catalog.code', $catalogCodes ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		return array_keys( $catalogManager->searchItems( $search ) );
	}


	/**
	 * Returns the catalog IDs from the given catalog item and its children
	 *
	 * @param \Aimeos\MShop\Catalog\Item\Iface $catalogItem Catalog node object
	 * @return array List of catalog IDs
	 */
	protected function getNodeCatalogIds( \Aimeos\MShop\Catalog\Item\Iface $catalogItem )
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
	protected function getProductIds( \Aimeos\MShop\Order\Item\Base\Iface $basket )
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
	protected function getRefCatalogIds( array $productIds )
	{
		$catalogListsManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'catalog/lists' );

		$search = $catalogListsManager->createSearch();
		$expr = array(
			$search->compare( '==', 'catalog.lists.refid', $productIds ),
			$search->compare( '==', 'catalog.lists.domain', 'product' ),
			$search->compare( '==', 'catalog.lists.type.code', 'default' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		return array_keys( $catalogListsManager->aggregate( $search, 'catalog.lists.parentid' ) );
	}


	/**
	 * Returns the catalog codes for the given catalog IDs
	 *
	 * @param array $catalogIds List of catalog IDs
	 * @return array List of catalog codes
	 */
	protected function getTreeCatalogIds( array $catalogIds )
	{
		$ids = [];
		$catalogManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'catalog' );

		foreach( $catalogIds as $catId )
		{
			$treeNode = $catalogManager->getTree( $catId );
			$ids = array_merge( $ids, $this->getNodeCatalogIds( $treeNode ) );
		}

		return array_unique( $ids );
	}
}
