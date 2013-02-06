<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


// Strings for translation
_('payment');


/**
 * Default implementation of checkout payment HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Checkout_Standard_Payment_Default
	extends Client_Html_Abstract
	implements Client_Html_Interface
{
	private $_cache;
	private $_subPartPath = 'client/html/checkout/standard/payment/default/subparts';
	private $_subPartNames = array();


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @return string HTML code
	 */
	public function getBody()
	{
		$view = $this->getView();

		if( $view->get( 'standardStepActive' ) != 'payment' ) {
			return '';
		}

		$view = $this->_setViewParams( $view );

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getBody();
		}
		$view->paymentBody = $html;

		$tplconf = 'client/html/checkout/standard/payment/default/template-body';
		$default = 'checkout/standard/payment-body-default.html';

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

		if( $view->get( 'standardStepActive' ) != 'payment' ) {
			return '';
		}

		$view = $this->_setViewParams( $view );

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getHeader();
		}
		$view->paymentHeader = $html;

		$tplconf = 'client/html/checkout/standard/payment/default/template-header';
		$default = 'checkout/standard/payment-header-default.html';

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
		return $this->_createSubClient( 'checkout/standard/payment/' . $type, $name );
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
		if( ( $serviceId = $view->param( 'c-payment-option', null ) ) === null ) {
			return;
		}

		try
		{
			$context = $this->_getContext();

			$serviceCtrl = Controller_Frontend_Service_Factory::createController( $context );

			$attributes = $view->param( 'c-payment/' . $serviceId, array() );
			$errors = $serviceCtrl->checkServiceAttributes( 'payment', $serviceId, $attributes );

			foreach( $errors as $key => $msg )
			{
				if( $msg === null ) {
					unset( $errors[$key] );
				}
			}

			if( count( $errors ) === 0 )
			{
				$basketCtrl = Controller_Frontend_Basket_Factory::createController( $context );
				$basketCtrl->setService( 'payment', $serviceId, $attributes );
			}
			else
			{
				$view->standardStepActive = 'payment';
			}

			$view->paymentError = $errors;

			$this->_process( $this->_subPartPath, $this->_subPartNames );
		}
		catch( Exception $e )
		{
			$view->standardStepActive = 'payment';

			$error = array(
				'An error occured while processing your request. Please re-check your input',
				$e->getMessage()
			);
			$view->standardErrorList = $error + $view->get( 'standardErrorList', array() );
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
			$context = $this->_getContext();

			$basketCntl = Controller_Frontend_Basket_Factory::createController( $context );
			$serviceCntl = Controller_Frontend_Service_Factory::createController( $context );

			$basket = $basketCntl->get();

			$services = $serviceCntl->getServices( 'payment', $basket );
			$serviceAttributes = $servicePrices = array();

			foreach( $services as $id => $service )
			{
				$serviceAttributes[$id] = $serviceCntl->getServiceAttributes( 'payment', $id, $basket );
				$servicePrices[$id] = $serviceCntl->getServicePrice( 'payment', $id, $basket );
			}

			$view->paymentServices = $services;
			$view->paymentServiceAttributes = $serviceAttributes;
			$view->paymentServicePrices = $servicePrices;

			$this->_cache = $view;
		}

		return $this->_cache;
	}
}