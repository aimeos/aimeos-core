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
	 * @param \Aimeos\MW\Observer\Publisher\Iface $p Object implementing publisher interface
	 * @return \Aimeos\MShop\Plugin\Provider\Iface Plugin object for method chaining
	 */
	public function register( \Aimeos\MW\Observer\Publisher\Iface $p )
	{
		$plugin = $this->getObject();

		$p->attach( $plugin, 'addAddress.after' );
		$p->attach( $plugin, 'deleteAddress.after' );
		$p->attach( $plugin, 'setAddresses.after' );
		$p->attach( $plugin, 'addCoupon.after' );
		$p->attach( $plugin, 'deleteCoupon.after' );
		$p->attach( $plugin, 'setCoupons.after' );
		$p->attach( $plugin, 'setCoupon.after' );
		$p->attach( $plugin, 'addProduct.after' );
		$p->attach( $plugin, 'deleteProduct.after' );
		$p->attach( $plugin, 'setProducts.after' );

		return $this;
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @return mixed Modified value parameter
	 */
	public function update( \Aimeos\MW\Observer\Publisher\Iface $order, $action, $value = null )
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Order\Item\Base\Iface::class, $order );

		$services = $order->getServices();

		if( count( $order->getProducts() ) === 0 )
		{
			$priceManager = \Aimeos\MShop::create( $this->getContext(), 'price' );

			foreach( $services as $type => $list )
			{
				foreach( $list as $key => $item ) {
					$services[$type][$key] = $item->setPrice( $priceManager->createItem() );
				}
			}

			$order->setServices( $services );
			return $value;
		}

		$serviceItems = $this->getServiceItems( $services );
		$serviceManager = \Aimeos\MShop::create( $this->getContext(), 'service' );

		foreach( $services as $type => $list )
		{
			foreach( $list as $key => $item )
			{
				if( isset( $serviceItems[$item->getServiceId()] ) )
				{
					$serviceItem = $serviceItems[$item->getServiceId()];
					$provider = $serviceManager->getProvider( $serviceItem, $serviceItem->getType() );

					if( $provider->isAvailable( $order ) ) {
						$services[$type][$key] = $item->setPrice( $provider->calcPrice( $order ) );
					} else {
						unset( $services[$type][$key] );
					}
				}
				else
				{
					unset( $services[$type][$key] );
				}
			}
		}

		$order->setServices( $services );
		return $value;
	}


	/**
	 * Returns the service items for the given order services
	 *
	 * @param array $services Associative list of service types as key and list
	 * 	of items implementing \Aimeos\MShop\Order\Item\Base\Service\Iface as values
	 * @return \Aimeos\MShop\Service\Item\Iface[] List of service items with IDs as keys and items as values
	 */
	protected function getServiceItems( array $services )
	{
		$list = [];

		foreach( $services as $type => $items )
		{
			foreach( $items as $service ) {
				$list[] = $service->getServiceId();
			}
		}

		if( $list !== [] )
		{
			$serviceManager = \Aimeos\MShop::create( $this->getContext(), 'service' );

			$search = $serviceManager->createSearch( true );
			$expr = [$search->compare( '==', 'service.id', $list ), $search->getConditions()];
			$search->setConditions( $search->combine( '&&', $expr ) );

			$list = $serviceManager->searchItems( $search, ['media', 'price', 'text'] );
		}

		return $list;
	}
}
