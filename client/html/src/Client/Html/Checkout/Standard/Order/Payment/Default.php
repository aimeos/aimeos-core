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
		$view = $this->getView();
		$basket = $view->orderBasket;
		$orderItem = $view->orderItem;
		$context = $this->_getContext();


		$target = $view->config( 'client/html/checkout/confirm/url/target' );
		$controller = $view->config( 'client/html/checkout/confirm/url/controller', 'checkout' );
		$action = $view->config( 'client/html/checkout/confirm/url/action', 'confirm' );
		$config = $view->config( 'client/html/checkout/confirm/url/config', array( 'absoluteUri' => true ) );

		$confirmUrl = $view->url( $target, $controller, $action, array(), array(), $config );

		$target = $view->config( 'client/html/checkout/update/url/target' );
		$controller = $view->config( 'client/html/checkout/update/url/controller', 'checkout' );
		$action = $view->config( 'client/html/checkout/update/url/action', 'update' );
		$config = $view->config( 'client/html/checkout/update/url/config', array( 'absoluteUri' => true ) );

		$updateUrl = $view->url( $target, $controller, $action, array(), array(), $config );

		$config = array( 'payment.url-success' => $confirmUrl, 'payment.url-update' => $updateUrl );


		try
		{
			$service = $basket->getService( 'payment' );

			$manager = MShop_Service_Manager_Factory::createManager( $context );
			$provider = $manager->getProvider( $manager->getItem( $service->getServiceId() ) );
			$provider->injectGlobalConfigBE( $config );

			$view->paymentForm = $provider->process( $orderItem );
		}
		catch( MShop_Order_Exception $e )
		{
			$view->paymentForm = new MShop_Common_Item_Helper_Form_Default( $confirmUrl, 'REDIRECT' );
		}

		if( !isset( $view->paymentForm ) || $view->paymentForm === null )
		{
			$msg = sprintf( 'Invalid process response from service provider with code "%1$s"', $service->getCode() );
			throw new Client_Html_Exception( $msg );
		}

		$this->_process( $this->_subPartPath, $this->_subPartNames );
	}
}