<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2025
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


/**
 * Updates service items on basket change
 *
 * Delivery or payment service options can be restricted to certain requirements
 * like the basket value, the delivery address or if virtual (download) products
 * are in the basket. If the service option is not available any more due to one
 * of these restrictions, it will be removed from the basket. Otherwise, the
 * price of the service option is recalculated.
 *
 * This plugin interacts with the "Autofill" plugin, which may re-add one of the
 * other delivery/payment options automatically, that are still available!
 *
 * @package MShop
 * @subpackage Plugin
 */
class ServicesUpdate
	extends \Aimeos\MShop\Plugin\Provider\Factory\Base
	implements \Aimeos\MShop\Plugin\Provider\Iface, \Aimeos\MShop\Plugin\Provider\Factory\Iface
{
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
		$p->attach( $plugin, 'deleteAddress.after' );
		$p->attach( $plugin, 'setAddresses.after' );
		$p->attach( $plugin, 'addCoupon.after' );
		$p->attach( $plugin, 'deleteCoupon.after' );
		$p->attach( $plugin, 'addProduct.after' );
		$p->attach( $plugin, 'deleteProduct.after' );
		$p->attach( $plugin, 'setProducts.after' );

		return $this;
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @return mixed Modified value parameter
	 */
	public function update( \Aimeos\MShop\Order\Item\Iface $order, string $action, $value = null )
	{
		$services = $order->getServices();

		if( $order->getProducts()->isEmpty() )
		{
			$priceManager = \Aimeos\MShop::create( $this->context(), 'price' );

			foreach( $services as $type => $list )
			{
				$serviceItems = $list;

				foreach( $list as $key => $item ) {
					$serviceItems[$key] = $item->setPrice( $priceManager->create() );
				}

				$services[$type] = $serviceItems;
			}

			$order->setServices( $services->toArray() );
			return $value;
		}

		$serviceItems = $this->getServiceItems( $services );
		$serviceManager = \Aimeos\MShop::create( $this->context(), 'service' );

		foreach( $services as $type => $list )
		{
			$orderServices = $list;

			foreach( $list as $key => $item )
			{
				if( ( $serviceItem = $serviceItems->get( $item->getServiceId() ) ) !== null )
				{
					$provider = $serviceManager->getProvider( $serviceItem, $serviceItem->getType() );

					if( $provider->isAvailable( $order ) )
					{
						$orderServices[$key] = $item->setPrice( $provider->calcPrice( $order ) );
						continue;
					}
				}

				unset( $orderServices[$key] );
			}

			$services[$type] = $orderServices;
		}

		$order->setServices( $services->toArray() );
		return $value;
	}


	/**
	 * Returns the service items for the given order services
	 *
	 * @param \Aimeos\Map $services List of items implementing \Aimeos\MShop\Order\Item\Service\Iface with IDs as keys
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Service\Item\Iface with IDs as keys
	 */
	protected function getServiceItems( \Aimeos\Map $services ) : \Aimeos\Map
	{
		$list = map();

		foreach( $services as $type => $items ) {
			$list->concat( map( $items )->getServiceId() );
		}

		if( $list->isEmpty() ) {
			return $list;
		}

		$serviceManager = \Aimeos\MShop::create( $this->context(), 'service' );
		$search = $serviceManager->filter( true )->add( ['service.id' => $list] );

		return $serviceManager->search( $search, ['media', 'price', 'text'] );
	}
}
