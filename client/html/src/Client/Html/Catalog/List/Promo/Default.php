<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of catalog list item section for HTML clients.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Catalog_List_Promo_Default
	extends Client_Html_Abstract
	implements Client_Html_Interface
{
	private $_cache;
	private $_subPartNames = array();
	private $_subPartPath = 'client/html/catalog/list/promo/default/subparts';


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @return string HTML code
	 */
	public function getBody()
	{
		$view = $this->_setViewParams( $this->getView() );

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getBody();
		}
		$view->promoBody = $html;

		$tplconf = 'client/html/catalog/list/promo/default/template-body';
		$default = 'catalog/list/promo-body-default.html';

		return $view->render( $this->_getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @return string String including HTML tags for the header
	 */
	public function getHeader()
	{
		$view = $this->_setViewParams( $this->getView() );

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getHeader();
		}
		$view->promoHeader = $html;

		$tplconf = 'client/html/catalog/list/promo/default/template-header';
		$default = 'catalog/list/promo-header-default.html';

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
		return $this->_createSubClient( 'catalog/list/promo/' . $type, $name );
	}


	/**
	 * Tests if the output of is cachable.
	 *
	 * @param integer $what Header or body constant from Client_HTML_Abstract
	 * @return boolean True if the output can be cached, false if not
	 */
	public function isCachable( $what )
	{
		return $this->_isCachable( $what, $this->_subPartPath, $this->_subPartNames );
	}


	/**
	 * Processes the input, e.g. store given values.
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 */
	public function process()
	{
		$this->_process( $this->_subPartPath, $this->_subPartNames );
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
			$products = $productPosItems = array();

			if( isset( $view->listCurrentCatItem ) )
			{
				$productPosItems = $view->listCurrentCatItem->getRefItems( 'product', null, 'promotion' );

				$manager = MShop_Factory::createManager( $context, 'product' );

				$search = $manager->createSearch( true );
				$expr = array(
					$search->compare( '==', 'product.id', array_keys( $productPosItems ) ),
					$search->getConditions(),
				);
				$search->setConditions( $search->combine( '&&', $expr ) );

				$products = $manager->searchItems( $search, array( 'text', 'price', 'media' ) );
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
				$view->promoStockUrl = $view->url( $stockTarget, $stockController, $stockAction, $params, array(), $stockConfig );
			}


			$view->promoPositionItems = $productPosItems;
			$view->promoItems = $products;

			$this->_cache = $view;
		}

		return $this->_cache;
	}
}