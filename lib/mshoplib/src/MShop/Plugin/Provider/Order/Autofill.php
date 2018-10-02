<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


/**
 * Adds address and service items to the basket
 *
 * This plugins acts if a product is added to the basket or a delivery/payment
 * service is removed from the basket. It adds the a delivery/payment service
 * item and the customer address(es) to the basket.
 *
 * The following options are available:
 * - autofill.address: 1 (add billing address of the logged in customer to the basket)
 * - autofill.delivery: 1 (add the first delivery option to the basket)
 * - autofill.deliverycode: '...' and autofill.delivery: 1 (add specific delivery option to the basket)
 * - autofill.payment: 1 (add the first payment option to the basket)
 * - autofill.paymentcode: '...' and autofill.payment: 1 (add specific payment option to the basket)
 * - autofill.useorder: 1 (use last order of the customer to pre-fill addresses or services)
 * - autofill.orderservice: 1 (add delivery and payment services from the last order of the customer)
 * - autofill.orderaddress: 1 (add billing and delivery addresses from the last order of the customer)
 *
 * This plugin interacts with other plugins that add products or remove services!
 * Especially the "ServiceUpdate" plugin may remove a delivery/payment option
 * that isn't available any more based on the current basket content.
 *
 * To trace the execution and interaction of the plugins, set the log level to DEBUG:
 *	madmin/log/manager/standard/loglevel = 7
 *
 * @package MShop
 * @subpackage Plugin
 */
class Autofill
	extends \Aimeos\MShop\Plugin\Provider\Factory\Base
	implements \Aimeos\MShop\Plugin\Provider\Iface, \Aimeos\MShop\Plugin\Provider\Factory\Iface
{
	private $beConfig = array(
		'autofill.address' => array(
			'code' => 'autofill.address',
			'internalcode' => 'autofill.address',
			'label' => 'Add customer address automatically',
			'type' => 'boolean',
			'internaltype' => 'boolean',
			'default' => '',
			'required' => false,
		),
		'autofill.delivery' => array(
			'code' => 'autofill.delivery',
			'internalcode' => 'autofill.delivery',
			'label' => 'Add delivery option automatically',
			'type' => 'boolean',
			'internaltype' => 'boolean',
			'default' => '',
			'required' => false,
		),
		'autofill.deliverycode' => array(
			'code' => 'autofill.deliverycode',
			'internalcode' => 'autofill.deliverycode',
			'label' => 'Add delivery by code',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => '',
			'required' => false,
		),
		'autofill.payment' => array(
			'code' => 'autofill.payment',
			'internalcode' => 'autofill.payment',
			'label' => 'Add payment option automatically',
			'type' => 'boolean',
			'internaltype' => 'boolean',
			'default' => '',
			'required' => false,
		),
		'autofill.paymentcode' => array(
			'code' => 'autofill.paymentcode',
			'internalcode' => 'autofill.paymentcode',
			'label' => 'Add payment by code',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => '',
			'required' => false,
		),
		'autofill.useorder' => array(
			'code' => 'autofill.useorder',
			'internalcode' => 'autofill.useorder',
			'label' => 'Add from last order',
			'type' => 'boolean',
			'internaltype' => 'boolean',
			'default' => '',
			'required' => false,
		),
		'autofill.orderaddress' => array(
			'code' => 'autofill.orderaddress',
			'internalcode' => 'autofill.orderaddress',
			'label' => 'Add address from last order',
			'type' => 'boolean',
			'internaltype' => 'boolean',
			'default' => '',
			'required' => false,
		),
		'autofill.orderservice' => array(
			'code' => 'autofill.orderservice',
			'internalcode' => 'autofill.orderservice',
			'label' => 'Add delivery/payment from last order',
			'type' => 'boolean',
			'internaltype' => 'boolean',
			'default' => '',
			'required' => false,
		),
	);


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigBE( array $attributes )
	{
		$errors = parent::checkConfigBE( $attributes );

		return array_merge( $errors, $this->checkConfig( $this->beConfig, $attributes ) );
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigBE()
	{
		return $this->getConfigItems( $this->beConfig );
	}


	/**
	 * Subscribes itself to a publisher
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $p Object implementing publisher interface
	 */
	public function register( \Aimeos\MW\Observer\Publisher\Iface $p )
	{
		$p->addListener( $this->getObject(), 'addProduct.after' );
		$p->addListener( $this->getObject(), 'deleteService.after' );
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
		\Aimeos\MW\Common\Base::checkClass( '\\Aimeos\\MShop\\Order\\Item\\Base\\Iface', $order );

		$context = $this->getContext();
		$services = $order->getServices();
		$addresses = $order->getAddresses();

		if( ( $userid = $context->getUserId() ) !== null
			&& (bool) $this->getConfigValue( 'autofill.useorder', false ) === true
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

		$this->setAddressDefault( $order );
		$this->setServicesDefault( $order );

		return true;
	}


	/**
	 * Returns the order service item for the given type and code if available.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $order Basket of the customer
	 * @param string $type Service type constant from \Aimeos\MShop\Order\Item\Base\Service\Base
	 * @param string|null $code Service item code
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface|null Order service item if available or null otherwise
	 */
	protected function getServiceItem( \Aimeos\MShop\Order\Item\Base\Iface $order, $type, $code = null )
	{
		$context = $this->getContext();
		$serviceManager = \Aimeos\MShop\Factory::createManager( $context, 'service' );

		$search = $serviceManager->createSearch( true );

		$expr = [];

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
			$provider = $serviceManager->getProvider( $item, $item->getType() );

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

		if( empty( $addresses ) && (bool) $this->getConfigValue( 'autofill.orderaddress', true ) === true )
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
					$order->addService( $item, $type );
				}
			}
		}
	}


	/**
	 * Adds the default addresses to the basket if they are not available.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $order Basket object
	 */
	protected function setAddressDefault( \Aimeos\MShop\Order\Item\Base\Iface $order )
	{
		$context = $this->getContext();
		$addresses = $order->getAddresses();
		$type = \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT;

		if( $context->getUserId() !== null && !isset( $addresses[$type] )
			&& (bool) $this->getConfigValue( 'autofill.address', false ) === true
		) {
			$customerManager = \Aimeos\MShop\Factory::createManager( $context, 'customer' );
			$orderAddressManager = \Aimeos\MShop\Factory::createManager( $context, 'order/base/address' );

			$address = $customerManager->getItem( $context->getUserId() )->getPaymentAddress();

			$orderAddressItem = $orderAddressManager->createItem();
			$orderAddressItem->copyFrom( $address );

			$order->setAddress( $orderAddressItem, $type );
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
			$order->addService( $item, $type );
		}


		$type = \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_PAYMENT;

		if( !isset( $services[$type] ) && (bool) $this->getConfigValue( 'autofill.payment', false ) === true
			&& ( ( $item = $this->getServiceItem( $order, $type, $this->getConfigValue( 'autofill.paymentcode' ) ) ) !== null
			|| ( $item = $this->getServiceItem( $order, $type ) ) !== null )
		) {
			$order->addService( $item, $type );
		}
	}
}
