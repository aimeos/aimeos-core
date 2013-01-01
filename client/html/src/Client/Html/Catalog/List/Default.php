<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 * @version $Id: Default.php 1354 2012-10-30 11:17:57Z nsendetzky $
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
	private $_subPartNames = array( 'pagination', 'items', 'pagination' );


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string|null $name Template name
	 * @return string HTML code
	 */
	public function getBody( $name = null )
	{
		$view = $this->_setViewParams( $this->getView() );

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getBody();
		}
		$view->listBody = $html;

		$tplconf = 'client/html/catalog/list/default/template-body';
		$default = 'catalog/list/body-default.html';

		return $view->render( $this->_getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string|null $name Template name
	 * @return string String including HTML tags for the header
	 */
	public function getHeader( $name = null )
	{
		$view = $this->_setViewParams( $this->getView() );

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getHeader();
		}
		$view->listHeader = $html;

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


			$params = array();
			foreach( $view->param() as $key => $value )
			{
				if( strncmp( 'f-', $key, 2 ) === 0 || strncmp( 'l-', $key, 2 ) === 0 ) {
					$params[$key] = $value;
				}
			}


			$defaultPageSize = $config->get( 'client/html/catalog/list/default/size', 48 );
			$domains = $config->get( 'client/html/catalog/list/default/domains', array( 'media', 'price', 'text' ) );


			$page = (int) $view->param( 'l-page', 1 );
			$size = (int) $view->param( 'l-size', $defaultPageSize );
			$sortation = (string) $view->param( 'l-sort', 'position' );
			$text = (string) $view->param( 'f-search-text' );
			$catid = $view->param( 'f-catalog-id' );

			$page = ( $page < 1 ? 1 : $page );
			$size = ( $size < 1 || $size > 100 ? $defaultPageSize : $size );
			$sortation = ( strlen( $sortation ) === 0 ? $sortation = 'position' : $sortation );


			$sortdir = ( $sortation[0] === '-' ? '-' : '+' );
			$sort = ltrim( $sortation, '-' );
			$total = 0;


			$controller = Controller_Frontend_Catalog_Factory::createController( $context );
			$catalogManager = MShop_Catalog_Manager_Factory::createManager( $context );

			if( !empty( $text ) ) {
				$filter = $controller->createProductFilterByText( $text, $sort, $sortdir, ($page-1) * $size, $size );
				$node = $catalogManager->createItem();
			} else if( !empty( $catid ) ) {
				$filter = $controller->createProductFilterByCategory( $catid, $sort, $sortdir, ($page-1) * $size, $size );
				$node = $catalogManager->getItem( $catid, array( 'text', 'media' ) );
			} else {
				$filter = $controller->createProductFilterByText( $text, $sort, $sortdir, ($page-1) * $size, $size );
				$node = $catalogManager->createItem();
			}

			$items = $controller->getProductList( $filter, $total, $domains );


			$view->listProductItems = $items;
			$view->listProductTotal = $total;
			$view->listProductSort = $sortation;
			$view->listPageCurr = $page;
			$view->listPageSize = $size;
			$view->listParams = $params;
			$view->listCatItem = $node;

			$this->_cache = $view;
		}

		return $this->_cache;
	}
}