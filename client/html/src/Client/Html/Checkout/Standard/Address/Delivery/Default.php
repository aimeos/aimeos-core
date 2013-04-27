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
class Client_Html_Checkout_Standard_Address_Delivery_Default
	extends Client_Html_Abstract
	implements Client_Html_Interface
{
	private $_cache;
	private $_subPartPath = 'client/html/checkout/standard/address/delivery/default/subparts';
	private $_subPartNames = array();

	private $_mandatory = array(
		'order.base.address.salutation',
		'order.base.address.firstname',
		'order.base.address.lastname',
		'order.base.address.address1',
		'order.base.address.postal',
		'order.base.address.city',
		'order.base.address.languageid',
	);

	private $_optional = array(
		'order.base.address.company',
		'order.base.address.address2',
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
		$view->deliveryBody = $html;

		$tplconf = 'client/html/checkout/standard/address/delivery/default/template-body';
		$default = 'checkout/standard/address-delivery-body-default.html';

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
		$view->deliveryHeader = $html;

		$tplconf = 'client/html/checkout/standard/address/delivery/default/template-header';
		$default = 'checkout/standard/address-delivery-header-default.html';

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
		return $this->_createSubClient( 'checkout/standard/address/delivery/' . $type, $name );
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
			if( $view->param( 'ca-delivery-option', null ) === null ) {
				return;
			}

			$context = $this->_getContext();
			$basketCtrl = Controller_Frontend_Basket_Factory::createController( $context );
			$basket = $basketCtrl->get();


			$type = MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY;

			if( ( $option = $view->param( 'ca-delivery-option', 'null' ) ) === 'null' ) // new address
			{
				$param = $view->param( 'ca-delivery', array() );
				$list = $view->config( 'client/html/checkout/standard/address/delivery/mandatory', $this->_mandatory );
				$optional = $view->config( 'client/html/checkout/standard/address/billing/optional', $this->_optional );
				$missing = array();

				foreach( $list as $mandatory )
				{
					if( !isset( $param[$mandatory] ) || $param[$mandatory] == '' )
					{
						$msg = $view->translate( 'client/html', 'Delivery address part "%1$s" is missing' );
						$missing[$mandatory] = sprintf( $msg, substr( $mandatory, 19 ) );
					}
				}

				if( !isset( $missing['order.base.address.company'] )
					&& isset( $param['order.base.address.salutation'] )
					&& $param['order.base.address.salutation'] === MShop_Common_Item_Address_Abstract::SALUTATION_COMPANY
					&& in_array( 'order.base.address.company', $optional )
					&& $param['order.base.address.company'] == ''
				) {
					$msg = $view->translate( 'client/html', 'Delivery address part "%1$s" is missing' );
					$missing['order.base.address.company'] = sprintf( $msg, 'salutation' );
				}

				if( count( $missing ) > 0 )
				{
					$view->deliveryError = $missing;
					throw new Client_Html_Exception( sprintf( 'At least one delivery address part is missing' ) );
				}

				$basketCtrl->setAddress( $type, $param );
			}
			else if( ( $option = $view->param( 'ca-delivery-option', 'null' ) ) !== '-1' ) // existing address
			{
				$customerManager = MShop_Customer_Manager_Factory::createManager( $context );
				$address = $customerManager->getSubManager( 'address' )->getItem( $option );

				$search = $customerManager->createSearch( true );
				$expr = array(
					$search->compare( '==', 'customer.id', $address->getRefId() ),
					$search->getConditions(),
				);
				$search->setConditions( $search->combine( '&&', $expr ) );

				$items = $customerManager->searchItems( $search );

				if( ( $item = reset( $items ) ) === false || $address->getRefId() != $context->getUserId() ) {
					throw new Client_Html_Exception( sprintf( 'No address found for ID "%1$s"', $option ) );
				}

				$basketCtrl->setAddress( $type, $address );
			}
			else
			{
				$basketCtrl->setAddress( $type, null );
			}

			$this->_process( $this->_subPartPath, $this->_subPartNames );
		}
		catch( Controller_Frontend_Exception $e )
		{
			$view->deliveryError = $e->getErrorList();
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
				$view->deliveryLanguage = $basketCntl->get()->getAddress( 'delivery' )->getLanguageId();
			} catch( Exception $e ) {
				$view->deliveryLanguage = $context->getLocale()->getLanguageId();
			}

			$salutations = array( 'company', 'mr', 'mrs' );
			$view->deliverySalutations = $view->config( 'client/html/common/address/delivery/salutations', $salutations );

			$view->deliveryMandatory = $view->config( 'client/html/checkout/standard/address/delivery/mandatory', $this->_mandatory );
			$view->deliveryOptional = $view->config( 'client/html/checkout/standard/address/delivery/optional', $this->_optional );


			$this->_cache = $view;
		}

		return $this->_cache;
	}
}