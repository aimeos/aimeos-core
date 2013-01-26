<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: CatalogController.php 1357 2012-10-30 11:20:09Z nsendetzky $
 */

/**
 * Checkout controller
 */
class CheckoutController extends Application_Controller_Action_Abstract
{
	/**
	 * Shows the checkout process
	 */
	public function indexAction()
	{
		$startaction = microtime( true );
		$context = Zend_Registry::get( 'ctx' );

		try
		{
			$mshop = $this->_getMShop();
			$templatePaths = $mshop->getCustomPaths( 'client/html' );

			$client = Client_Html_Checkout_Standard_Factory::createClient( $context, $templatePaths );
			$client->setView( $this->_createView() );
			$client->process();

			$this->view->checkout = $client;

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

		$msg = 'Checkout::index total time: ' . ( ( microtime( true ) - $startaction ) * 1000 ) . 'ms';
		$context->getLogger()->log( $msg, MW_Logger_Abstract::INFO, 'performance' );
	}

}
