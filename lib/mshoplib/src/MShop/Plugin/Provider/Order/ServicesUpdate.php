<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Plugin
 */


/**
 * Updates service items on basket change.
 *
 * @package MShop
 * @subpackage Plugin
 */
class MShop_Plugin_Provider_Order_ServicesUpdate
	extends MShop_Plugin_Provider_Order_Abstract
	implements MShop_Plugin_Provider_Factory_Interface
{
	/**
	 * Subscribes itself to a publisher
	 *
	 * @param MW_Observer_Publisher_Interface $p Object implementing publisher interface
	 */
	public function register( MW_Observer_Publisher_Interface $p )
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
	 * @param MW_Observer_Publisher_Interface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @throws MShop_Plugin_Provider_Exception if checks fail
	 * @return bool true if checks succeed
	 */
	public function update( MW_Observer_Publisher_Interface $order, $action, $value = null )
	{
		$class = 'MShop_Order_Item_Base_Interface';
		if( !( $order instanceof $class ) ) {
			throw new MShop_Plugin_Exception( sprintf( 'Object is not of required type "%1$s"', $class ) );
		}

		$ids = array();
		$context = $this->_getContext();
		$services = $order->getServices();


		if( count( $order->getProducts() ) === 0 )
		{
			$priceManager = MShop_Factory::createManager( $context, 'price' );

			foreach( $services as $type => $service ) {
				$service->setPrice( $priceManager->createItem() );
			}

			return true;
		}


		foreach( $services as $type => $service ) {
			$ids[$type] = $service->getServiceId();
		}

		$serviceManager = MShop_Factory::createManager( $context, 'service' );

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