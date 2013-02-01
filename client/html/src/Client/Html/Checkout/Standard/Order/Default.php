<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


// Strings for translation
_('order');


/**
 * Default implementation of checkout order HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Checkout_Standard_Order_Default
	extends Client_Html_Abstract
	implements Client_Html_Interface
{
	private $_cache;
	private $_subPartPath = 'client/html/checkout/standard/order/default/subparts';
	private $_subPartNames = array( 'payment' );


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @return string HTML code
	 */
	public function getBody()
	{
		$view = $this->getView();

		if( $view->get( 'standardStepActive' ) != 'order' ) {
			return '';
		}

		$view = $this->getView( $view );

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getBody();
		}
		$view->orderBody = $html;

		$tplconf = 'client/html/checkout/standard/order/default/template-body';
		$default = 'checkout/standard/order-body-default.html';

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

		if( $view->get( 'standardStepActive' ) != 'order' ) {
			return '';
		}

		$view = $this->getView( $view );

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getHeader();
		}
		$view->orderHeader = $html;

		$tplconf = 'client/html/checkout/standard/order/default/template-header';
		$default = 'checkout/standard/order-header-default.html';

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
		return $this->_createSubClient( 'checkout/standard/order/' . $type, $name );
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
	 * Processes the input, e.g. store given order.
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 */
	public function process()
	{
		$view = $this->getView();

		// only start if there's something to do
		if( ( $option = $view->param( 'cs-order', null ) ) === null
			|| $view->get( 'standardStepActive' ) !== null
		) {
			return;
		}

		try
		{
			$context = $this->_getContext();

			$controller = Controller_Frontend_Basket_Factory::createController( $context );
			$orderManager = MShop_Order_Manager_Factory::createManager( $context );
			$orderBaseManager = $orderManager->getSubManager( 'base' );

			$basket = $controller->get();
			$orderBaseManager->store( $basket );

			$orderItem = $orderManager->createItem();
			$orderItem->setBaseId( $basket->getId() );
			$orderItem->setType( MShop_Order_Item_Abstract::TYPE_WEB );
			$orderManager->saveItem( $orderItem );

			$view->orderItem = $orderItem;

			$this->_process( $this->_subPartPath, $this->_subPartNames );
		}
		catch( Exception $e )
		{
			$error = array( 'An error occured while placing your order' );
			$view->standardErrorList = $error + $view->get( 'standardErrorList', array() );
		}
	}
}