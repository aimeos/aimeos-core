<?php

/**
 * @copyright Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Catalog;


/**
 * Common methods for the catalog HTML client classes.
 *
 * @package Client
 * @subpackage Html
 */
abstract class Base
	extends \Aimeos\Client\Html\Common\Client\Factory\Base
{
	private $controller;
	private $productList;
	private $productTotal = 0;


	/**
	 * Adds the conditions for the selected attributes to the given search filter.
	 *
	 * @param array $params Associative list of parameters that should be used for filtering
	 * @param \Aimeos\MW\Criteria\Iface $filter Criteria object for searching
	 */
	protected function addAttributeFilterByParam( array $params, \Aimeos\MW\Criteria\Iface $filter )
	{
		$attrids = ( isset( $params['f_attrid'] ) ? (array) $params['f_attrid'] : array() );

		if( !empty( $attrids ) )
		{
			$func = $filter->createFunction( 'index.attributeaggregate', array( array_keys( $attrids ) ) );
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
	 * @param \Aimeos\MW\View\Iface $view View instance with helper for retrieving the required parameters
	 * @param \Aimeos\MW\Criteria\Iface $filter Criteria object for searching
	 */
	protected function addAttributeFilter( \Aimeos\MW\View\Iface $view, \Aimeos\MW\Criteria\Iface $filter )
	{
		$this->addAttributeFilterByParam( $view->param(), $filter );
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
	 * @return \Aimeos\MW\Criteria\Iface Search criteria object
	 */
	protected function createProductListFilter( $text, $catid, $sort, $sortdir, $page, $size, $catfilter, $textfilter )
	{
		$controller = $this->getCatalogController();

		if( $text !== '' && $textfilter === true )
		{
			$filter = $controller->createIndexFilterText( $text, $sort, $sortdir, ( $page - 1 ) * $size, $size );

			if( $catid !== '' && $catfilter === true ) {
				$filter = $controller->addIndexFilterCategory( $filter, $this->getCatalogIds( $catid ) );
			}

			return $filter;
		}
		elseif( $catid !== '' && $catfilter === true )
		{
			$catIds = $this->getCatalogIds( $catid );
			return $controller->createIndexFilterCategory( $catIds, $sort, $sortdir, ( $page - 1 ) * $size, $size );
		}
		else
		{
			return $controller->createIndexFilter( $sort, $sortdir, ( $page - 1 ) * $size, $size );
		}
	}


	/**
	 * Returns the catalog controller object
	 *
	 * @return \Aimeos\Controller\Frontend\Catalog\Interface Catalog controller
	 */
	protected function getCatalogController()
	{
		if( !isset( $this->controller ) )
		{
			$context = $this->getContext();
			$this->controller = \Aimeos\Controller\Frontend\Factory::createController( $context, 'catalog' );
		}

		return $this->controller;
	}


	/**
	 * Returns the list of catetory IDs if subcategories should be included
	 *
	 * @param string $catId Category ID
	 * @return string|array Cateogory ID or list of catetory IDs
	 */
	protected function getCatalogIds( $catId )
	{
		$config = $this->getContext()->getConfig();
		$default = \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE;

		/** client/html/catalog/lists/levels
		 * Include products of sub-categories in the product list of the current category
		 *
		 * Sometimes it may be useful to show products of sub-categories in the
		 * current category product list, e.g. if the current category contains
		 * no products at all or if there are only a few products in all categories.
		 *
		 * Possible constant values for this setting are:
		 * * 1 : Only products from the current category
		 * * 2 : Products from the current category and the direct child categories
		 * * 3 : Products from the current category and the whole category sub-tree
		 *
		 * Caution: Please keep in mind that displaying products of sub-categories
		 * can slow down your shop, especially if it contains more than a few
		 * products! You have no real control over the positions of the products
		 * in the result list too because all products from different categories
		 * with the same position value are placed randomly.
		 *
		 * Usually, a better way is to associate products to all categories they
		 * should be listed in. This can be done manually if there are only a few
		 * ones or during the product import automatically.
		 *
		 * @param integer Tree level constant
		 * @since 2015.11
		 * @category Developer
		 * @see client/html/catalog/lists/catid-default
		 * @see client/html/catalog/lists/domains
		 * @see client/html/catalog/lists/size
		 */
		$level = $config->get( 'client/html/catalog/lists/levels', $default );

		if( $level != $default )
		{
			$tree = $this->getCatalogController()->getCatalogTree( $catId, array(), $level );
			$catId = $this->getCatalogIdsFromTree( $tree );
		}

		return $catId;
	}


	/**
	 * Returns the list of catalog IDs for the given catalog tree
	 *
	 * @param \Aimeos\MShop\Catalog\Item\Iface $item Catalog item with children
	 * @return array List of catalog IDs
	 */
	protected function getCatalogIdsFromTree( \Aimeos\MShop\Catalog\Item\Iface $item )
	{
		$list = array( $item->getId() );

		foreach( $item->getChildren() as $child ) {
			$list = array_merge( $list, $this->getCatalogIdsFromTree( $child ) );
		}

		return $list;
	}


	/**
	 * Returns the products found for the current parameters.
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance with helper for retrieving the required parameters
	 * @return array List of products implementing \Aimeos\MShop\Product\Item\Iface
	 */
	protected function getProductList( \Aimeos\MW\View\Iface $view )
	{
		if( $this->productList === null ) {
			$this->searchProducts( $view );
		}

		return $this->productList;
	}


	/**
	 * Returns the filter from the given parameters for the product list.
	 *
	 * @param array $params Associative list of parameters that should be used for filtering
	 * @param boolean $catfilter True to include catalog criteria in product filter, false if not
	 * @param boolean $textfilter True to include text criteria in product filter, false if not
	 * @param boolean $attrfilter True to include attribute criteria in product filter, false if not
	 * @return \Aimeos\MW\Criteria\Iface Search criteria object
	 */
	protected function getProductListFilterByParam( array $params, $catfilter = true, $textfilter = true, $attrfilter = true )
	{
		$sortdir = '+';
		$context = $this->getContext();
		$config = $context->getConfig();

		$text = ( isset( $params['f_search'] ) ? (string) $params['f_search'] : '' );
		$catid = ( isset( $params['f_catid'] ) ? (string) $params['f_catid'] : '' );

		if( $catid == '' && $catfilter === true )
		{
			/** client/html/catalog/lists/catid-default
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
			 * @see client/html/catalog/lists/size
			 * @see client/html/catalog/lists/domains
			 * @see client/html/catalog/lists/levels
			 * @see client/html/catalog/detail/prodid-default
			 */
			$catid = $config->get( 'client/html/catalog/lists/catid-default', '' );
		}

		$page = $this->getProductListPageByParam( $params );
		$size = $this->getProductListSizeByParam( $params );
		$sort = $this->getProductListSortByParam( $params, $sortdir );

		$filter = $this->createProductListFilter( $text, $catid, $sort, $sortdir, $page, $size, $catfilter, $textfilter );

		if( $attrfilter === true ) {
			$this->addAttributeFilterByParam( $params, $filter );
		}


		return $filter;
	}


	/**
	 * Returns the filter created from the view parameters for the product list.
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance with helper for retrieving the required parameters
	 * @param boolean $catfilter True to include catalog criteria in product filter, false if not
	 * @param boolean $textfilter True to include text criteria in product filter, false if not
	 * @param boolean $attrfilter True to include attribute criteria in product filter, false if not
	 * @return \Aimeos\MW\Criteria\Iface Search criteria object
	 */
	protected function getProductListFilter( \Aimeos\MW\View\Iface $view, $catfilter = true, $textfilter = true, $attrfilter = true )
	{
		return $this->getProductListFilterByParam( $view->param(), $catfilter, $textfilter, $attrfilter );
	}


	/**
	 * Returns the total number of products available for the current parameters.
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance with helper for retrieving the required parameters
	 * @return integer Total number of products
	 */
	protected function getProductListTotal( \Aimeos\MW\View\Iface $view )
	{
		if( $this->productList === null ) {
			$this->searchProducts( $view );
		}

		return $this->productTotal;
	}


	/**
	 * Returns the sanitized page from the parameters for the product list.
	 *
	 * @param array $params Associative list of parameters that should be used for filtering
	 * @return integer Page number starting from 1
	 */
	protected function getProductListPageByParam( array $params )
	{
		return ( isset( $params['l_page'] ) && $params['l_page'] > 0 ? (int) $params['l_page'] : 1 );
	}


	/**
	 * Returns the sanitized page from the parameters for the product list.
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance with helper for retrieving the required parameters
	 * @return integer Page number starting from 1
	 */
	protected function getProductListPage( \Aimeos\MW\View\Iface $view )
	{
		return $this->getProductListPageByParam( $view->param() );
	}


	/**
	 * Returns the sanitized page size from the parameters for the product list.
	 *
	 * @param array $params Associative list of parameters that should be used for filtering
	 * @return integer Page size
	 */
	protected function getProductListSizeByParam( array $params )
	{
		/** client/html/catalog/lists/size
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
		 * @see client/html/catalog/lists/catid-default
		 * @see client/html/catalog/lists/domains
		 * @see client/html/catalog/lists/levels
		 */
		$defaultSize = $this->getContext()->getConfig()->get( 'client/html/catalog/lists/size', 48 );

		$size = ( isset( $params['l_size'] ) ? (int) $params['l_size'] : $defaultSize );
		return ( $size < 1 || $size > 100 ? $defaultSize : $size );
	}


	/**
	 * Returns the sanitized page size from the parameters for the product list.
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance with helper for retrieving the required parameters
	 * @return integer Page size
	 */
	protected function getProductListSize( \Aimeos\MW\View\Iface $view )
	{
		return $this->getProductListSizeByParam( $view->param() );
	}


	/**
	 * Returns the sanitized sortation from the parameters for the product list.
	 *
	 * @param array $params Associative list of parameters that should be used for filtering
	 * @param string &$sortdir Value-result parameter where the sort direction will be stored
	 * @return string Sortation string (relevance, name, price)
	 */
	protected function getProductListSortByParam( array $params, &$sortdir )
	{
		$sortation = ( isset( $params['f_sort'] ) ? (string) $params['f_sort'] : 'relevance' );

		$sortdir = ( $sortation[0] === '-' ? '-' : '+' );
		$sort = ltrim( $sortation, '-' );

		return ( strlen( $sort ) > 0 ? $sort : 'relevance' );
	}


	/**
	 * Returns the sanitized sortation from the parameters for the product list.
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance with helper for retrieving the required parameters
	 * @param string &$sortdir Value-result parameter where the sort direction will be stored
	 * @return string Sortation string (relevance, name, price)
	 */
	protected function getProductListSort( \Aimeos\MW\View\Iface $view, &$sortdir )
	{
		return $this->getProductListSortByParam( $view->param(), $sortdir );
	}


	/**
	 * Searches for the products based on the current paramters.
	 *
	 * The found products and the total number of available products can be
	 * retrieved using the getProductList() and getProductTotal() methods.
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance with helper for retrieving the required parameters
	 */
	protected function searchProducts( \Aimeos\MW\View\Iface $view )
	{
		$context = $this->getContext();
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
		 * This configuration option can be overwritten by the "client/html/catalog/lists/domains"
		 * configuration option that allows to configure the domain names of the
		 * items fetched specifically for all types of product listings.
		 *
		 * @param array List of domain names
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/catalog/lists/domains
		 * @see client/html/catalog/lists/catid-default
		 * @see client/html/catalog/lists/size
		 * @see client/html/catalog/lists/levels
		 */
		$domains = $config->get( 'client/html/catalog/domains', array( 'media', 'price', 'text' ) );

		/** client/html/catalog/lists/domains
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
		 * @see client/html/catalog/lists/catid-default
		 * @see client/html/catalog/lists/size
		 * @see client/html/catalog/lists/levels
		 */
		$domains = $config->get( 'client/html/catalog/lists/domains', $domains );

		$controller = $this->getCatalogController();
		$productFilter = $this->getProductListFilter( $view );

		$this->productList = $controller->getIndexItems( $productFilter, $domains, $this->productTotal );
	}
}
