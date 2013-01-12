<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of checkout billing address HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Checkout_Standard_Address_Billing_Default
	extends Client_Html_Abstract
	implements Client_Html_Interface
{
	private $_cache;
	private $_subPartPath = 'client/html/checkout/standard/address/billing/default/subparts';
	private $_subPartNames = array();


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
		$view->billingBody = $html;

		$tplconf = 'client/html/checkout/standard/address/billing/default/template-body';
		$default = 'checkout/standard/address-billing-body-default.html';

		return $view->render( $this->_getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @return string String including HTML tags for the header
	 */
	public function getHeader()
	{
		$view = $this->_process( $this->getView() );

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getHeader();
		}
		$view->billingHeader = $html;

		$tplconf = 'client/html/checkout/standard/address/billing/default/template-header';
		$default = 'checkout/standard/address-billing-header-default.html';

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
		return $this->_createSubClient( 'checkout/standard/address/billing/' . $type, $name );
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
	 * Stores the given or fetched billing address in the basket.
	 */
	public function process()
	{
		$view = $this->getView();

		// only start if there's something to do
		if( $view->param( 'ca-billing-option', null ) === null ) {
			return;
		}

		$basketCtrl = Controller_Frontend_Basket_Factory::createController( $this->_getContext() );
		$basket = $basketCtrl->get();

		try
		{
			$type = MShop_Order_Item_Base_Address_Abstract::TYPE_BILLING;

			if( ( $option = $view->param( 'ca-billing-option', '' ) ) == '' ) // new address
			{
				$param = $view->param( 'ca-billing', array() );

				$missing = array();
				$default = array(
					'order.base.address.salutation',
					'order.base.address.firstname',
					'order.base.address.lastname',
					'order.base.address.address1',
					'order.base.address.postal',
					'order.base.address.city',
					'order.base.address.langid',
					'order.base.address.email'
				);

				foreach( $view->config( 'checkout/address/billing/mandatory', $default ) as $mandatory )
				{
					if( !isset( $param[$mandatory] ) ) {
						$missing[$mandatory] = sprintf( 'Billing address part "%1$s" is missing', $mandatory );
					}
				}

				if( count( $missing ) > 0 )
				{
					$view->billingError = $missing;
					throw new Client_Html_Exception( 'At least one billing address part is missing' );
				}

				$basketCtrl->setAddress( $type, $param );
			}
			else // existing address
			{
				/** @todo check that only addresses for the logged in customer can be added */
				$customerManager = MShop_Customer_Manager_Factory::createManager( $this->_getContext() );
				$basketCtrl->setAddress( $type, $customerManager->getItem( $option )->getBillingAddress() );
			}

			$view->billingAddress = $basket->getAddress( $type );
		}
		catch( Controller_Frontend_Exception $e )
		{
			$view->billingError = $e->getErrorList();
			throw $e;
		}
	}


	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param MW_View_Interface $view The view object which generates the HTML output
	 * @return MW_View_Interface Modified view object
	 */
	protected function _process( MW_View_Interface $view )
	{
		if( !isset( $this->_cache ) )
		{
			$default = array(
				'order.base.address.salutation',
				'order.base.address.firstname',
				'order.base.address.lastname',
				'order.base.address.address1',
				'order.base.address.postal',
				'order.base.address.city',
				'order.base.address.langid',
				'order.base.address.email'
			);

			$view->billingMandatory = $view->config( 'checkout/address/billing/mandatory', $default );


			$default = array(
				'order.base.address.company',
				'order.base.address.address2',
				'order.base.address.countryid',
			);

			$view->billingOptional = $view->config( 'checkout/address/billing/optional', $default );


			$this->_cache = $view;
		}

		return $this->_cache;
	}
}