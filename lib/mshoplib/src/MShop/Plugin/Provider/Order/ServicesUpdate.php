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
	 */
	public function register( \Aimeos\MW\Observer\Publisher\Iface $p )
	{
		$p->addListener( $this->getObject(), 'deleteAddress.after' );
		$p->addListener( $this->getObject(), 'setAddress.after' );
		$p->addListener( $this->getObject(), 'setAddresses.after' );
		$p->addListener( $this->getObject(), 'addProduct.after' );
		$p->addListener( $this->getObject(), 'deleteProduct.after' );
		$p->addListener( $this->getObject(), 'setProducts.after' );
		$p->addListener( $this->getObject(), 'addCoupon.after' );
		$p->addListener( $this->getObject(), 'deleteCoupon.after' );
		$p->addListener( $this->getObject(), 'setCoupons.after' );
		$p->addListener( $this->getObject(), 'setCoupon.after' );
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception if checks fail
	 * @return bool true if checks succeed
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
			return true;
		}


		$serviceManager = \Aimeos\MShop::create( $this->getContext(), 'service' );
		$serviceItems = $this->getServiceItems( $services );

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

		return true;
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
