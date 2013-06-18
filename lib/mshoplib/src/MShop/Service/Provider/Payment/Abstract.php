<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Service
 */


/**
 * Abstract class for all payment provider implementations.
 *
 * @package MShop
 * @subpackage Service
 */
abstract class MShop_Service_Provider_Payment_Abstract
extends MShop_Service_Provider_Abstract
implements MShop_Service_Provider_Payment_Interface
{
	/**
	 * Feature constant if querying for status updates for an order is supported.
	 */
	const FEAT_QUERY = 1;

	/**
	 * Feature constant if canceling authorizations is supported.
	 */
	const FEAT_CANCEL = 2;

	/**
	 * Feature constant if money authorization and later capture is supported.
	 */
	const FEAT_CAPTURE = 3;

	/**
	 * Feature constant if refunding payments is supported.
	 */
	const FEAT_REFUND = 4;


	/**
	 * Cancels the authorization for the given order if supported.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object
	 */
	public function cancel( MShop_Order_Item_Interface $order )
	{
		throw new MShop_Service_Exception( sprintf( 'Method "%1$s" for provider not available', 'cancel' ) );
	}


	/**
	 * Captures the money later on request for the given order if supported.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object
	 */
	public function capture( MShop_Order_Item_Interface $order )
	{
		throw new MShop_Service_Exception( sprintf( 'Method "%1$s" for provider not available', 'capture' ) );
	}


	/**
	 * Refunds the money for the given order if supported.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object
	 */
	public function refund( MShop_Order_Item_Interface $order )
	{
		throw new MShop_Service_Exception( sprintf( 'Method "%1$s" for provider not available', 'refund' ) );
	}


	/**
	 * Sets or adds a attribute value to the list of service payment items.
	 *
	 * @param array &$attributes Associative array of existing service attributes code/item pairs
	 * @param string $code Payment attribute code
	 * @param string $value Payment attribute value
	 * @param integer $serviceId Order service ID this attributes should be added to
	 */
	protected function _setValue( array &$attributes, $code, $value, $serviceId )
	{
		if( isset( $attributes[ $code ] ) )
		{
			$attributes[ $code ]->setValue( utf8_encode( $value ) );
			return;
		}

		$orderManager = MShop_Order_Manager_Factory::createManager( $this->_getContext() );
		$orderServiceManager = $orderManager->getSubManager( 'base' )->getSubManager( 'service' );
		$attributeManager = $orderServiceManager->getSubManager( 'attribute' );

		$item = $attributeManager->createItem();
		$item->setCode( $code );
		$item->setValue( utf8_encode( $value ) );
		$item->setServiceId( $serviceId );

		$attributes[ $code ] = $item;
	}
}