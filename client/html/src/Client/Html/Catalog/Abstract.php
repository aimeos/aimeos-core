<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Common methods for the catalog HTML client classes.
 *
 * @package Client
 * @subpackage Html
 */
abstract class Client_Html_Catalog_Abstract
	extends Client_Html_Abstract
{
	static private $_productList;
	static private $_productTotal = 0;


	/**
	 * Adds the conditions for the selected attributes to the given search filter.
	 *
	 * @param MW_View_Interface $view View instance with helper for retrieving the required parameters
	 * @param MW_Common_Criteria_Interface $filter Criteria object for searching
	 */
	protected function _addAttributeFilter( MW_View_Interface $view, MW_Common_Criteria_Interface $filter )
	{
		$attrids = $view->param( 'f-attr-id', array() );

		if( is_string( $attrids ) && $attrids !== '' ) {
			$attrids = explode( ' ', $attrids );
		}

		if( !empty( $attrids ) )
		{
			$func = $filter->createFunction( 'catalog.index.attributeaggregate', array( $attrids ) );
			$expr = array(
				$filter->getConditions(),
				$filter->compare( '==', $func, count( $attrids ) ),
			);
			$filter->setConditions( $filter->combine( '&&', $expr ) );
		}
	}


	/**
	 * Returns the products found for the current parameters.
	 *
	 * @param MW_View_Interface $view View instance with helper for retrieving the required parameters
	 * @return array List of products implementing MShop_Product_Item_Interface
	 */
	protected function _getProductList( MW_View_Interface $view )
	{
		if( self::$_productList === null ) {
			$this->_searchProducts( $view );
		}

		return self::$_productList;
	}


	/**
	 * Returns the filter from the parameters for the product list.
	 *
	 * @param MW_View_Interface $view View instance with helper for retrieving the required parameters
	 * @param boolean $catfilter True to include catalog criteria in product filter, false if not
	 * @param boolean $textfilter True to include text criteria in product filter, false if not
	 * @param boolean $attrfilter True to include attribute criteria in product filter, false if not
	 * @return array List of products implementing MShop_Product_Item_Interface
	 */
	protected function _getProductListFilter( MW_View_Interface $view, $catfilter = true, $textfilter = true, $attrfilter = true )
	{
		$sortdir = '+';
		$context = $this->_getContext();
		$config = $context->getConfig();

		$text = (string) $view->param( 'f-search-text' );
		$catid = (string) $view->param( 'f-catalog-id' );

		if( $catid == '' && $catfilter === true ) {
			$catid = $config->get( 'client/html/catalog/list/catid-default', '' );
		}

		$page = $this->_getProductListPage( $view );
		$size = $this->_getProductListSize( $view );
		$sort = $this->_getProductListSort( $view, $sortdir );


		$controller = Controller_Frontend_Factory::createController( $context, 'catalog' );

		if( $text !== '' && $textfilter === true )
		{
			$filter = $controller->createProductFilterByText( $text, $sort, $sortdir, ($page-1) * $size, $size );

			if( $catid !== '' && $catfilter === true ) {
				$filter = $controller->addProductFilterCategory( $filter, $catid );
			}
		}
		elseif( $catid !== '' && $catfilter === true )
		{
			$filter = $controller->createProductFilterByCategory( $catid, $sort, $sortdir, ($page-1) * $size, $size );
		}
		else
		{
			$filter = $controller->createProductFilterDefault( $sort, $sortdir, ($page-1) * $size, $size );
		}

		if( $attrfilter === true ) {
			$this->_addAttributeFilter( $view, $filter );
		}


		return $filter;
	}


	/**
	 * Returns the total number of products available for the current parameters.
	 *
	 * @param MW_View_Interface $view View instance with helper for retrieving the required parameters
	 * @return integer Total number of products
	 */
	protected function _getProductListTotal( MW_View_Interface $view )
	{
		if( self::$_productList === null ) {
			$this->_searchProducts( $view );
		}

		return self::$_productTotal;
	}


	/**
	 * Returns the sanitized page from the parameters for the product list.
	 *
	 * @param MW_View_Interface $view View instance with helper for retrieving the required parameters
	 * @return integer Page number starting from 1
	 */
	protected function _getProductListPage( MW_View_Interface $view )
	{
		$page = (int) $view->param( 'l-page', 1 );
		return ( $page < 1 ? 1 : $page );
	}


	/**
	 * Returns the sanitized page size from the parameters for the product list.
	 *
	 * @param MW_View_Interface $view View instance with helper for retrieving the required parameters
	 * @return integer Page size
	 */
	protected function _getProductListSize( MW_View_Interface $view )
	{
		$defaultSize = $this->_getContext()->getConfig()->get( 'client/html/catalog/list/size', 48 );

		$size = (int) $view->param( 'l-size', $defaultSize );
		return ( $size < 1 || $size > 100 ? $defaultSize : $size );
	}


	/**
	 * Returns the sanitized sortation from the parameters for the product list.
	 *
	 * @param MW_View_Interface $view View instance with helper for retrieving the required parameters
	 * @param string &$sortdir Value-result parameter where the sort direction will be stored
	 * @return string Sortation string (relevance, name, price)
	 */
	protected function _getProductListSort( MW_View_Interface $view, &$sortdir )
	{
		$sortation = (string) $view->param( 'f-sort', 'relevance' );

		$sortdir = ( $sortation[0] === '-' ? '-' : '+' );
		$sort = ltrim( $sortation, '-' );

		return ( strlen( $sort ) > 0 ? $sort : 'relevance' );
	}


	/**
	 * Searches for the products based on the current paramters.
	 *
	 * The found products and the total number of available products can be
	 * retrieved using the getProductList() and getProductTotal() methods.
	 *
	 * @param MW_View_Interface $view View instance with helper for retrieving the required parameters
	 */
	protected function _searchProducts( MW_View_Interface $view )
	{
		$context = $this->_getContext();
		$domains = $context->getConfig()->get( 'client/html/catalog/domains', array( 'media', 'price', 'text' ) );

		$productFilter = $this->_getProductListFilter( $view );
		$controller = Controller_Frontend_Factory::createController( $context, 'catalog' );

		self::$_productList = $controller->getProductList( $productFilter, self::$_productTotal, $domains );
	}
}
