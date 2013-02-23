<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Frontend
 * @version $Id: Default.php 1364 2012-10-31 13:19:53Z doleiynyk $
 */


/**
 * Default implementation of the catalog frontend controller.
 *
 * @package Controller
 * @subpackage Frontend
 */
class Controller_Frontend_Catalog_Default
	extends Controller_Frontend_Abstract
	implements Controller_Frontend_Catalog_Interface
{
	private $_manager;
	private $_indexManager;


	/**
	 * Initializes the frontend controller.
	 *
	 * @param MShop_Context_Item_Interface $context Object storing the required instances for manaing databases
	 *  connections, logger, session, etc.
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );

		$this->_manager = MShop_Catalog_Manager_Factory::createManager( $context );
		$this->_indexManager = $this->_manager->getSubManager( 'index' );
	}


	/**
	 * Returns the list of categries that are in the path to the root node including the one specified by its ID.
	 *
	 * @param integer $id Category ID to start from, null for root node
	 * @param array $domains Domain names of items that are associated with the categories and that should be fetched too
	 * @return array Associative list of items implementing MShop_Catalog_Item_Interface with their IDs as keys
	 */
	public function getCatalogPath( $id, $domains = array( 'text', 'media' ) )
	{
		return $this->_manager->getPath( $id, $domains );
	}


	/**
	 * Returns the hierarchical catalog tree starting from the given ID.
	 *
	 * @param integer|null $id Category ID to start from, null for root node
	 * @param array $domains Domain names of items that are associated with the categories and that should be fetched too
	 * @param integer $level Constant from MW_Tree_Manager_Abstract for the depth of the returned tree, LEVEL_ONE for
	 * 	specific node only, LEVEL_LIST for node and all direct child nodes, LEVEL_TREE for the whole tree
	 * @return MShop_Catalog_Item_Interface Catalog node, maybe with children depending on the level constant
	 */
	public function getCatalogTree( $id = null, $domains = array( 'text', 'media' ), $level = MW_Tree_Manager_Abstract::LEVEL_TREE )
	{
		return $this->_manager->getTree( $id, $domains, $level );
	}


	/**
	 * Returns a product filter for the given category ID.
	 *
	 * @param integer $catid ID of the category to get the product list from
	 * @param string|null $sort Sortation of the product list like "name", "code", "price" and "position", null for no sortation
	 * @param string $direction Sort direction of the product list ("+", "-")
	 * @param integer $start Position in the list of found products where to begin retrieving the items
	 * @param integer $size Number of products that should be returned
	 * @param string $listtype Type of the product list, e.g. default, promotion, etc.
	 * @param string $domain Text associated to the domain e.g. product, attribute
	 * @return MW_Common_Criteria_Interface Criteria object containing the conditions for searching
	 */
	public function createProductFilterByCategory( $catid, $sort = null, $direction = '+', $start = 0, $size = 100, $listtype = 'default' )
	{
		$expr = $sortations = array();
		$search = $this->_indexManager->createSearch( true );

		$expr[] = $search->compare( '==', 'catalog.index.catalog.id', $catid );

		switch( $sort )
		{
			case 'code':
				$sortations[] = $search->sort( $direction, 'product.code' );
				break;

			case 'name':
				$langid = $this->_getContext()->getLocale()->getLanguageId();

				$cmpfunc = $search->createFunction( 'catalog.index.text.value', array( $listtype, $langid, 'name', 'product' ) );
				$expr[] = $search->compare( '>=', $cmpfunc, '' );

				$sortfunc = $search->createFunction( 'sort:catalog.index.text.value', array( $listtype, $langid, 'name' ) );
				$sortations[] = $search->sort( $direction, $sortfunc );
				break;

			case 'position':
				$cmpfunc = $search->createFunction( 'catalog.index.catalog.position', array( $listtype, $catid ) );
				$expr[] = $search->compare( '>=', $cmpfunc, 0 );

				$sortfunc = $search->createFunction( 'sort:catalog.index.catalog.position', array( $listtype, $catid ) );
				$sortations[] = $search->sort( $direction, $sortfunc );
				break;

			case 'price':
				$currencyid = $this->_getContext()->getLocale()->getCurrencyId();

				$cmpfunc = $search->createFunction( 'catalog.index.price.value', array( $listtype, $currencyid, 'default' ) );
				$expr[] = $search->compare( '>=', $cmpfunc, '0.00' );

				$sortfunc = $search->createFunction( 'sort:catalog.index.price.value', array( $listtype, $currencyid, 'default' ) );
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
	 * Returns product filter for the given search string.
	 *
	 * @param string $input Search string entered by the user
	 * @param string|null $sort Sortation of the product list like "name", "price" and "relevance", null for no sortation
	 * @param string $direction Sort direction of the product list ("+", "-")
	 * @param integer $start Position in the list of found products where to begin retrieving the items
	 * @param integer $size Number of products that should be returned
	 * @param string $listtype List type of the text associated to the product, usually "default"
	 * @param string $domain Text associated to the domain e.g. product, attribute
	 * @return MW_Common_Criteria_Interface Criteria object containing the conditions for searching
	 */
	public function createProductFilterByText( $input, $sort = null, $direction = '+', $start = 0, $size = 100, $listtype = 'default' )
	{
		$locale = $this->_getContext()->getLocale();
		$langid = $locale->getLanguageId();

		$search = $this->_indexManager->createSearch( true );

		$expr = array( $search->compare( '>', $search->createFunction( 'catalog.index.text.relevance', array( $listtype, $langid, $input ) ), 0 ) );

		$sortations = array();

		switch( $sort )
		{
			case 'code':
				$sortations[] = $search->sort( $direction, 'product.code' );
				break;

			case 'name':
				$cmpfunc = $search->createFunction( 'catalog.index.text.value', array( $listtype, $langid, 'name', 'product' ) );
				$expr[] = $search->compare( '>=', $cmpfunc, '' );

				$sortfunc = $search->createFunction( 'sort:catalog.index.text.value', array( $listtype, $langid, 'name' ) );
				$sortations[] = $search->sort( $direction, $sortfunc );
				break;

			case 'price':
				$currencyid = $locale->getCurrencyId();

				$cmpfunc = $search->createFunction( 'catalog.index.price.value', array( $listtype, $currencyid, 'default' ) );
				$expr[] = $search->compare( '>=', $cmpfunc, '0.00' );

				$sortfunc = $search->createFunction( 'sort:catalog.index.price.value', array( $listtype, $currencyid, 'default' ) );
				$sortations[] = $search->sort( $direction, $sortfunc );
				break;

			case 'relevance':
				$sortfunc = $search->createFunction( 'sort:catalog.index.text.relevance', array( $listtype, $langid, $input ) );
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
	 * Returns a product list filtered by the given criteria object.
	 *
	 * @param MW_Common_Criteria_Interface $filter Critera object which contains the filter conditions
	 * @param integer &$total Parameter where the total number of found products will be stored in
	 * @param array $domains Domain names of items that are associated with the products and that should be fetched too
	 * @return array Ordered list of product items implementing MShop_Product_Item_Interface
	 */
	public function getProductList( MW_Common_Criteria_Interface $filter, &$total = null, $domains = array( 'media', 'price', 'text' ) )
	{
		return $this->_indexManager->searchItems( $filter, $domains, $total );
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
	 * @param string $domain Text associated to the domain e.g. product, attribute
	 * @return MW_Common_Criteria_Interface Criteria object containing the conditions for searching
	 */
	public function createTextFilter( $input, $sort = null, $direction = '+', $start = 0, $size = 25, $listtype = 'default', $type = 'name' )
	{
		$locale = $this->_getContext()->getLocale();
		$langid = $locale->getLanguageId();

		$search = $this->_indexManager->getSubManager( 'text' )->createSearch( true );

		$expr = array(
			$search->compare( '>', $search->createFunction( 'catalog.index.text.relevance', array( $listtype, $langid, $input ) ), 0 ),
			$search->compare( '>', $search->createFunction( 'catalog.index.text.value', array( $listtype, $langid, $type, 'product' ) ), '' ),
		);

		$sortations = array();

		switch( $sort )
		{
			case 'name':
				$cmpfunc = $search->createFunction( 'catalog.index.text.value', array( $listtype, $langid, 'name', 'product' ) );
				$expr[] = $search->compare( '>=', $cmpfunc, '' );

				$sortfunc = $search->createFunction( 'sort:catalog.index.text.value', array( $listtype, $langid, 'name' ) );
				$sortations[] = $search->sort( $direction, $sortfunc );
				break;

			case 'relevance':
				$sortfunc = $search->createFunction( 'sort:catalog.index.text.relevance', array( $listtype, $langid, $input ) );
				$sortations[] = $search->sort( $direction, $sortfunc );
				break;
		}

		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSortations( $sortations );
		$search->setSlice( $start, $size );

		return $search;
	}


	/**
	 * Returns an list of product text strings matched by the filter.
	 *
	 * @param MW_Common_Criteria_Interface $filter Critera object which contains the filter conditions
	 * @return array Associative list of the product ID as key and the product text as value
	 */
	public function getTextList( MW_Common_Criteria_Interface $filter )
	{
		return $this->_indexManager->getSubManager( 'text' )->searchTexts( $filter );
	}
}
