<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage HTML
 * @version $Id: Default.php 1324 2012-10-21 13:17:19Z nsendetzky $
 */


/**
 * Default implementation of catalog detail item section for HTML clients.
 *
 * @package Client
 * @subpackage HTML
 */
class Client_Html_Catalog_Detail_Additional_Text_Default
	extends Client_Html_Abstract
	implements Client_Html_Interface
{
	private $_subPartNames = array();
	private $_subPartPath = 'client/html/catalog/detail/additional/text/default/subparts';


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string|null $name Template name
	 * @return string HTML code
	 */
	public function getBody( $name = null )
	{
		$view = $this->getView();

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getBody();
		}
		$view->textBody = $html;

		$tplconf = 'client/html/catalog/detail/additional/text/default/template-body';
		$default = 'catalog/detail/additional-text-body-default.html';

		return $view->render( $this->_getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string|null $name Template name
	 * @return string String including HTML tags for the header
	 */
	public function getHeader( $name = null )
	{
		$view = $this->getView();

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getHeader();
		}
		$view->textHeader = $html;

		$tplconf = 'client/html/catalog/detail/additional/text/default/template-header';
		$default = 'catalog/detail/additional-text-header-default.html';

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
		return $this->_createSubClient( 'catalog/detail/additional/text/' . $type, $name );
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
}