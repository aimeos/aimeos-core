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
		$productIds = $this->getProductIds( $basket );
		$catalogIds = $this->getCatalogIds( $productIds );
		$catalogCodes = $this->getCatalogCodes( $catalogIds );

		if( $this->checkCategories( $catalogCodes, 'category.include' ) === false
			|| $this->checkCategories( $catalogCodes, 'category.exclude' ) === true
		) {
			return false;
		}

		return $this->getProvider()->isAvailable( $basket );
	}


	/**
	 * Checks if at least one of the given category codes is configured
	 *
	 * @param array $catalogCodes List of category codes
	 * @param string $key Configuration key (category.include or category.exclude)
	 * @return boolean|null True if one catalog code is part of the config, false if not, null for no configuration
	 */
	protected function checkCategories( array $catalogCodes, $key )
	{
		if( ( $codes = $this->getConfigValue( array( $key ) ) ) == null ) {
			return null;
		}

		return ( array_intersect( $catalogCodes, explode( ',', $codes ) ) !== array() );
	}


	/**
	 * Returns the catalog codes for the given catalog IDs
	 *
	 * @param array $catalogIds List of catalog IDs
	 * @return array List of catalog codes
	 */
	protected function getCatalogCodes( array $catalogIds )
	{
		$catalogCodes = array();
		$catalogManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'catalog' );

		foreach( $catalogIds as $catId )
		{
			$treeNode = $catalogManager->getTree( $catId );
			$catalogCodes = array_merge( $catalogCodes, $this->getCatalogCodesFromTree( $treeNode ) );
		}

		return array_unique( $catalogCodes );
	}


	/**
	 * Returns the catalog codes from the given catalog item and its children
	 *
	 * @param \Aimeos\MShop\Catalog\Item\Iface $catalogItem Catalog node object
	 * @return array List of catalog codes
	 */
	protected function getCatalogCodesFromTree( \Aimeos\MShop\Catalog\Item\Iface $catalogItem )
	{
		$codes = array( $catalogItem->getCode() );

		foreach( $catalogItem->getChildren() as $childNode ) {
			$codes = array_merge( $codes, $this->getCatalogCodesFromTree( $childNode ) );
		}

		return $codes;
	}


	/**
	 * Returns the catalog IDs for the given product IDs
	 *
	 * @param array $productIds List of product IDs
	 * @return array List of catalog IDs
	 */
	protected function getCatalogIds( array $productIds )
	{
		$catalogListsManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'catalog/lists' );

		$search = $catalogListsManager->createSearch();
		$expr = array(
			$search->compare( '==', 'catalog.lists.refid', $productIds ),
			$search->compare( '==', 'catalog.lists.domain', 'product' ),
			$search->compare( '==', 'catalog.lists.type.code', 'default' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $catalogListsManager->aggregate( $search, 'catalog.lists.parentid' );

		return array_keys( $result );
	}


	/**
	 * Returns the products IDs from the products in the basket
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object with ordered products included
	 * @return array List of proudct IDs
	 */
	protected function getProductIds( \Aimeos\MShop\Order\Item\Base\Iface $basket )
	{
		$productIds = array();

		foreach( $basket->getProducts() as $product ) {
			$productIds[] = $product->getProductId();
		}

		return $productIds;
	}
}
