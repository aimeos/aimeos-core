<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of catalog detail basket selection section for HTML clients.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Catalog_Detail_Basket_Selection_Default
	extends Client_Html_Abstract
	implements Client_Html_Interface
{
	private $_cache;
	private $_subPartNames = array();
	private $_subPartPath = 'client/html/catalog/detail/basket/selection/default/subparts';


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
		$view->selectionBody = $html;

		$tplconf = 'client/html/catalog/detail/basket/selection/default/template-body';
		$default = 'catalog/detail/basket-selection-body-default.html';

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
		$view->selectionHeader = $html;

		$tplconf = 'client/html/catalog/detail/basket/selection/default/template-header';
		$default = 'catalog/detail/basket-selection-header-default.html';

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
		return $this->_createSubClient( 'catalog/detail/basket/selection/' . $type, $name );
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
	 */
	protected function _setViewParams( MW_View_Interface $view )
	{
		if( !isset( $this->_cache ) )
		{
			if( $view->detailProductItem->getType() === 'select' )
			{
				$context = $this->_getContext();
				$products = $view->detailProductItem->getRefItems( 'product', 'default', 'default' );

				$productManager = MShop_Product_Manager_Factory::createManager( $context );
				$attrManager = MShop_Attribute_Manager_Factory::createManager( $context );

				$search = $productManager->createSearch( true );
				$expr = array(
					$search->compare( '==', 'product.id', array_keys( $products ) ),
					$search->getConditions(),
				);
				$search->setConditions( $search->combine( '&&', $expr ) );

				$attrIds = $attrMap = array();
				$domains = array( 'text', 'price', 'media', 'attribute' );

				foreach( $productManager->searchItems( $search, $domains ) as $subProduct )
				{
					foreach( $subProduct->getRefItems( 'attribute', null, 'variant' ) as $id => $attrItem )
					{
						$attrMap[ $attrItem->getType() ][$id] = $attrItem;
						$attrIds[] = $id;
					}
				}

				$search = $attrManager->createSearch( true );
				$expr = array(
					$search->compare( '==', 'attribute.id', $attrIds ),
					$search->getConditions(),
				);
				$search->setConditions( $search->combine( '&&', $expr ) );

				$view->selectionAttributeItems = $attrManager->searchItems( $search, array( 'text', 'media' ) );
				$view->selectionAttributeMap = $attrMap;
			}

			$this->_cache = $view;
		}

		return $this->_cache;
	}
}