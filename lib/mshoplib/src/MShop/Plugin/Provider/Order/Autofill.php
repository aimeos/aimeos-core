<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


/**
 * Adds address and service items to basket.
 *
 * @package MShop
 * @subpackage Plugin
 */
class Autofill
	extends \Aimeos\MShop\Plugin\Provider\Factory\Base
	implements \Aimeos\MShop\Plugin\Provider\Factory\Iface
{
	/**
	 * Subscribes itself to a publisher
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $p Object implementing publisher interface
	 */
	public function register( \Aimeos\MW\Observer\Publisher\Iface $p )
	{
		$p->addListener( $this, 'addProduct.after' );
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception if an error occurs
	 * @return bool true if subsequent plugins should be processed
	 */
	public function update( \Aimeos\MW\Observer\Publisher\Iface $order, $action, $value = null )
	{
		$class = '\\Aimeos\\MShop\\Order\\Item\\Base\\Iface';
		if( !( $order instanceof $class ) ) {
			throw new \Aimeos\MShop\Plugin\Exception( sprintf( 'Object is not of required type "%1$s"', $class ) );
		}


		$context = $this->getContext();
		$services = $order->getServices();
		$addresses = $order->getAddresses();

		if( ( $userid = $context->getUserId() ) !== null
			&& $this->getConfigValue( 'autofill.useorder', true ) == true
			&& ( empty( $addresses ) || empty( $services ) )
		) {
			$orderManager = \Aimeos\MShop\Factory::createManager( $context, 'order' );

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
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $order Basket of the customer
	 * @param string $type Service type constant from \Aimeos\MShop\Order\Item\Base\Service\Base
	 * @param string|null $code Service item code
	 * @param \Aimeos\MShop\Order\Item\Base\Service\Iface|null Order service item if available or null otherwise
	 */
	protected function getServiceItem( \Aimeos\MShop\Order\Item\Base\Iface $order, $type, $code = null )
	{
		$context = $this->getContext();
		$serviceManager = \Aimeos\MShop\Factory::createManager( $context, 'service' );

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
				$orderServiceManager = \Aimeos\MShop\Factory::createManager( $context, 'order/base/service' );
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
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $order Basket object
	 * @param \Aimeos\MShop\Order\Item\Iface $item Existing order to fetch the addresses from
	 */
	protected function setAddresses( \Aimeos\MShop\Order\Item\Base\Iface $order, \Aimeos\MShop\Order\Item\Iface $item )
	{
		$addresses = $order->getAddresses();

		if( empty( $addresses ) && $this->getConfigValue( 'autofill.orderaddress', true ) == true )
		{
			$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'order/base/address' );
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
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $order Basket object
	 * @param \Aimeos\MShop\Order\Item\Iface $item Existing order to fetch the services from
	 */
	protected function setServices( \Aimeos\MShop\Order\Item\Base\Iface $order, \Aimeos\MShop\Order\Item\Iface $item )
	{
		$services = $order->getServices();

		if( empty( $services ) && $this->getConfigValue( 'autofill.orderservice', true ) == true )
		{
			$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'order/base/service' );
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
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $order Basket object
	 */
	protected function setServicesDefault( \Aimeos\MShop\Order\Item\Base\Iface $order )
	{
		$services = $order->getServices();

		$type = \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_DELIVERY;

		if( !isset( $services[$type] ) && (bool) $this->getConfigValue( 'autofill.delivery', false ) === true
			&& ( ( $item = $this->getServiceItem( $order, $type, $this->getConfigValue( 'autofill.deliverycode' ) ) ) !== null
			|| ( $item = $this->getServiceItem( $order, $type ) ) !== null )
		) {
			$order->setService( $item, $type );
		}


		$type = \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_PAYMENT;

		if( !isset( $services[$type] ) && (bool) $this->getConfigValue( 'autofill.payment', false ) === true
			&& ( ( $item = $this->getServiceItem( $order, $type, $this->getConfigValue( 'autofill.paymentcode' ) ) ) !== null
			|| ( $item = $this->getServiceItem( $order, $type ) ) !== null )
		) {
			$order->setService( $item, $type );
		}
	}
}