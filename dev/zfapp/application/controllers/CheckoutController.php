<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

/**
 * Checkout controller
 */
class CheckoutController extends Application_Controller_Action_Abstract
{
	/**
	 * Integrates the checkout process
	 */
	public function indexAction()
	{
		$startaction = microtime( true );
		$context = Zend_Registry::get( 'ctx' );

		try
		{
			$mshop = $this->_getMShop();
			$templatePaths = $mshop->getCustomPaths( 'client/html' );

			$this->view->minibasket = Client_Html_Basket_Mini_Factory::createClient( $context, $templatePaths );
			$this->view->minibasket->setView( $this->_createView() );
			$this->view->minibasket->process();

			$filter = Client_Html_Catalog_Filter_Factory::createClient( $context, $templatePaths );
			$this->view->searchfilter = $filter->getSubClient( 'search' );
			$this->view->searchfilter->setView( $this->_createView() );
			$this->view->searchfilter->process();

			$client = Client_Html_Checkout_Standard_Factory::createClient( $context, $templatePaths );
			$client->setView( $this->_createView() );
			$client->process();

			$this->view->checkout = $client;

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

		$msg = 'Checkout::index total time: ' . ( ( microtime( true ) - $startaction ) * 1000 ) . 'ms';
		$context->getLogger()->log( $msg, MW_Logger_Abstract::INFO, 'performance' );
	}


	/**
	 * Integrates the checkout confirmation
	 */
	public function confirmAction()
	{
		$startaction = microtime( true );
		$context = Zend_Registry::get( 'ctx' );

		try
		{
			$mshop = $this->_getMShop();
			$templatePaths = $mshop->getCustomPaths( 'client/html' );

			$this->view->minibasket = Client_Html_Basket_Mini_Factory::createClient( $context, $templatePaths );
			$this->view->minibasket->setView( $this->_createView() );
			$this->view->minibasket->process();

			$filter = Client_Html_Catalog_Filter_Factory::createClient( $context, $templatePaths );
			$this->view->searchfilter = $filter->getSubClient( 'search' );
			$this->view->searchfilter->setView( $this->_createView() );
			$this->view->searchfilter->process();

			$client = Client_Html_Checkout_Confirm_Factory::createClient( $context, $templatePaths );
			$client->setView( $this->_createView() );
			$client->process();

			$this->view->checkout = $client;

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

		$msg = 'Checkout::confirm total time: ' . ( ( microtime( true ) - $startaction ) * 1000 ) . 'ms';
		$context->getLogger()->log( $msg, MW_Logger_Abstract::INFO, 'performance' );
	}
}