<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of terms option in checkout summary HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Checkout_Standard_Summary_Option_Terms_Default
	extends Client_Html_Abstract
	implements Client_Html_Interface
{
	private $_cache;
	private $_subPartPath = 'client/html/checkout/standard/summary/option/terms/default/subparts';
	private $_subPartNames = array();


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @return string HTML code
	 */
	public function getBody()
	{
		$view = $this->getView();

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getBody();
		}
		$view->optionBody = $html;

		$tplconf = 'client/html/checkout/standard/summary/option/terms/default/template-body';
		$default = 'checkout/standard/summary-option-terms-body-default.html';

		return $view->render( $this->_getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @return string String including HTML tags for the header
	 */
	public function getHeader()
	{
		$view = $this->getView();

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getHeader();
		}
		$view->optionHeader = $html;

		$tplconf = 'client/html/checkout/standard/summary/option/terms/default/template-header';
		$default = 'checkout/standard/summary-option-terms-header-default.html';

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
		return $this->_createSubClient( 'checkout/standard/summary/option/terms/' . $type, $name );
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
	 * Processes the input, e.g. store given values.
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 */
	public function process()
	{
		$view = $this->getView();

		// only start if there's something to do
		if( ( $option = $view->param( 'cs-option-terms', null ) ) === null ) {
			return;
		}

		if( ( $option = $view->param( 'cs-option-terms-value', 0 ) ) != 1 )
		{
			$view->standardStepActive = 'summary';
			$view->termsError = true;

			$error = array( $view->translate( 'client/html', 'Please accept the terms and conditions' ) );
			$view->standardErrorList = $error + $view->get( 'standardErrorList', array() );
		}

		$this->_process( $this->_subPartPath, $this->_subPartNames );
	}
}