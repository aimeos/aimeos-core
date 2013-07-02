<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

/**
 * Account controller
 */
class AccountController extends Application_Controller_Action_Abstract
{
	/**
	 * Integrates the account history.
	 */
	public function historyAction()
	{
		$startaction = microtime( true );
		$context = Zend_Registry::get( 'ctx' );

		try
		{
			$arcavias = $this->_getArcavias();
			$templatePaths = $arcavias->getCustomPaths( 'client/html' );

			$this->view->account = Client_Html_Account_History_Factory::createClient( $context, $templatePaths );
			$this->view->account->setView( $this->_createView() );
			$this->view->account->process();

			$filter = Client_Html_Catalog_Filter_Factory::createClient( $context, $templatePaths );
			$this->view->searchfilter = $filter->getSubClient( 'search' );
			$this->view->searchfilter->setView( $this->_createView() );
			$this->view->searchfilter->process();

			$this->view->minibasket = Client_Html_Basket_Mini_Factory::createClient( $context, $templatePaths );
			$this->view->minibasket->setView( $this->_createView() );
			$this->view->minibasket->process();

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


		$msg = 'Account total time: ' . ( ( microtime( true ) - $startaction ) * 1000 ) . 'ms';
		$context->getLogger()->log( $msg, MW_Logger_Abstract::INFO, 'performance' );
	}

}
