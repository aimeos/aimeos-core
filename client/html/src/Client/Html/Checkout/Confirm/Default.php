<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of confirm checkout HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Checkout_Confirm_Default
	extends Client_Html_Abstract
	implements Client_Html_Interface
{
	private $_subPartPath = 'client/html/checkout/confirm/default/subparts';
	private $_subPartNames = array( 'basic' );


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
		$view->confirmBody = $html;

		$tplconf = 'client/html/checkout/confirm/default/template-body';
		$default = 'checkout/confirm/body-default.html';

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
		$view->confirmHeader = $html;

		$tplconf = 'client/html/checkout/confirm/default/template-header';
		$default = 'checkout/confirm/header-default.html';

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
		return $this->_createSubClient( 'checkout/confirm/' . $type, $name );
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

		if( ( $orderid = $view->param( 'arcavias', null ) ) === null ) {
			return;
		}


		$context = $this->_getContext();
		$serviceManager = MShop_Service_Manager_Factory::createManager( $context );


		$customerManager = MShop_Customer_Manager_Factory::createManager( $context );

		$search = $customerManager->createSearch( true );
		$expr = array(
			$search->compare( '==', 'customer.code', $context->getEditor() ),
			$search->getConditions()
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$customerItems = $customerManager->searchItems( $search );

		if( ( $customerItem = reset( $customerItems ) ) === false ) {
			throw new Client_Html_Exception( sprintf( 'Invalid customer "%1$s"', $context->getEditor() ) );
		}


		$orderManager = MShop_Order_Manager_Factory::createManager( $context );

		$search = $orderManager->createSearch();
		$expr = array(
			$search->compare( '==', 'order.id', $orderid ),
			$search->compare( '==', 'order.base.customerid', $customerItem->getId() )
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$orderItems = $orderManager->searchItems( $search );

		if( ( $orderItem = reset( $orderItems ) ) === false ) {
			throw new Client_Html_Exception( sprintf( 'Invalid order ID "%1$s"', $orderid ) );
		}


		$orderBaseServiceManager = $orderManager->getSubManager( 'base' )->getSubManager( 'service' );

		$search = $orderBaseServiceManager->createSearch();
		$expr = array(
			$search->compare( '==', 'order.base.service.baseid', $orderItem->getBaseId() ),
			$search->compare( '==', 'order.base.service.type', 'payment' )
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		foreach( $orderBaseServiceManager->searchItems( $search ) as $orderServiceItem )
		{
			/** @todo Use getServiceId() as soon as the method is available */
			$search = $serviceManager->createSearch();
			$expr = array(
				$search->compare( '==', 'service.code', $orderServiceItem->getCode() ),
				$search->compare( '==', 'service.type.code', 'payment' )
			);
			$search->setConditions( $search->combine( '&&', $expr ) );

			$serviceItems = $serviceManager->searchItems( $search );

			if( ( $serviceItem = reset( $serviceItems ) ) !== false ) {
				$serviceManager->getProvider( $serviceItem )->updateSync( $view->param() );
			}
		}


		$this->_process( $this->_subPartPath, $this->_subPartNames );
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
			if( ( $orderid = $view->param( 'arcavias', null ) ) !== null )
			{
				$context = $this->_getContext();


				$customerManager = MShop_Customer_Manager_Factory::createManager( $context );

				$search = $customerManager->createSearch( true );
				$expr = array(
					$search->compare( '==', 'customer.code', $context->getEditor() ),
					$search->getConditions()
				);
				$search->setConditions( $search->combine( '&&', $expr ) );

				$customerItems = $customerManager->searchItems( $search );


				if( ( $customerItem = reset( $customerItems ) ) !== false )
				{
					$orderManager = MShop_Order_Manager_Factory::createManager( $context );

					$search = $orderManager->createSearch();
					$expr = array(
						$search->compare( '==', 'order.id', $orderid ),
						$search->compare( '==', 'order.base.customerid', $customerItem->getId() )
					);
					$search->setConditions( $search->combine( '&&', $expr ) );

					$orderItems = $orderManager->searchItems( $search );

					if( ( $orderItem = reset( $orderItems ) ) === false ) {
						throw new Client_Html_Exception( sprintf( 'Invalid order ID "%1$s"', $orderid ) );
					}

					$view->confirmOrderItem = $orderItem;
				}
			}

			$this->_cache = $view;
		}

		return $this->_cache;
	}
}