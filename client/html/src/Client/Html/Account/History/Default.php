<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of account history HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Account_History_Default
	extends Client_Html_Abstract
	implements Client_Html_Interface
{
	private $_subPartPath = 'client/html/account/history/default/subparts';
	private $_subPartNames = array( 'list', 'detail' );


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @return string HTML code
	 */
	public function getBody()
	{
		try
		{
			$view = $this->getView();

			$html = '';
			foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
				$html .= $subclient->setView( $view )->getBody();
			}
			$view->historyBody = $html;
		}
		catch( Client_Html_Exception $e )
		{
			$view = $this->getView();
			$error = array( $this->_getContext()->getI18n()->dt( 'client/html', $e->getMessage() ) );
			$view->historyErrorList = $view->get( 'historyErrorList', array() ) + $error;
		}
		catch( Controller_Frontend_Exception $e )
		{
			$view = $this->getView();
			$error = array( $this->_getContext()->getI18n()->dt( 'controller/frontend', $e->getMessage() ) );
			$view->historyErrorList = $view->get( 'historyErrorList', array() ) + $error;
		}
		catch( MShop_Exception $e )
		{
			$view = $this->getView();
			$error = array( $this->_getContext()->getI18n()->dt( 'mshop', $e->getMessage() ) );
			$view->historyErrorList = $view->get( 'historyErrorList', array() ) + $error;
		}
		catch( Exception $e )
		{
			$context = $this->_getContext();
			$context->getLogger()->log( $e->getMessage() . PHP_EOL . $e->getTraceAsString() );

			$view = $this->getView();
			$error = array( $context->getI18n()->dt( 'client/html', 'A non-recoverable error occured' ) );
			$view->historyErrorList = $view->get( 'historyErrorList', array() ) + $error;
		}

		$tplconf = 'client/html/account/history/default/template-body';
		$default = 'account/history/body-default.html';

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
			$view->historyHeader = $html;
		}
		catch( Exception $e )
		{
			$this->_getContext()->getLogger()->log( $e->getMessage() . PHP_EOL . $e->getTraceAsString() );
			return '';
		}

		$tplconf = 'client/html/account/history/default/template-header';
		$default = 'account/history/header-default.html';

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
		return $this->_createSubClient( 'account/history/' . $type, $name );
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
	 */
	public function process()
	{
		$this->_process( $this->_subPartPath, $this->_subPartNames );
	}
}