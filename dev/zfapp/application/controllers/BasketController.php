<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

/**
 * Basket controller
 */
class BasketController extends Application_Controller_Action_Abstract
{
	/**
	 * Integrates the basket.
	 */
	public function indexAction()
	{
		$startaction = microtime( true );
		$context = Zend_Registry::get( 'ctx' );

		try
		{
			$mshop = $this->_getMShop();
			$templatePaths = $mshop->getCustomPaths( 'client/html' );

			$this->view->basket = Client_Html_Basket_Standard_Factory::createClient( $context, $templatePaths );
			$this->view->basket->setView( $this->_createView() );
			$this->view->basket->process();

			$filter = Client_Html_Catalog_Filter_Factory::createClient( $context, $templatePaths );
			$this->view->searchfilter = $filter->getSubClient( 'search' );
			$this->view->searchfilter->setView( $this->_createView() );

			$this->view->minibasket = Client_Html_Basket_Mini_Factory::createClient( $context, $templatePaths );
			$this->view->minibasket->setView( $this->_createView() );

			$this->render( 'index' );
		}
		catch( MW_Exception $e )
		{
			echo 'A database error occured';
		}
		catch( Exception $e )
		{
			echo 'Error: ' . $e->getMessage();
		}


		$msg = 'Basket total time: ' . ( ( microtime( true ) - $startaction ) * 1000 ) . 'ms';
		$context->getLogger()->log( $msg, MW_Logger_Abstract::INFO, 'performance' );
	}

}
