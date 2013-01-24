<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of checkout payment order HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Checkout_Standard_Order_Payment_Default
	extends Client_Html_Abstract
	implements Client_Html_Interface
{
	private $_cache;
	private $_subPartPath = 'client/html/checkout/standard/order/payment/default/subparts';
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
		$view->paymentBody = $html;

		$tplconf = 'client/html/checkout/standard/order/payment/default/template-body';
		$default = 'checkout/standard/order-payment-body-default.html';

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
		$view->paymentHeader = $html;

		$tplconf = 'client/html/checkout/standard/order/payment/default/template-header';
		$default = 'checkout/standard/order-payment-header-default.html';

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
		return $this->_createSubClient( 'checkout/standard/order/payment/' . $type, $name );
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
	 * Processes the input, e.g. provides the payment form.
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 */
	public function process()
	{
		try
		{
			$view = $this->getView();
			$context = $this->_getContext();

			$controller = Controller_Frontend_Basket_Factory::createController( $context );
			$service = $controller->get()->getService( 'payment' );

			$manager = MShop_Service_Manager_Factory::createManager( $context );
			$provider = $manager->getProvider( $manager->getItem( $service->serviceId ) );


			$url = $view->url( $view->config( 'confirm-target' ), 'basket', 'confirm' );

			if( strpos( $url, '?' ) === false ) {
				$url .= '?';
			} else {
				$url .= '&';
			}

			$url .= '&arcavias=' . $view->orderItem->getId();

			$view->paymentForm = $provider->process( $view->orderItem );
			$view->paymentUrl = $url;

			$this->_process( $this->_subPartPath, $this->_subPartNames );
		}
		catch( Exception $e )
		{
			$error = array( 'An error occured while processing the payment: ' . $e->getMessage() );
			$view->standardErrorList = $error + $view->get( 'standardErrorList', array() );
		}
	}
}