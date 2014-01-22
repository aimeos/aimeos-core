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
{
	private $_cache;
	private $_subPartPath = 'client/html/catalog/list/default/subparts';
	private $_subPartNames = array( 'head', 'promo', 'pagination', 'items', 'pagination' );


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

			$products = $this->_getProductList( $view );

			$text = (string) $view->param( 'f-search-text' );
			$catid = (string) $view->param( 'f-catalog-id' );

			if( $catid == '' ) {
				$catid = $config->get( 'client/html/catalog/list/catid-default', '' );
			}

			if( $text === '' && $catid !== '' )
			{
				$domains = $config->get( 'client/html/catalog/domains', array( 'media', 'text' ) );

				$catalogManager = MShop_Factory::createManager( $context, 'catalog' );
				$view->listCatPath = $catalogManager->getPath( $catid, $domains );

				$listCatPath = $view->get( 'listCatPath', array() );

				if( ( $categoryItem = end( $listCatPath ) ) !== false ) {
					$view->listCurrentCatItem = $categoryItem;
				}
			}

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
			$view->listProductTotal = $this->_getProductListTotal( $view );
			$view->listProductSort = $this->_getProductListSort( $view );
			$view->listPageCurr = $this->_getProductListPage( $view );
			$view->listPageSize = $this->_getProductListSize( $view );
			$view->listProductItems = $products;

			$this->_cache = $view;
		}

		return $this->_cache;
	}
}
