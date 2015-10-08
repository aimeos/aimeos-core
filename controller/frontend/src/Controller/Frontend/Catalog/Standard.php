<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Frontend
 */


namespace Aimeos\Controller\Frontend\Catalog;


/**
 * Default implementation of the catalog frontend controller.
 *
 * @package Controller
 * @subpackage Frontend
 */
class Standard
	extends \Aimeos\Controller\Frontend\Base
	implements \Aimeos\Controller\Frontend\Catalog\Iface
{
	/**
	 * Returns the manager for the given name
	 *
	 * @param string $name Name of the manager
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object
	 * @since 2015.08
	 */
	public function createManager( $name )
	{
		return \Aimeos\MShop\Factory::createManager( $this->getContext(), $name );
	}


	/**
	 * Returns the default catalog filter
	 *
	 * @param boolean True to add default criteria, e.g. status > 0
	 * @return \Aimeos\MW\Common\Criteria\Iface Criteria object for filtering
	 * @since 2015.08
	 */
	public function createCatalogFilter( $default = true )
	{
		return \Aimeos\MShop\Factory::createManager( $this->getContext(), 'catalog' )->createSearch( $default );
	}


	/**
	 * Returns the list of categries that are in the path to the root node including the one specified by its ID.
	 *
	 * @param integer $id Category ID to start from, null for root node
	 * @param string[] $domains Domain names of items that are associated with the categories and that should be fetched too
	 * @return array Associative list of items implementing \Aimeos\MShop\Catalog\Item\Iface with their IDs as keys
	 * @since 2015.08
	 */
	public function getCatalogPath( $id, array $domains = array( 'text', 'media' ) )
	{
		return \Aimeos\MShop\Factory::createManager( $this->getContext(), 'catalog' )->getPath( $id, $domains );
	}


	/**
	 * Returns the hierarchical catalog tree starting from the given ID.
	 *
	 * @param integer|null $id Category ID to start from, null for root node
	 * @param string[] $domains Domain names of items that are associated with the categories and that should be fetched too
	 * @param integer $level Constant from \Aimeos\MW\Tree\Manager\Base for the depth of the returned tree, LEVEL_ONE for
	 * 	specific node only, LEVEL_LIST for node and all direct child nodes, LEVEL_TREE for the whole tree
	 * @param \Aimeos\MW\Common\Criteria\Iface|null $search Optional criteria object with conditions
	 * @return \Aimeos\MShop\Catalog\Item\Iface Catalog node, maybe with children depending on the level constant
	 * @since 2015.08
	 */
	public function getCatalogTree( $id = null, array $domains = array( 'text', 'media' ),
		$level = \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE, \Aimeos\MW\Common\Criteria\Iface $search = null )
	{
		return \Aimeos\MShop\Factory::createManager( $this->getContext(), 'catalog' )->getTree( $id, $domains, $level, $search );
	}


	/**
	 * Returns the aggregated count of products for the given key.
	 *
	 * @param \Aimeos\MW\Common\Criteria\Iface $filter Critera object which contains the filter conditions
	 * @param string $key Search key to aggregate for, e.g. "index.attribute.id"
	 * @return array Associative list of key values as key and the product count for this key as value
	 * @since 2015.08
	 */
	public function aggregateIndex( \Aimeos\MW\Common\Criteria\Iface $filter, $key )
	{
		return \Aimeos\MShop\Factory::createManager( $this->getContext(), 'index' )->aggregate( $filter, $key );
	}


	/**
	 * Returns the default index filter.
	 *
	 * @param string|null $sort Sortation of the product list like "name", "code", "price" and "position", null for no sortation
	 * @param string $direction Sort direction of the product list ("+", "-")
	 * @param integer $start Position in the list of found products where to begin retrieving the items
	 * @param integer $size Number of products that should be returned
	 * @param string $listtype Type of the product list, e.g. default, promotion, etc.
	 * @return \Aimeos\MW\Common\Criteria\Iface Criteria object containing the conditions for searching
	 * @since 2015.08
	 */
	public function createIndexFilter( $sort = null, $direction = '+', $start = 0, $size = 100, $listtype = 'default' )
	{
		$sortations = array();
		$context = $this->getContext();

		$search = \Aimeos\MShop\Factory::createManager( $context, 'index' )->createSearch( true );
		$expr = array( $search->compare( '!=', 'index.catalog.id', null ) );

		switch( $sort )
		{
			case 'code':
				$sortations[] = $search->sort( $direction, 'product.code' );
				break;

			case 'name':
				$langid = $context->getLocale()->getLanguageId();

				$cmpfunc = $search->createFunction( 'index.text.value', array( $listtype, $langid, 'name', 'product' ) );
				$expr[] = $search->compare( '>=', $cmpfunc, '' );

				$sortfunc = $search->createFunction( 'sort:index.text.value', array( $listtype, $langid, 'name' ) );
				$sortations[] = $search->sort( $direction, $sortfunc );
				break;

			case 'price':
				$currencyid = $context->getLocale()->getCurrencyId();

				$cmpfunc = $search->createFunction( 'index.price.value', array( $listtype, $currencyid, 'default' ) );
				$expr[] = $search->compare( '>=', $cmpfunc, '0.00' );

				$sortfunc = $search->createFunction( 'sort:index.price.value', array( $listtype, $currencyid, 'default' ) );
				$sortations[] = $search->sort( $direction, $sortfunc );
				break;
		}

		$expr[] = $search->getConditions();

		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSortations( $sortations );
		$search->setSlice( $start, $size );

		return $search;
	}


	/**
	 * Returns the index filter for the given category ID.
	 *
	 * @param integer $catid ID of the category to get the product list from
	 * @param string|null $sort Sortation of the product list like "name", "code", "price" and "position", null for no sortation
	 * @param string $direction Sort direction of the product list ("+", "-")
	 * @param integer $start Position in the list of found products where to begin retrieving the items
	 * @param integer $size Number of products that should be returned
	 * @param string $listtype Type of the product list, e.g. default, promotion, etc.
	 * @return \Aimeos\MW\Common\Criteria\Iface Criteria object containing the conditions for searching
	 * @since 2015.08
	 */
	public function createIndexFilterCategory( $catid, $sort = null, $direction = '+', $start = 0, $size = 100, $listtype = 'default' )
	{
		$search = $this->createIndexFilter( $sort, $direction, $start, $size, $listtype );
		$expr = array( $search->compare( '==', 'index.catalog.id', $catid ) );

		if( $sort === 'relevance' )
		{
			$cmpfunc = $search->createFunction( 'index.catalog.position', array( $listtype, $catid ) );
			$expr[] = $search->compare( '>=', $cmpfunc, 0 );

			$sortfunc = $search->createFunction( 'sort:index.catalog.position', array( $listtype, $catid ) );
			$search->setSortations( array( $search->sort( $direction, $sortfunc ) ) );
		}

		$expr[] = $search->getConditions();
		$search->setConditions( $search->combine( '&&', $expr ) );

		return $search;
	}


	/**
	 * Returns the index filter for the given search string.
	 *
	 * @param string $input Search string entered by the user
	 * @param string|null $sort Sortation of the product list like "name", "price" and "relevance", null for no sortation
	 * @param string $direction Sort direction of the product list ("+", "-")
	 * @param integer $start Position in the list of found products where to begin retrieving the items
	 * @param integer $size Number of products that should be returned
	 * @param string $listtype List type of the text associated to the product, usually "default"
	 * @return \Aimeos\MW\Common\Criteria\Iface Criteria object containing the conditions for searching
	 * @since 2015.08
	 */
	public function createIndexFilterText( $input, $sort = null, $direction = '+', $start = 0, $size = 100, $listtype = 'default' )
	{
		$langid = $this->getContext()->getLocale()->getLanguageId();
		$search = $this->createIndexFilter( $sort, $direction, $start, $size, $listtype );
		$expr = array( $search->compare( '>', $search->createFunction( 'index.text.relevance', array( $listtype, $langid, $input ) ), 0 ) );

		// we don't need to sort by 'sort:index.text.relevance' because it's a boolean match (relevance is either 0 or 1)

		$expr[] = $search->getConditions();
		$search->setConditions( $search->combine( '&&', $expr ) );

		return $search;
	}


	/**
	 * Returns the given search filter with the conditions attached for filtering by category.
	 *
	 * @param \Aimeos\MW\Common\Criteria\Iface $search Criteria object used for product search
	 * @param string $catid Selected category by the user
	 * @return \Aimeos\MW\Common\Criteria\Iface Criteria object containing the conditions for searching
	 * @since 2015.08
	 */
	public function addIndexFilterCategory( \Aimeos\MW\Common\Criteria\Iface $search, $catid )
	{
		$expr = array( $search->compare( '==', 'index.catalog.id', $catid ) );

		$expr[] = $search->getConditions();
		$search->setConditions( $search->combine( '&&', $expr ) );

		return $search;
	}


	/**
	 * Returns the given search filter with the conditions attached for filtering by text.
	 *
	 * @param \Aimeos\MW\Common\Criteria\Iface $search Criteria object used for product search
	 * @param string $input Search string entered by the user
	 * @param string $listtype List type of the text associated to the product, usually "default"
	 * @return \Aimeos\MW\Common\Criteria\Iface Criteria object containing the conditions for searching
	 * @since 2015.08
	 */
	public function addIndexFilterText( \Aimeos\MW\Common\Criteria\Iface $search, $input, $listtype = 'default' )
	{
		$langid = $this->getContext()->getLocale()->getLanguageId();
		$expr = array( $search->compare( '>', $search->createFunction( 'index.text.relevance', array( $listtype, $langid, $input ) ), 0 ) );

		$expr[] = $search->getConditions();
		$search->setConditions( $search->combine( '&&', $expr ) );

		return $search;
	}


	/**
	 * Returns the products from the index filtered by the given criteria object.
	 *
	 * @param \Aimeos\MW\Common\Criteria\Iface $filter Critera object which contains the filter conditions
	 * @param string[] $domains Domain names of items that are associated with the products and that should be fetched too
	 * @param integer &$total Parameter where the total number of found products will be stored in
	 * @return array Ordered list of product items implementing \Aimeos\MShop\Product\Item\Iface
	 * @since 2015.08
	 */
	public function getIndexItems( \Aimeos\MW\Common\Criteria\Iface $filter, array $domains = array( 'media', 'price', 'text' ), &$total = null )
	{
		return \Aimeos\MShop\Factory::createManager( $this->getContext(), 'index' )->searchItems( $filter, $domains, $total );
	}


	/**
	 * Returns the product item for the given ID if it's available
	 *
	 * @param array $ids List of product IDs
	 * @param array $domains Domain names of items that are associated with the products and that should be fetched too
	 * @return string[] List of product items implementing \Aimeos\MShop\Product\Item\Iface
	 * @throws \Aimeos\Controller\Frontend\Catalog\Exception If product isn't available
	 * @since 2015.08
	 */
	public function getProductItems( array $ids, array $domains = array( 'media', 'price', 'text' ) )
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product' );

		$search = $manager->createSearch( true );
		$expr = array(
			$search->compare( '==', 'product.id', $ids ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, count( $ids ) );

		return $manager->searchItems( $search, $domains );
	}


	/**
	 * Returns text filter for the given search string.
	 *
	 * @param string $input Search string entered by the user
	 * @param string|null $sort Sortation of the product list like "name" and "relevance", null for no sortation
	 * @param string $direction Sort direction of the product list ("asc", "desc")
	 * @param integer $start Position in the list of found products where to begin retrieving the items
	 * @param integer $size Number of products that should be returned
	 * @param string $listtype List type of the text associated to the product, usually "default"
	 * @param string $type Type of the text like "name", "short", "long", etc.
	 * @return \Aimeos\MW\Common\Criteria\Iface Criteria object containing the conditions for searching
	 */
	public function createTextFilter( $input, $sort = null, $direction = '+', $start = 0, $size = 25, $listtype = 'default', $type = 'name' )
	{
		$locale = $this->getContext()->getLocale();
		$langid = $locale->getLanguageId();

		$search = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'index/text' )->createSearch( true );

		$expr = array(
			$search->compare( '>', $search->createFunction( 'index.text.relevance', array( $listtype, $langid, $input ) ), 0 ),
			$search->compare( '>', $search->createFunction( 'index.text.value', array( $listtype, $langid, $type, 'product' ) ), '' ),
		);

		$sortations = array();

		switch( $sort )
		{
			case 'name':
				$cmpfunc = $search->createFunction( 'index.text.value', array( $listtype, $langid, 'name', 'product' ) );
				$expr[] = $search->compare( '>=', $cmpfunc, '' );

				$sortfunc = $search->createFunction( 'sort:index.text.value', array( $listtype, $langid, 'name' ) );
				$sortations[] = $search->sort( $direction, $sortfunc );
				break;

			case 'relevance':
				// we don't need to sort by 'sort:index.text.relevance' because it's a boolean match (relevance is either 0 or 1)
		}

		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSortations( $sortations );
		$search->setSlice( $start, $size );

		return $search;
	}


	/**
	 * Returns an list of product text strings matched by the filter.
	 *
	 * @param \Aimeos\MW\Common\Criteria\Iface $filter Critera object which contains the filter conditions
	 * @return array Associative list of the product ID as key and the product text as value
	 */
	public function getTextList( \Aimeos\MW\Common\Criteria\Iface $filter )
	{
		return \Aimeos\MShop\Factory::createManager( $this->getContext(), 'index/text' )->searchTexts( $filter );
	}
}
