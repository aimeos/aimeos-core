<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Plugin
 */


/**
 * Adds address and service items to basket.
 *
 * @package MShop
 * @subpackage Plugin
 */
class MShop_Plugin_Provider_Order_Autofill
	extends MShop_Plugin_Provider_Factory_Abstract
	implements MShop_Plugin_Provider_Factory_Interface
{
	/**
	 * Subscribes itself to a publisher
	 *
	 * @param MW_Observer_Publisher_Interface $p Object implementing publisher interface
	 */
	public function register( MW_Observer_Publisher_Interface $p )
	{
		$p->addListener( $this, 'addProduct.after' );
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param MW_Observer_Publisher_Interface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @throws MShop_Plugin_Provider_Exception if an error occurs
	 * @return bool true if subsequent plugins should be processed
	 */
	public function update( MW_Observer_Publisher_Interface $order, $action, $value = null )
	{
		$class = 'MShop_Order_Item_Base_Interface';
		if( !( $order instanceof $class ) ) {
			throw new MShop_Plugin_Exception( sprintf( 'Object is not of required type "%1$s"', $class ) );
		}


		$context = $this->getContext();
		$services = $order->getServices();
		$addresses = $order->getAddresses();

		if( ( $userid = $context->getUserId() ) !== null
			&& $this->getConfigValue( 'autofill.useorder', true ) == true
			&& ( empty( $addresses ) || empty( $services ) )
		) {
			$orderManager = MShop_Factory::createManager( $context, 'order' );

			$search = $orderManager->createSearch();
			$search->setConditions( $search->compare( '==', 'order.base.customerid', $userid ) );
			$search->setSortations( array( $search->sort( '-', 'order.ctime' ) ) );
			$search->setSlice( 0, 1 );

			$result = $orderManager->searchItems( $search );

			if( ( $item = reset( $result ) ) !== false )
			{
				$this->setAddresses( $order, $item );
				$this->setServices( $order, $item );
			}
		}

		$this->setServicesDefault( $order );

		return true;
	}


	/**
	 * Returns the order service item for the given type and code if available.
	 *
	 * @param MShop_Order_Item_Base_Interface $order Basket of the customer
	 * @param string $type Service type constant from MShop_Order_Item_Base_Service_Abstract
	 * @param string|null $code Service item code
	 * @param MShop_Order_Item_Base_Service_Interface|null Order service item if available or null otherwise
	 */
	protected function getServiceItem( MShop_Order_Item_Base_Interface $order, $type, $code = null )
	{
		$context = $this->getContext();
		$serviceManager = MShop_Factory::createManager( $context, 'service' );

		$search = $serviceManager->createSearch( true );

		$expr = array();

		if( $code !== null ) {
			$expr[] = $search->compare( '==', 'service.code', $code );
		}

		$expr[] = $search->compare( '==', 'service.type.code', $type );
		$expr[] = $search->getConditions();

		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSortations( array( $search->sort( '+', 'service.position' ) ) );

		$result = $serviceManager->searchItems( $search, array( 'media', 'price', 'text' ) );

		foreach( $result as $item )
		{
			$provider = $serviceManager->getProvider( $item );

			if( $provider->isAvailable( $order ) === true )
			{
				$orderServiceManager = MShop_Factory::createManager( $context, 'order/base/service' );
				$orderServiceItem = $orderServiceManager->createItem();
				$orderServiceItem->copyFrom( $item );
				$orderServiceItem->setPrice( $provider->calcPrice( $order ) );

				return $orderServiceItem;
			}
		}
	}


	/**
	 * Adds the addresses from the given order item to the basket.
	 *
	 * @param MShop_Order_Item_Base_Interface $order Basket object
	 * @param MShop_Order_Item_Interface $item Existing order to fetch the addresses from
	 */
	protected function setAddresses( MShop_Order_Item_Base_Interface $order, MShop_Order_Item_Interface $item )
	{
		$addresses = $order->getAddresses();

		if( empty( $addresses ) && $this->getConfigValue( 'autofill.orderaddress', true ) == true )
		{
			$manager = MShop_Factory::createManager( $this->getContext(), 'order/base/address' );
			$search = $manager->createSearch();
			$search->setConditions( $search->compare( '==', 'order.base.address.baseid', $item->getBaseId() ) );
			$addresses = $manager->searchItems( $search );

			foreach( $addresses as $address ) {
				$order->setAddress( $address, $address->getType() );
			}
		}
	}


	/**
	 * Adds the services from the given order item to the basket.
	 *
	 * @param MShop_Order_Item_Base_Interface $order Basket object
	 * @param MShop_Order_Item_Interface $item Existing order to fetch the services from
	 */
	protected function setServices( MShop_Order_Item_Base_Interface $order, MShop_Order_Item_Interface $item )
	{
		$services = $order->getServices();

		if( empty( $services ) && $this->getConfigValue( 'autofill.orderservice', true ) == true )
		{
			$manager = MShop_Factory::createManager( $this->getContext(), 'order/base/service' );
			$search = $manager->createSearch();
			$search->setConditions( $search->compare( '==', 'order.base.service.baseid', $item->getBaseId() ) );
			$services = $manager->searchItems( $search );

			foreach( $services as $service )
			{
				$type = $service->getType();

				if( ( $item = $this->getServiceItem( $order, $type, $service->getCode() ) ) !== null ) {
					$order->setService( $item, $type );
				}
			}
		}
	}


	/**
	 * Adds the default services to the basket if they are not available.
	 *
	 * @param MShop_Order_Item_Base_Interface $order Basket object
	 */
	protected function setServicesDefault( MShop_Order_Item_Base_Interface $order )
	{
		$services = $order->getServices();

		$type = MShop_Order_Item_Base_Service_Abstract::TYPE_DELIVERY;

		if( !isset( $services[$type] ) && (bool) $this->getConfigValue( 'autofill.delivery', false ) === true
			&& ( ( $item = $this->getServiceItem( $order, $type, $this->getConfigValue( 'autofill.deliverycode' ) ) ) !== null
			|| ( $item = $this->getServiceItem( $order, $type ) ) !== null )
		) {
			$order->setService( $item, $type );
		}


		$type = MShop_Order_Item_Base_Service_Abstract::TYPE_PAYMENT;

		if( !isset( $services[$type] ) && (bool) $this->getConfigValue( 'autofill.payment', false ) === true
			&& ( ( $item = $this->getServiceItem( $order, $type, $this->getConfigValue( 'autofill.paymentcode' ) ) ) !== null
			|| ( $item = $this->getServiceItem( $order, $type ) ) !== null )
		) {
			$order->setService( $item, $type );
		}
	}
}