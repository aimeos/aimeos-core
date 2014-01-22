<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of catalog stock HTML clients.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Catalog_Stock_Default
	extends Client_Html_Abstract
{
	private $_cache;
	private $_subPartPath = 'client/html/catalog/stock/default/subparts';
	private $_subPartNames = array();


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
			$view->stockBody = $html;
		}
		catch( Exception $e )
		{
			$this->_getContext()->getLogger()->log( $e->getMessage() . PHP_EOL . $e->getTraceAsString() );
			return;
		}

		$tplconf = 'client/html/catalog/stock/default/template-body';
		$default = 'catalog/stock/body-default.html';

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
			$view = $this->getView();

			$html = '';
			foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
				$html .= $subclient->setView( $view )->getHeader();
			}
			$view->stockHeader = $html;
		}
		catch( Exception $e )
		{
			$this->_getContext()->getLogger()->log( $e->getMessage() . PHP_EOL . $e->getTraceAsString() );
			return '';
		}

		$tplconf = 'client/html/catalog/stock/default/template-header';
		$default = 'catalog/stock/header-default.html';

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
		return $this->_createSubClient( 'catalog/stock/' . $type, $name );
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
		catch( Exception $e )
		{
			$this->_getContext()->getLogger()->log( $e->getMessage() . PHP_EOL . $e->getTraceAsString() );
			return;
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
			$siteConfig = $context->getLocale()->getSite()->getConfig();
			$productIds = explode( ' ', $view->param( 's-product-id' ) );
			$sortkey = $context->getConfig()->get( 'client/html/catalog/stock/sort', 'product.stock.warehouseid' );


			$stockManager = MShop_Factory::createManager( $context, 'product/stock' );

			$search = $stockManager->createSearch( true );
			$expr = array( $search->compare( '==', 'product.stock.productid', $productIds ) );

			if( isset( $siteConfig['warehouse'] ) ) {
				$expr[] = $search->compare( '==', 'product.stock.warehouse.code', $siteConfig['warehouse'] );
			}

			$expr[] = $search->getConditions();

			$sortations = array(
				$search->sort( '+', 'product.stock.productid' ),
				$search->sort( '+', $sortkey ),
			);

			$search->setConditions( $search->combine( '&&', $expr ) );
			$search->setSortations( $sortations );
			$search->setSlice( 0, 0x7fffffff );

			$stockItems = $stockManager->searchItems( $search );


			if( !empty( $stockItems ) )
			{
				$warehouseIds = $stockItemsByProducts = array();

				foreach( $stockItems as $item )
				{
					$warehouseIds[ $item->getWarehouseId() ] = null;
					$stockItemsByProducts[ $item->getProductId() ][] = $item;
				}

				$warehouseIds = array_keys( $warehouseIds );


				$warehouseManager = MShop_Factory::createManager( $context, 'product/stock/warehouse' );

				$search = $warehouseManager->createSearch();
				$search->setConditions( $search->compare( '==', 'product.stock.warehouse.id', $warehouseIds ) );
				$search->setSlice( 0, count( $warehouseIds ) );


				$view->stockWarehouseItems = $warehouseManager->searchItems( $search );
				$view->stockItemsByProducts = $stockItemsByProducts;
			}


			$view->stockProductIds = $productIds;

			$this->_cache = $view;
		}

		return $this->_cache;
	}
}
