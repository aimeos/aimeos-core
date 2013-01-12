<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: CatalogController.php 1357 2012-10-30 11:20:09Z nsendetzky $
 */

/**
 * Basket controller
 */
class BasketController extends Application_Controller_Action_Abstract
{
	/**
	 * Shows the catalog with or without given search, pagination criteria
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

			$this->render( 'index' );
		}
		catch( MW_Exception $e )
		{
			echo 'An error occured';
		}
		catch( Exception $e )
		{
			echo 'Error: ' . $e->getMessage();
		}


		$msg = 'Product::catalog total time: ' . ( ( microtime( true ) - $startaction ) * 1000 ) . 'ms';
		$context->getLogger()->log( $msg, MW_Logger_Abstract::INFO, 'performance' );
	}

}
