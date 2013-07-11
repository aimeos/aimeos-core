<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of checkout address order HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Checkout_Standard_Order_Address_Default
	extends Client_Html_Abstract
	implements Client_Html_Interface
{
	private $_cache;
	private $_subPartPath = 'client/html/checkout/standard/order/address/default/subparts';
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
		$view->addressBody = $html;

		$tplconf = 'client/html/checkout/standard/order/address/default/template-body';
		$default = 'checkout/standard/order-address-body-default.html';

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
		$view->addressHeader = $html;

		$tplconf = 'client/html/checkout/standard/order/address/default/template-header';
		$default = 'checkout/standard/order-address-header-default.html';

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
		return $this->_createSubClient( 'checkout/standard/order/address/' . $type, $name );
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
	 * Processes the input, e.g. provides the address form.
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 */
	public function process()
	{
		$view = $this->getView();
		$basket = $view->orderBasket;
		$customerId = $basket->getCustomerId();

		try
		{
			$addr = $basket->getAddress( MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY );

			if( $customerId != '' && $addr->getAddressId() == '' )
			{
				$manager = MShop_Customer_Manager_Factory::createManager( $this->_getContext() );
				$addrManager = $manager->getSubManager( 'address' );

				$item = $addrManager->createItem();
				$item->setRefId( $customerId );
				$item->copyFrom( $addr );

				$addrManager->saveItem( $item );

				$addr->setAddressId( $item->getId() );
			}
		}
		catch( Exception $e )
		{
			$msg = sprintf( 'Unable to save address for customer "%1$s": %2$s', $customerId, $e->getMessage() );
			$this->_getContext()->getLogger()->log( $msg, MW_Logger_Abstract::DEBUG );
		}

		$this->_process( $this->_subPartPath, $this->_subPartNames );
	}
}