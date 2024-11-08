<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2024
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
 * - address: 1 (add billing address of the logged in customer to the basket)
 * - delivery: 1 (add the first delivery option to the basket)
 * - deliverycode: '...' and delivery: 1 (add specific delivery option to the basket)
 * - payment: 1 (add the first payment option to the basket)
 * - paymentcode: '...' and payment: 1 (add specific payment option to the basket)
 * - useorder: 1 (use last order of the customer to pre-fill addresses or services)
 * - orderservice: 1 (add delivery and payment services from the last order of the customer)
 * - orderaddress: 1 (add billing and delivery addresses from the last order of the customer)
 *
 * This plugin interacts with other plugins that add products or remove services!
 * Especially the "ServiceUpdate" plugin may remove a delivery/payment option
 * that isn't available any more based on the current basket content.
 *
 * To trace the execution and interaction of the plugins, set the log level to DEBUG:
 *	madmin/log/manager/loglevel = 7
 *
 * @package MShop
 * @subpackage Plugin
 */
class Autofill
	extends \Aimeos\MShop\Plugin\Provider\Factory\Base
	implements \Aimeos\MShop\Plugin\Provider\Iface, \Aimeos\MShop\Plugin\Provider\Factory\Iface
{
	private array $beConfig = array(
		'address' => array(
			'code' => 'address',
			'internalcode' => 'address',
			'label' => 'Add customer address automatically',
			'type' => 'bool',
			'default' => '',
			'required' => false,
		),
		'delivery' => array(
			'code' => 'delivery',
			'internalcode' => 'delivery',
			'label' => 'Add delivery option automatically',
			'type' => 'bool',
			'default' => '',
			'required' => false,
		),
		'deliverycode' => array(
			'code' => 'deliverycode',
			'internalcode' => 'deliverycode',
			'label' => 'Add delivery by code',
			'default' => '',
			'required' => false,
		),
		'payment' => array(
			'code' => 'payment',
			'internalcode' => 'payment',
			'label' => 'Add payment option automatically',
			'type' => 'bool',
			'default' => '',
			'required' => false,
		),
		'paymentcode' => array(
			'code' => 'paymentcode',
			'internalcode' => 'paymentcode',
			'label' => 'Add payment by code',
			'default' => '',
			'required' => false,
		),
		'useorder' => array(
			'code' => 'useorder',
			'internalcode' => 'useorder',
			'label' => 'Add from last order',
			'type' => 'bool',
			'default' => '',
			'required' => false,
		),
		'orderaddress' => array(
			'code' => 'orderaddress',
			'internalcode' => 'orderaddress',
			'label' => 'Add address from last order',
			'type' => 'bool',
			'default' => '',
			'required' => false,
		),
		'orderservice' => array(
			'code' => 'orderservice',
			'internalcode' => 'orderservice',
			'label' => 'Add delivery/payment from last order',
			'type' => 'bool',
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
	public function checkConfigBE( array $attributes ) : array
	{
		$errors = parent::checkConfigBE( $attributes );

		return array_merge( $errors, $this->checkConfig( $this->beConfig, $attributes ) );
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
	{
		return $this->getConfigItems( $this->beConfig );
	}


	/**
	 * Subscribes itself to a publisher
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $p Object implementing publisher interface
	 * @return \Aimeos\MShop\Plugin\Provider\Iface Plugin object for method chaining
	 */
	public function register( \Aimeos\MShop\Order\Item\Iface $p ) : \Aimeos\MShop\Plugin\Provider\Iface
	{
		$plugin = $this->object();

		$p->attach( $plugin, 'addAddress.after' );
		$p->attach( $plugin, 'setAddresses.after' );
		$p->attach( $plugin, 'addProduct.after' );
		$p->attach( $plugin, 'setProducts.after' );
		$p->attach( $plugin, 'deleteService.after' );

		return $this;
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @return mixed Modified value parameter
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception if an error occurs
	 */
	public function update( \Aimeos\MShop\Order\Item\Iface $order, string $action, $value = null )
	{
		$context = $this->context();
		$services = $order->getServices();
		$addresses = $order->getAddresses();

		if( ( $userid = $context->user() ) !== null
			&& (bool) $this->getConfigValue( 'useorder', false ) === true
			&& ( $addresses->isEmpty() || $services->isEmpty() )
		) {
			$orderManager = \Aimeos\MShop::create( $context, 'order' );

			$search = $orderManager->filter()->add( [
				'order.customerid' => $userid,
				'order.languageid' => $order->locale()->getLanguageId(),
				'order.currencyid' => $order->locale()->getCurrencyId(),
			] )->order( '-order.id' )->slice( 0, 1 );

			if( ( $item = $orderManager->search( $search, ['order/address', 'order/service', 'service'] )->first() ) !== null )
			{
				$this->setAddresses( $order, $item );
				$this->setServices( $order, $item );
			}
		}

		$this->setAddressDefault( $order );
		$this->setServicesDefault( $order );

		return $value;
	}


	/**
	 * Returns the order service item for the given type and code if available.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Basket of the customer
	 * @param string $type Service type constant from \Aimeos\MShop\Order\Item\Service\Base
	 * @param string|null $code Service item code
	 * @return \Aimeos\MShop\Order\Item\Service\Iface|null Order service item if available or null otherwise
	 */
	protected function getServiceItem( \Aimeos\MShop\Order\Item\Iface $order, string $type,
		?string $code = null ) : ?\Aimeos\MShop\Order\Item\Service\Iface
	{
		$context = $this->context();
		$serviceManager = \Aimeos\MShop::create( $context, 'service' );

		$filter = $serviceManager->filter( true )->add( ['service.type' => $type] )->order( 'service.position' );

		if( $code !== null ) {
			$filter->add( 'service.code', '==', $code );
		}

		foreach( $serviceManager->search( $filter, ['media', 'price', 'text'] ) as $item )
		{
			$provider = $serviceManager->getProvider( $item, $item->getType() );

			if( $provider->isAvailable( $order ) === true )
			{
				return \Aimeos\MShop::create( $context, 'order/service' )->create()
					->copyFrom( $item )->setPrice( $provider->calcPrice( $order ) );
			}
		}

		return null;
	}


	/**
	 * Adds the addresses from the given order item to the basket.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Basket object
	 * @param \Aimeos\MShop\Order\Item\Iface $item Existing order to fetch the addresses from
	 * @return \Aimeos\MShop\Order\Item\Iface Updated basket object
	 */
	protected function setAddresses( \Aimeos\MShop\Order\Item\Iface $order,
		\Aimeos\MShop\Order\Item\Iface $item ) : \Aimeos\MShop\Order\Item\Iface
	{
		if( $order->getAddresses()->isEmpty() && (bool) $this->getConfigValue( 'orderaddress', true ) === true )
		{
			$map = $item->getAddresses();

			foreach( $map as $type => $list ) {
				map( $list )->setId( null );
			}

			$order->setAddresses( $map );
		}

		return $order;
	}


	/**
	 * Adds the services from the given order item to the basket.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Basket object
	 * @param \Aimeos\MShop\Order\Item\Iface $item Existing order to fetch the services from
	 * @return \Aimeos\MShop\Order\Item\Iface Updated basket object
	 */
	protected function setServices( \Aimeos\MShop\Order\Item\Iface $order,
		\Aimeos\MShop\Order\Item\Iface $item ) : \Aimeos\MShop\Order\Item\Iface
	{
		if( $order->getServices()->isEmpty() && $this->getConfigValue( 'orderservice', true ) == true )
		{
			$map = $item->getServices()->all();
			$serviceManager = \Aimeos\MShop::create( $this->context(), 'service' );

			foreach( $map as $type => $list )
			{
				foreach( $list as $key => $service )
				{
					if( $serviceItem = $service->getServiceItem() )
					{
						$provider = $serviceManager->getProvider( $serviceItem, $service->getType() );

						if( $provider->isAvailable( $order ) === true )
						{
							$attrItems = $service->getAttributeItems()->filter( function( $attr ) {
								return in_array( $attr->getType(), ['', 'hidden'] );
							} );

							$service->setId( null )->setAttributeItems( $attrItems->setId( null ) );
						}
						else
						{
							unset( $map[$type][$key] );
						}
					}
				}
			}

			$order->setServices( $map );
		}

		return $order;
	}


	/**
	 * Adds the default addresses to the basket if they are not available.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Basket object
	 * @return \Aimeos\MShop\Order\Item\Iface Updated basket object
	 */
	protected function setAddressDefault( \Aimeos\MShop\Order\Item\Iface $order ) : \Aimeos\MShop\Order\Item\Iface
	{
		$context = $this->context();
		$addresses = $order->getAddresses();
		$type = \Aimeos\MShop\Order\Item\Address\Base::TYPE_PAYMENT;

		if( $context->user() !== null && !isset( $addresses[$type] )
			&& (bool) $this->getConfigValue( 'address', false ) === true
		) {
			$address = \Aimeos\MShop::create( $context, 'customer' )
				->get( $context->user() )->getPaymentAddress();

			$addrItem = \Aimeos\MShop::create( $context, 'order/address' )
				->create()->copyFrom( $address );

			$order->addAddress( $addrItem, $type );
		}

		return $order;
	}


	/**
	 * Adds the default services to the basket if they are not available.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Basket object
	 * @return \Aimeos\MShop\Order\Item\Iface Updated basket object
	 */
	protected function setServicesDefault( \Aimeos\MShop\Order\Item\Iface $order ) : \Aimeos\MShop\Order\Item\Iface
	{
		$type = \Aimeos\MShop\Order\Item\Service\Base::TYPE_DELIVERY;

		foreach( $order->getService( $type ) as $pos => $service )
		{
			if( $this->getServiceItem( $order, $type, $service->getCode() ) === null ) {
				$order->deleteService( $type, $pos );
			}
		}

		if( $order->getService( $type ) === [] && (bool) $this->getConfigValue( 'delivery', false ) === true
			&& ( ( $item = $this->getServiceItem( $order, $type, $this->getConfigValue( 'deliverycode' ) ) ) !== null
			|| ( $item = $this->getServiceItem( $order, $type ) ) !== null )
		) {
			$order->addService( $item, $type );
		}


		$type = \Aimeos\MShop\Order\Item\Service\Base::TYPE_PAYMENT;

		foreach( $order->getService( $type ) as $pos => $service )
		{
			if( $this->getServiceItem( $order, $type, $service->getCode() ) === null ) {
				$order->deleteService( $type, $pos );
			}
		}

		if( $order->getService( $type ) === [] && (bool) $this->getConfigValue( 'payment', false ) === true
			&& ( ( $item = $this->getServiceItem( $order, $type, $this->getConfigValue( 'paymentcode' ) ) ) !== null
			|| ( $item = $this->getServiceItem( $order, $type ) ) !== null )
		) {
			$order->addService( $item, $type );
		}

		return $order;
	}
}
