<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 * @version $Id: Default.php 1324 2012-10-21 13:17:19Z nsendetzky $
 */


/**
 * Default implementation of standard basket HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Basket_Standard_Default
	extends Client_Html_Abstract
	implements Client_Html_Interface
{
	private $_subPartPath = 'client/html/basket/standard/default/subparts';
	private $_subPartNames = array( 'main' );


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @return string HTML code
	 */
	public function getBody()
	{
		$view = $this->_process( $this->getView() );

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getBody();
		}
		$view->standardBody = $html;

		$tplconf = 'client/html/basket/standard/default/template-body';
		$default = 'basket/standard/body-default.html';

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
		$view->standardHeader = $html;

		$tplconf = 'client/html/basket/standard/default/template-header';
		$default = 'basket/standard/header-default.html';

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
		return $this->_createSubClient( 'basket/standard/' . $type, $name );
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
	 * Sets the necessary parameter values in the view.
	 *
	 * @param MW_View_Interface $view The view object which generates the HTML output
	 * @return MW_View_Interface Modified view object
	 */
	protected function _process( MW_View_Interface $view )
	{
		$controller = Controller_Frontend_Basket_Factory::createController( $this->_getContext() );

		switch( $view->param( 'b-action' ) )
		{
			case 'add':

				$products = $view->param( 'b-prod', array() );
				$reqvariant = $view->config( 'basket/require-variant', true );

				if( ( $prodid = $view->param( 'b-prod-id', null ) ) !== null )
				{
					$products[] = array(
						'prod-id' => $prodid,
						'quantity' => $view->param( 'b-quantity', 1 ),
						'attrconf-id' => $view->param( 'b-attrconf-id', array() ),
						'attrvar-id' => $view->param( 'b-attrvar-id', array() )
					);
				}

				foreach( $products as $values )
				{
					$controller->addProduct(
						( isset( $values['prod-id'] ) ? $values['prod-id'] : null ),
						( isset( $values['quantity'] ) ? $values['quantity'] : 1 ),
						( isset( $values['attrconf-id'] ) ? $values['attrconf-id'] : array() ),
						( isset( $values['attrvar-id'] ) ? $values['attrvar-id'] : array() ),
						$reqvariant
					);
				}

				break;

			case 'edit':

				$products = $view->param( 'b-prod', array() );

				if( ( $positon = $view->param( 'b-position', null ) ) !== null )
				{
					$products[] = array(
						'position' => $positon,
						'quantity' => $view->param( 'b-quantity', 1 ),
						'attrconf-code' => $view->param( 'b-attrconf-code', array() )
					);
				}

				foreach( $products as $values )
				{
					$controller->editProduct(
						( isset( $values['position'] ) ? $values['position'] : null ),
						( isset( $values['quantity'] ) ? $values['quantity'] : 1 ),
						( isset( $values['attrconf-code'] ) ? $values['attrconf-code'] : array() )
					);
				}

				break;

			case 'delete':

				$products = (array) $view->param( 'b-position', array() );

				foreach( $products as $position ) {
					$controller->deleteProduct( $position );
				}

				break;
		}

		$view->standardBasket = $controller->get();

		return $view;
	}
}