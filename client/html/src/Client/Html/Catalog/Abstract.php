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
	static private $_productFilter;
	static private $_productTotal = 0;


	/**
	 * Returns the products found for the current parameters.
	 *
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
	 * @return array List of products implementing MShop_Product_Item_Interface
	 */
	protected function _getProductListFilter( MW_View_Interface $view )
	{
		if( self::$_productList === null ) {
			$this->_searchProducts( $view );
		}

		return self::$_productFilter;
	}


	/**
	 * Returns the total number of products available for the current parameters.
	 *
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
	 * @return string Sortation string (relevance, name, price)
	 */
	protected function _getProductListSort( MW_View_Interface $view )
	{
		$sortation = (string) $view->param( 'l-sort', 'relevance' );
		return ( strlen( $sortation ) === 0 ? $sortation = 'relevance' : $sortation );
	}


	/**
	 * Searches for the products based on the current paramters.
	 *
	 * The found products and the total number of available products can be
	 * retrieved using the getProductList() and getProductTotal() methods.
	 */
	protected function _searchProducts( MW_View_Interface $view )
	{
			$context = $this->_getContext();
			$config = $context->getConfig();

			$domains = $config->get( 'client/html/catalog/domains', array( 'media', 'price', 'text' ) );

			$text = (string) $view->param( 'f-search-text' );
			$catid = (string) $view->param( 'f-catalog-id' );
			$attrids = $view->param( 'f-attr-id', array() );

			if( is_string( $attrids ) ) {
				$attrids = explode( ' ', $attrids );
			}

			if( $catid == '' ) {
				$catid = $config->get( 'client/html/catalog/list/catid-default', '' );
			}

			$page = $this->_getProductListPage( $view );
			$size = $this->_getProductListSize( $view );
			$sortation = $this->_getProductListSort( $view );

			$sortdir = ( $sortation[0] === '-' ? '-' : '+' );
			$sort = ltrim( $sortation, '-' );


			$controller = Controller_Frontend_Catalog_Factory::createController( $context );

			if( $text !== '' )
			{
				$filter = $controller->createProductFilterByText( $text, $sort, $sortdir, ($page-1) * $size, $size );
			}
			else if( $catid !== '' )
			{
				$filter = $controller->createProductFilterByCategory( $catid, $sort, $sortdir, ($page-1) * $size, $size );
			}
			else
			{
				$filter = $controller->createProductFilterDefault( $sort, $sortdir, ($page-1) * $size, $size );
				$expr = array(
					$filter->compare( '!=', 'catalog.index.catalog.id', null ),
					$filter->getConditions(),
				);
				$filter->setConditions( $filter->combine( '&&', $expr ) );
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

			self::$_productFilter = $filter;
			self::$_productList = $controller->getProductList( $filter, self::$_productTotal, $domains );
	}
}
