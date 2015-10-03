<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Plugin
 */


/**
 * Checks the current availability of the products in a basket
 *
 * @package MShop
 * @subpackage Plugin
 */
class MShop_Plugin_Provider_Order_ProductGone
	extends MShop_Plugin_Provider_Factory_Base
	implements MShop_Plugin_Provider_Factory_Interface
{
	/**
	 * Subscribes itself to a publisher
	 *
	 * @param MW_Observer_Publisher_Interface $p Object implementing publisher interface
	 */
	public function register( MW_Observer_Publisher_Interface $p )
	{
		$p->addListener( $this, 'check.after' );
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
			throw new MShop_Plugin_Order_Exception( sprintf( 'Object is not of required type "%1$s"', $class ) );
		}

		if( !( $value & MShop_Order_Item_Base_Base::PARTS_PRODUCT ) ) {
			return true;
		}

		$productIds = array();
		foreach( $order->getProducts() as $pr ) {
			$productIds[] = $pr->getProductId();
		}

		$productManager = MShop_Factory::createManager( $this->getContext(), 'product' );

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
			throw new MShop_Plugin_Provider_Exception( sprintf( 'Products in basket not available' ), -1, null, $code );
		}

		return true;
	}
}