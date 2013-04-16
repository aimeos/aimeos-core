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

	private $_mandatory = array(
		'order.base.address.salutation',
		'order.base.address.firstname',
		'order.base.address.lastname',
		'order.base.address.address1',
		'order.base.address.postal',
		'order.base.address.city',
		'order.base.address.languageid',
		'order.base.address.email'
	);

	private $_optional = array(
		'order.base.address.company',
		'order.base.address.address2',
		'order.base.address.countryid'
	);


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @return string HTML code
	 */
	public function getBody()
	{
		$view = $this->_setViewParams( $this->getView() );

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
		$view = $this->_setViewParams( $this->getView() );

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

		try
		{
			// only start if there's something to do
			if( $view->param( 'ca-billing-option', null ) === null ) {
				return;
			}

			$context = $this->_getContext();
			$basketCtrl = Controller_Frontend_Basket_Factory::createController( $context );
			$basket = $basketCtrl->get();


			$type = MShop_Order_Item_Base_Address_Abstract::TYPE_BILLING;

			if( ( $option = $view->param( 'ca-billing-option', 'null' ) ) == 'null' ) // new address
			{
				$param = $view->param( 'ca-billing', array() );
				$list = $view->config( 'client/html/checkout/standard/address/billing/mandatory', $this->_mandatory );
				$optional = $view->config( 'client/html/checkout/standard/address/billing/optional', $this->_optional );
				$missing = array();

				foreach( $list as $mandatory )
				{
					if( !isset( $param[$mandatory] ) || $param[$mandatory] == '' )
					{
						$msg = $view->translate( 'client/html', 'Billing address part "%1$s" is missing' );
						$missing[$mandatory] = sprintf( $msg, substr( $mandatory, 19 ) );
					}
				}

				if( !isset( $missing['order.base.address.company'] )
					&& isset( $param['order.base.address.salutation'] )
					&& $param['order.base.address.salutation'] === MShop_Common_Item_Address_Abstract::SALUTATION_COMPANY
					&& in_array( 'order.base.address.company', $optional )
					&& $param['order.base.address.company'] == ''
				) {
					$msg = $view->translate( 'client/html', 'Billing address part "%1$s" is missing' );
					$missing['order.base.address.company'] = sprintf( $msg, 'salutation' );
				}

				if( count( $missing ) > 0 )
				{
					$view->billingError = $missing;
					throw new Client_Html_Exception( sprintf( 'At least one billing address part is missing' ) );
				}

				$basketCtrl->setAddress( $type, $param );
			}
			else // existing address
			{
				$customerManager = MShop_Customer_Manager_Factory::createManager( $context );

				$search = $customerManager->createSearch( true );
				$expr = array(
					$search->compare( '==', 'customer.id', $option ),
					$search->compare( '==', 'customer.code', $context->getEditor() ),
					$search->getConditions(),
				);
				$search->setConditions( $search->combine( '&&', $expr ) );

				$items = $customerManager->searchItems( $search );
				if( ( $item = reset( $items ) ) === false ) {
					throw new Client_Html_Exception( sprintf( 'No customer found for ID "%1$s"', $option ) );
				}

				$basketCtrl->setAddress( $type, $item->getBillingAddress() );
			}

			$this->_process( $this->_subPartPath, $this->_subPartNames );
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
	protected function _setViewParams( MW_View_Interface $view )
	{
		if( !isset( $this->_cache ) )
		{
			$context = $this->_getContext();
			$basketCntl = Controller_Frontend_Basket_Factory::createController( $context );

			try {
				$view->billingLanguage = $basketCntl->get()->getAddress( 'payment' )->getLanguageId();
			} catch( Exception $e ) {
				$view->billingLanguage = $context->getLocale()->getLanguageId();
			}

			$view->billingMandatory = $view->config( 'client/html/checkout/standard/address/billing/mandatory', $this->_mandatory );
			$view->billingOptional = $view->config( 'client/html/checkout/standard/address/billing/optional', $this->_optional );

			$this->_cache = $view;
		}

		return $this->_cache;
	}
}