<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


/**
 * Updates service items on basket change.
 *
 * @package MShop
 * @subpackage Plugin
 */
class ServicesUpdate
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
		$p->addListener( $this, 'setAddress.after' );
		$p->addListener( $this, 'deleteAddress.after' );
		$p->addListener( $this, 'addProduct.after' );
		$p->addListener( $this, 'deleteProduct.after' );
		$p->addListener( $this, 'addCoupon.after' );
		$p->addListener( $this, 'deleteCoupon.after' );
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
		if( !( $order instanceof \Aimeos\MShop\Order\Item\Base\Iface ) )
		{
			$msg = $this->getContext()->getI18n()->dt( 'mshop', 'Object is not of required type "%1$s"' );
			throw new \Aimeos\MShop\Plugin\Exception( sprintf( $msg, '\Aimeos\MShop\Order\Item\Base\Iface' ) );
		}

		$ids = [];
		$context = $this->getContext();
		$services = $order->getServices();


		if( count( $order->getProducts() ) === 0 )
		{
			$priceManager = \Aimeos\MShop\Factory::createManager( $context, 'price' );

			foreach( $services as $type => $service ) {
				$service->setPrice( $priceManager->createItem() );
			}

			return true;
		}


		foreach( $services as $type => $service ) {
			$ids[$type] = $service->getServiceId();
		}

		$serviceManager = \Aimeos\MShop\Factory::createManager( $context, 'service' );

		$search = $serviceManager->createSearch( true );
		$expr = array(
			$search->compare( '==', 'service.id', $ids ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $serviceManager->searchItems( $search, array( 'price' ) );


		foreach( $services as $type => $service )
		{
			if( isset( $result[$service->getServiceId()] ) )
			{
				$provider = $serviceManager->getProvider( $result[$service->getServiceId()] );

				if( $provider->isAvailable( $order ) )
				{
					$service->setPrice( $provider->calcPrice( $order ) );
					$order->setService( $service, $type );
					continue;
				}
			}

			$order->deleteService( $type );
		}

		return true;
	}
}
