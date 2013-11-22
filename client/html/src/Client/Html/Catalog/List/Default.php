<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of catalog list section HTML clients.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Catalog_List_Default
	extends Client_Html_Abstract
	implements Client_Html_Interface
{
	private $_cache;
	private $_subPartPath = 'client/html/catalog/list/default/subparts';
	private $_subPartNames = array( 'head', 'pagination', 'items', 'pagination' );


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @return string HTML code
	 */
	public function getBody()
	{
		try
		{
			$view = $this->_setViewParams( $this->getView() );

			$html = '';
			foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
				$html .= $subclient->setView( $view )->getBody();
			}
			$view->listBody = $html;
		}
		catch( Client_Html_Exception $e )
		{
			$view = $this->getView();
			$error = array( $this->_getContext()->getI18n()->dt( 'client/html', $e->getMessage() ) );
			$view->listErrorList = $view->get( 'listErrorList', array() ) + $error;
		}
		catch( Controller_Frontend_Exception $e )
		{
			$view = $this->getView();
			$error = array( $this->_getContext()->getI18n()->dt( 'controller/frontend', $e->getMessage() ) );
			$view->listErrorList = $view->get( 'listErrorList', array() ) + $error;
		}
		catch( MShop_Exception $e )
		{
			$view = $this->getView();
			$error = array( $this->_getContext()->getI18n()->dt( 'mshop', $e->getMessage() ) );
			$view->listErrorList = $view->get( 'listErrorList', array() ) + $error;
		}
		catch( Exception $e )
		{
			$context = $this->_getContext();
			$context->getLogger()->log( $e->getMessage() . PHP_EOL . $e->getTraceAsString() );

			$view = $this->getView();
			$error = array( $context->getI18n()->dt( 'client/html', 'A non-recoverable error occured' ) );
			$view->listErrorList = $view->get( 'listErrorList', array() ) + $error;
		}

		$tplconf = 'client/html/catalog/list/default/template-body';
		$default = 'catalog/list/body-default.html';

		return $view->render( $this->_getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @return string String including HTML tags for the header
	 */
	public function getHeader()
	{
		try
		{
			$view = $this->_setViewParams( $this->getView() );

			$html = '';
			foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
				$html .= $subclient->setView( $view )->getHeader();
			}
			$view->listHeader = $html;
		}
		catch( Exception $e )
		{
			$this->_getContext()->getLogger()->log( $e->getMessage() . PHP_EOL . $e->getTraceAsString() );
			return '';
		}

		$tplconf = 'client/html/catalog/list/default/template-header';
		$default = 'catalog/list/header-default.html';

		return $view->render( $this->_getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return Client_Html_Interface Sub-client object
	 */
	public function getSubClient( $type, $name = null )
	{
		return $this->_createSubClient( 'catalog/list/' . $type, $name );
	}


	/**
	 * Tests if the output of is cachable.
	 *
	 * @param integer $what Header or body constant from Client_HTML_Abstract
	 * @return boolean True if the output can be cached, false if not
	 */
	public function isCachable( $what )
	{
		return false;
	}


	/**
	 * Processes the input, e.g. store given values.
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 */
	public function process()
	{
		try
		{
			$this->_process( $this->_subPartPath, $this->_subPartNames );
		}
		catch( Client_Html_Exception $e )
		{
			$view = $this->getView();
			$error = array( $this->_getContext()->getI18n()->dt( 'client/html', $e->getMessage() ) );
			$view->listErrorList = $view->get( 'listErrorList', array() ) + $error;
		}
		catch( Controller_Frontend_Exception $e )
		{
			$view = $this->getView();
			$error = array( $this->_getContext()->getI18n()->dt( 'controller/frontend', $e->getMessage() ) );
			$view->listErrorList = $view->get( 'listErrorList', array() ) + $error;
		}
		catch( MShop_Exception $e )
		{
			$view = $this->getView();
			$error = array( $this->_getContext()->getI18n()->dt( 'mshop', $e->getMessage() ) );
			$view->listErrorList = $view->get( 'listErrorList', array() ) + $error;
		}
		catch( Exception $e )
		{
			$context = $this->_getContext();
			$context->getLogger()->log( $e->getMessage() . PHP_EOL . $e->getTraceAsString() );

			$view = $this->getView();
			$error = array( $context->getI18n()->dt( 'client/html', 'A non-recoverable error occured' ) );
			$view->listErrorList = $view->get( 'listErrorList', array() ) + $error;
		}
	}


	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param MW_View_Interface $view The view object which generates the HTML output
	 * @return MW_View_Interface Modified view object
	 */
	protected function _setViewParams( MW_View_Interface $view )
	{
		if( !isset( $this->_cache ) )
		{
			$context = $this->_getContext();
			$config = $context->getConfig();

			$defaultPageSize = $config->get( 'client/html/catalog/list/size', 48 );
			$domains = $config->get( 'client/html/catalog/list/domains', array( 'media', 'price', 'text' ) );


			$page = (int) $view->param( 'l-page', 1 );
			$size = (int) $view->param( 'l-size', $defaultPageSize );
			$sortation = (string) $view->param( 'l-sort', 'relevance' );
			$text = (string) $view->param( 'f-search-text' );
			$catid = (string) $view->param( 'f-catalog-id' );
			$attrids = $view->param( 'f-attr-id', array() );

			if( is_string( $attrids ) ) {
				$attrids = explode( ' ', $attrids );
			}

			if( $catid == '' ) {
				$catid = $config->get( 'client/html/catalog/list/catid-default', '' );
			}

			$page = ( $page < 1 ? 1 : $page );
			$size = ( $size < 1 || $size > 100 ? $defaultPageSize : $size );
			$sortation = ( strlen( $sortation ) === 0 ? $sortation = 'relevance' : $sortation );


			$sortdir = ( $sortation[0] === '-' ? '-' : '+' );
			$sort = ltrim( $sortation, '-' );
			$products = array();
			$total = 0;


			$controller = Controller_Frontend_Catalog_Factory::createController( $context );

			if( $text !== '' )
			{
				$filter = $controller->createProductFilterByText( $text, $sort, $sortdir, ($page-1) * $size, $size );
			}
			else if( $catid !== '' )
			{
				$filter = $controller->createProductFilterByCategory( $catid, $sort, $sortdir, ($page-1) * $size, $size );

				$catalogManager = MShop_Factory::createManager( $context, 'catalog' );
				$view->listCatPath = $catalogManager->getPath( $catid, array( 'text', 'media', 'attribute' ) );

				$listCatPath = $view->get( 'listCatPath', array() );
				if( ( $categoryItem = end( $listCatPath ) ) !== false ) {
					$view->listCurrentCatItem = $categoryItem;
				}
			}
			else
			{
				$filter = $controller->createProductFilterDefault( $sort, $sortdir, ($page-1) * $size, $size );
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

			$products = $controller->getProductList( $filter, $total, $domains );


			if( !empty( $products ) && $config->get( 'client/html/catalog/list/stock/enable', true ) === true )
			{
				$stockTarget = $config->get( 'client/html/catalog/stock/url/target' );
				$stockController = $config->get( 'client/html/catalog/stock/url/controller', 'catalog' );
				$stockAction = $config->get( 'client/html/catalog/stock/url/action', 'stock' );
				$stockConfig = $config->get( 'client/html/catalog/stock/url/config', array() );

				$productIds = array_keys( $products );
				sort( $productIds );

				$params = array( 's-product-id' => implode( ' ', $productIds ) );
				$view->listStockUrl = $view->url( $stockTarget, $stockController, $stockAction, $params, array(), $stockConfig );
			}


			$view->listParams = $this->_getClientParams( $view->param() );
			$view->listProductItems = $products;
			$view->listProductTotal = $total;
			$view->listProductSort = $sortation;
			$view->listPageCurr = $page;
			$view->listPageSize = $size;

			$this->_cache = $view;
		}

		return $this->_cache;
	}
}
