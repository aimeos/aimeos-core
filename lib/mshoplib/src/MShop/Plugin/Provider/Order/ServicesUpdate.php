<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/license
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
	implements MShop_Plugin_Provider_Interface
{
	/**
	 * Subscribes itself to a publisher
	 *
	 * @param MW_Observer_Publisher_Interface $p Object implementing publisher interface
	 */
	public function register( MW_Observer_Publisher_Interface $p )
	{
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
		$ids = array();
		$services = $order->getServices();

		foreach( $order->getServices() as $type => $service ) {
			$ids[$type] = $service->getServiceId();
		}

		$context = $this->_getContext();
		$serviceManager = MShop_Factory::createManager( $context, 'service' );

		$search = $serviceManager->createSearch( true );
		$expr = array(
			$search->compare( '==', 'service.id', $ids ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		foreach( $serviceManager->searchItems( $search, array( 'price' ) ) as $item )
		{
			$provider = $serviceManager->getProvider( $item );
			$services[ $item->getType() ]->setPrice( $provider->calcPrice( $order ) );
			$order->setService( $services[ $item->getType() ], $type );
		}

		return true;
	}
}