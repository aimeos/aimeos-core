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
	private $_controller;


	/**
	 * Initializes the client.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 * @param array $templatePaths Associative list of the file system paths to the co
	 *      and a list of relative paths inside the core or the extension as values
	 */
	public function __construct( MShop_Context_Item_Interface $context, array $templatePaths )
	{
		parent::__construct( $context, $templatePaths );

		$this->_controller = Controller_Frontend_Basket_Factory::createController( $this->_getContext() );
	}


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
			$view->standardBody = $html;
		}
		catch( Client_Html_Exception $e )
		{
			$view = $this->getView();
			$error = array( $this->_getContext()->getI18n()->dt( 'client/html', $e->getMessage() ) );
			$view->standardErrorList = $view->get( 'standardErrorList', array() ) + $error;
		}
		catch( Controller_Frontend_Exception $e )
		{
			$view = $this->getView();
			$error = array( $this->_getContext()->getI18n()->dt( 'controller/frontend', $e->getMessage() ) );
			$view->standardErrorList = $view->get( 'standardErrorList', array() ) + $error;
		}
		catch( MShop_Exception $e )
		{
			$view = $this->getView();
			$error = array( $this->_getContext()->getI18n()->dt( 'mshop', $e->getMessage() ) );
			$view->standardErrorList = $view->get( 'standardErrorList', array() ) + $error;
		}
		catch( Exception $e )
		{
			$context = $this->_getContext();
			$context->getLogger()->log( $e->getMessage() . PHP_EOL . $e->getTraceAsString() );

			$view = $this->getView();
			$error = array( $context->getI18n()->dt( 'client/html', 'A non-recoverable error occured' ) );
			$view->standardErrorList = $view->get( 'standardErrorList', array() ) + $error;
		}

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
		try
		{
			$view = $this->_setViewParams( $this->getView() );

			$html = '';
			foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
				$html .= $subclient->setView( $view )->getHeader();
			}
			$view->standardHeader = $html;
		}
		catch( Exception $e )
		{
			$this->_getContext()->getLogger()->log( $e->getMessage() . PHP_EOL . $e->getTraceAsString() );
			return '';
		}

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
	 */
	public function process()
	{
		$view = $this->getView();

		try
		{
			switch( $view->param( 'b-action' ) )
			{
				case 'add':

					$products = (array) $view->param( 'b-prod', array() );
					$reqvariant = $view->config( 'client/html/basket/standard/require-variant', true );

					if( ( $prodid = $view->param( 'b-prod-id', null ) ) !== null )
					{
						$products[] = array(
							'prod-id' => $prodid,
							'quantity' => $view->param( 'b-quantity', 1 ),
							'attrvar-id' => array_filter( (array) $view->param( 'b-attrvar-id', array() ) ),
							'attrconf-id' => array_filter( (array) $view->param( 'b-attrconf-id', array() ) ),
							'attrhide-id' => array_filter( (array) $view->param( 'b-attrhide-id', array() ) )
						);
					}

					foreach( $products as $values )
					{
						$this->_controller->addProduct(
							( isset( $values['prod-id'] ) ? $values['prod-id'] : null ),
							( isset( $values['quantity'] ) ? $values['quantity'] : 1 ),
							$reqvariant,
							( isset( $values['attrvar-id'] ) ? array_filter( (array) $values['attrvar-id'] ) : array() ),
							( isset( $values['attrconf-id'] ) ? array_filter( (array) $values['attrconf-id'] ) : array() ),
							( isset( $values['attrhide-id'] ) ? array_filter( (array) $values['attrhide-id'] ) : array() )
						);
					}

					break;

				case 'edit':

					$products = (array) $view->param( 'b-prod', array() );

					if( ( $positon = $view->param( 'b-position', null ) ) !== null )
					{
						$products[] = array(
							'position' => $positon,
							'quantity' => $view->param( 'b-quantity', 1 ),
							'attrconf-code' => array_filter( (array) $view->param( 'b-attrconf-code', array() ) )
						);
					}

					foreach( $products as $values )
					{
						$this->_controller->editProduct(
							( isset( $values['position'] ) ? $values['position'] : null ),
							( isset( $values['quantity'] ) ? $values['quantity'] : 1 ),
							( isset( $values['attrconf-code'] ) ? array_filter( (array) $values['attrconf-code'] ) : array() )
						);
					}

					break;

				case 'delete':

					$products = (array) $view->param( 'b-position', array() );

					foreach( $products as $position ) {
						$this->_controller->deleteProduct( $position );
					}

					break;
			}

			$this->_process( $this->_subPartPath, $this->_subPartNames );

			$this->_controller->get()->check( MShop_Order_Item_Base_Abstract::PARTS_PRODUCT );
		}
		catch( Client_Html_Exception $e )
		{
			$view = $this->getView();
			$error = array( $this->_getContext()->getI18n()->dt( 'client/html', $e->getMessage() ) );
			$view->standardErrorList = $view->get( 'standardErrorList', array() ) + $error;
		}
		catch( Controller_Frontend_Exception $e )
		{
			$view = $this->getView();
			$error = array( $this->_getContext()->getI18n()->dt( 'controller/frontend', $e->getMessage() ) );
			$view->standardErrorList = $view->get( 'standardErrorList', array() ) + $error;
		}
		catch( MShop_Exception $e )
		{
			$view = $this->getView();
			$error = array( $this->_getContext()->getI18n()->dt( 'mshop', $e->getMessage() ) );
			$view->standardErrorList = $view->get( 'standardErrorList', array() ) + $error;
		}
		catch( Exception $e )
		{
			$context = $this->_getContext();
			$context->getLogger()->log( $e->getMessage() . PHP_EOL . $e->getTraceAsString() );

			$view = $this->getView();
			$error = array( $context->getI18n()->dt( 'client/html', 'A non-recoverable error occured' ) );
			$view->standardErrorList = $view->get( 'standardErrorList', array() ) + $error;
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
			$view->standardBasket = $this->_controller->get();

			$this->_cache = $view;
		}

		return $this->_cache;
	}
}