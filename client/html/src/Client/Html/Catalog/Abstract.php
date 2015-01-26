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
	private $_productList;
	private $_productTotal = 0;


	/**
	 * Adds the conditions for the selected attributes to the given search filter.
	 *
	 * @param array $params Associative list of parameters that should be used for filtering
	 * @param MW_Common_Criteria_Interface $filter Criteria object for searching
	 */
	protected function _addAttributeFilterByParam( array $params, MW_Common_Criteria_Interface $filter )
	{
		$attrids = ( isset( $params['f_attrid'] ) ? (array) $params['f_attrid'] : array() );

		if( !empty( $attrids ) )
		{
			$func = $filter->createFunction( 'catalog.index.attributeaggregate', array( array_keys( $attrids ) ) );
			$expr = array(
				$filter->getConditions(),
				$filter->compare( '==', $func, count( $attrids ) ),
			);
			$filter->setConditions( $filter->combine( '&&', $expr ) );
		}
	}


	/**
	 * Adds the conditions for the selected attributes to the given search filter.
	 *
	 * @param MW_View_Interface $view View instance with helper for retrieving the required parameters
	 * @param MW_Common_Criteria_Interface $filter Criteria object for searching
	 */
	protected function _addAttributeFilter( MW_View_Interface $view, MW_Common_Criteria_Interface $filter )
	{
		$this->_addAttributeFilterByParam( $view->param(), $filter );
	}


	/**
	 * Returns the products found for the current parameters.
	 *
	 * @param MW_View_Interface $view View instance with helper for retrieving the required parameters
	 * @return array List of products implementing MShop_Product_Item_Interface
	 */
	protected function _getProductList( MW_View_Interface $view )
	{
		if( $this->_productList === null ) {
			$this->_searchProducts( $view );
		}

		return $this->_productList;
	}


	/**
	 * Returns the filter from the given parameters for the product list.
	 *
	 * @param array $params Associative list of parameters that should be used for filtering
	 * @param boolean $catfilter True to include catalog criteria in product filter, false if not
	 * @param boolean $textfilter True to include text criteria in product filter, false if not
	 * @param boolean $attrfilter True to include attribute criteria in product filter, false if not
	 * @return MW_Common_Criteria_Interface Search criteria object
	 */
	protected function _getProductListFilterByParam( array $params, $catfilter = true, $textfilter = true, $attrfilter = true )
	{
		$sortdir = '+';
		$context = $this->_getContext();
		$config = $context->getConfig();

		$text = ( isset( $params['f_search'] ) ? (string) $params['f_search'] : '' );
		$catid = ( isset( $params['f_catid'] ) ? (string) $params['f_catid'] : '' );

		if( $catid == '' && $catfilter === true )
		{
			/** client/html/catalog/list/catid-default
			 * The default category ID used if none is given as parameter
			 *
			 * If users view a product list page without a category ID in the
			 * parameter list, the first found products are displayed with a
			 * random order. You can circumvent this by configuring a default
			 * category ID that should be used in this case (the ID of the root
			 * category is best for this). In most cases you can set this value
			 * via the administration interface of the shop application.
			 *
			 * @param string Category ID
			 * @since 2014.03
			 * @category User
			 * @category Developer
			 * @see client/html/catalog/list/size
			 * @see client/html/catalog/list/domains
			 */
			$catid = $config->get( 'client/html/catalog/list/catid-default', '' );
		}

		$page = $this->_getProductListPageByParam( $params );
		$size = $this->_getProductListSizeByParam( $params );
		$sort = $this->_getProductListSortByParam( $params, $sortdir );

		$filter = $this->_createProductListFilter( $text, $catid, $sort, $sortdir, $page, $size, $catfilter, $textfilter );

		if( $attrfilter === true ) {
			$this->_addAttributeFilterByParam( $params, $filter );
		}


		return $filter;
	}


	/**
	 * Creates the filter from the given parameters for the product list.
	 *
	 * @param string $text Text to search for
	 * @param string $catid Category ID to search for
	 * @param string $sort Sortation string (relevance, name, price)
	 * @param string $sortdir Sortation direction (+ or -)
	 * @param integer $page Page number starting from 1
	 * @param integer $size Page size
	 * @param boolean $catfilter True to include catalog criteria in product filter, false if not
	 * @param boolean $textfilter True to include text criteria in product filter, false if not
	 * @return MW_Common_Criteria_Interface Search criteria object
	 */
	private function _createProductListFilter( $text, $catid, $sort, $sortdir, $page, $size, $catfilter, $textfilter )
	{
		$controller = Controller_Frontend_Factory::createController( $this->_getContext(), 'catalog' );

		if( $text !== '' && $textfilter === true )
		{
			$filter = $controller->createProductFilterByText( $text, $sort, $sortdir, ($page-1) * $size, $size );

			if( $catid !== '' && $catfilter === true ) {
				$filter = $controller->addProductFilterCategory( $filter, $catid );
			}

			return $filter;
		}
		elseif( $catid !== '' && $catfilter === true )
		{
			return $controller->createProductFilterByCategory( $catid, $sort, $sortdir, ($page-1) * $size, $size );
		}
		else
		{
			return $controller->createProductFilterDefault( $sort, $sortdir, ($page-1) * $size, $size );
		}
	}


	/**
	 * Returns the filter created from the view parameters for the product list.
	 *
	 * @param MW_View_Interface $view View instance with helper for retrieving the required parameters
	 * @param boolean $catfilter True to include catalog criteria in product filter, false if not
	 * @param boolean $textfilter True to include text criteria in product filter, false if not
	 * @param boolean $attrfilter True to include attribute criteria in product filter, false if not
	 * @return MW_Common_Criteria_Interface Search criteria object
	 */
	protected function _getProductListFilter( MW_View_Interface $view, $catfilter = true, $textfilter = true, $attrfilter = true )
	{
		return $this->_getProductListFilterByParam( $view->param(), $catfilter, $textfilter, $attrfilter );
	}


	/**
	 * Returns the total number of products available for the current parameters.
	 *
	 * @param MW_View_Interface $view View instance with helper for retrieving the required parameters
	 * @return integer Total number of products
	 */
	protected function _getProductListTotal( MW_View_Interface $view )
	{
		if( $this->_productList === null ) {
			$this->_searchProducts( $view );
		}

		return $this->_productTotal;
	}


	/**
	 * Returns the sanitized page from the parameters for the product list.
	 *
	 * @param array $params Associative list of parameters that should be used for filtering
	 * @return integer Page number starting from 1
	 */
	protected function _getProductListPageByParam( array $params )
	{
		return ( isset( $params['l_page'] ) && $params['l_page'] > 0 ? (int) $params['l_page'] : 1 );
	}


	/**
	 * Returns the sanitized page from the parameters for the product list.
	 *
	 * @param MW_View_Interface $view View instance with helper for retrieving the required parameters
	 * @return integer Page number starting from 1
	 */
	protected function _getProductListPage( MW_View_Interface $view )
	{
		return $this->_getProductListPageByParam( $view->param() );
	}


	/**
	 * Returns the sanitized page size from the parameters for the product list.
	 *
	 * @param array $params Associative list of parameters that should be used for filtering
	 * @return integer Page size
	 */
	protected function _getProductListSizeByParam( array $params )
	{
		/** client/html/catalog/list/size
		 * The number of products shown in a list page
		 *
		 * Limits the number of products that is shown in the list pages to the
		 * given value. If more products are available, the products are split
		 * into bunches which will be shown on their own list page. The user is
		 * able to move to the next page (or previous one if it's not the first)
		 * to display the next (or previous) products.
		 *
		 * The value must be an integer number from 1 to 100. Negative values as
		 * well as values above 100 are not allowed. The value can be overwritten
		 * per request if the "l_size" parameter is part of the URL.
		 *
		 * @param integer Number of products
		 * @since 2014.03
		 * @category User
		 * @category Developer
		 * @see client/html/catalog/list/catid-default
		 * @see client/html/catalog/list/domains
		 */
		$defaultSize = $this->_getContext()->getConfig()->get( 'client/html/catalog/list/size', 48 );

		$size = ( isset( $params['l_size'] ) ? (int) $params['l_size'] : $defaultSize );
		return ( $size < 1 || $size > 100 ? $defaultSize : $size );
	}


	/**
	 * Returns the sanitized page size from the parameters for the product list.
	 *
	 * @param MW_View_Interface $view View instance with helper for retrieving the required parameters
	 * @return integer Page size
	 */
	protected function _getProductListSize( MW_View_Interface $view )
	{
		return $this->_getProductListSizeByParam( $view->param() );
	}


	/**
	 * Returns the sanitized sortation from the parameters for the product list.
	 *
	 * @param array $params Associative list of parameters that should be used for filtering
	 * @param string &$sortdir Value-result parameter where the sort direction will be stored
	 * @return string Sortation string (relevance, name, price)
	 */
	protected function _getProductListSortByParam( array $params, &$sortdir )
	{
		$sortation = ( isset( $params['f_sort'] ) ? (string) $params['f_sort'] : 'relevance' );

		$sortdir = ( $sortation[0] === '-' ? '-' : '+' );
		$sort = ltrim( $sortation, '-' );

		return ( strlen( $sort ) > 0 ? $sort : 'relevance' );
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
		return $this->_getProductListSortByParam( $view->param(), $sortdir );
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
		$config = $context->getConfig();

		/** client/html/catalog/domains
		 * A list of domain names whose items should be available in the catalog view templates
		 *
		 * The templates rendering catalog related data usually add the images and
		 * texts associated to each item. If you want to display additional
		 * content like the attributes, you can configure your own list of
		 * domains (attribute, media, price, product, text, etc. are domains)
		 * whose items are fetched from the storage. Please keep in mind that
		 * the more domains you add to the configuration, the more time is required
		 * for fetching the content!
		 *
		 * This configuration option can be overwritten by the "client/html/catalog/list/domains"
		 * configuration option that allows to configure the domain names of the
		 * items fetched specifically for all types of product listings.
		 *
		 * @param array List of domain names
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/catalog/list/domains
		 * @see client/html/catalog/list/catid-default
		 * @see client/html/catalog/list/size
		 */
		$domains = $config->get( 'client/html/catalog/domains', array( 'media', 'price', 'text' ) );

		/** client/html/catalog/list/domains
		 * A list of domain names whose items should be available in the product list view template
		 *
		 * The templates rendering product lists usually add the images, prices
		 * and texts associated to each product item. If you want to display additional
		 * content like the product attributes, you can configure your own list of
		 * domains (attribute, media, price, product, text, etc. are domains)
		 * whose items are fetched from the storage. Please keep in mind that
		 * the more domains you add to the configuration, the more time is required
		 * for fetching the content!
		 *
		 * This configuration option overwrites the "client/html/catalog/domains"
		 * option that allows to configure the domain names of the items fetched
		 * for all catalog related data.
		 *
		 * @param array List of domain names
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/catalog/domains
		 * @see client/html/catalog/detail/domains
		 * @see client/html/catalog/stage/domains
		 * @see client/html/catalog/list/catid-default
		 * @see client/html/catalog/list/size
		 */
		$domains = $config->get( 'client/html/catalog/list/domains', $domains );

		$productFilter = $this->_getProductListFilter( $view );
		$controller = Controller_Frontend_Factory::createController( $context, 'catalog' );

		$this->_productList = $controller->getProductList( $productFilter, $this->_productTotal, $domains );
	}
}
