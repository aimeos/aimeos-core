<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


/**
 * Checks the current availability of the products in a basket
 *
 * @package MShop
 * @subpackage Plugin
 */
class ProductGone
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
		$p->addListener( $this, 'check.after' );
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
		$class = '\\Aimeos\\MShop\\Order\\Item\\Base\\Iface';
		if( !( $order instanceof $class ) ) {
			throw new \Aimeos\MShop\Plugin\Order\Exception( sprintf( 'Object is not of required type "%1$s"', $class ) );
		}

		if( !( $value & \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT ) ) {
			return true;
		}

		$productIds = array();
		foreach( $order->getProducts() as $pr ) {
			$productIds[] = $pr->getProductId();
		}

		$productManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product' );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.id', $productIds ) );
		$checkItems = $productManager->searchItems( $search );

		$notAvailable = array();
		$now = date( 'Y-m-d H-i-s' );

		foreach( $order->getProducts() as $position => $orderProduct )
		{
			if( !array_key_exists( $orderProduct->getProductId(), $checkItems ) )
			{
				$notAvailable[$position] = 'gone.notexist';
				continue;
			}

			$product = $checkItems[$orderProduct->getProductId()];

			if( $product->getStatus() <= 0 )
			{
				$notAvailable[$position] = 'gone.status';
				continue;
			}

			$start = $product->getDateStart();
			$end = $product->getDateEnd();

			if( ( ( $start !== null ) && ( $start >= $now ) ) || ( ( $end !== null ) && ( $now > $end ) ) ) {
				$notAvailable[$position] = 'gone.timeframe';
			}
		}

		if( count( $notAvailable ) > 0 )
		{
			$code = array( 'product' => $notAvailable );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( sprintf( 'Products in basket not available' ), -1, null, $code );
		}

		return true;
	}
}