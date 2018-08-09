<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds subscription test data
 */
class SubscriptionAddTestData extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'OrderAddTestData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return [];
	}


	/**
	 * Adds subscription test data
	 */
	public function migrate()
	{
		\Aimeos\MW\Common\Base::checkClass( '\\Aimeos\\MShop\\Context\\Item\\Iface', $this->additional );

		$this->msg( 'Adding subscription test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'subscription.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for subscription domain', $path ) );
		}

		$this->addData( $testdata );

		$this->status( 'done' );
	}


	/**
	 * Adds the required subscription base data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	protected function addData( array $testdata )
	{
		$subscriptionManager = \Aimeos\MShop\Subscription\Manager\Factory::createManager( $this->additional, 'Standard' );

		$subscriptionManager->begin();

		foreach( $testdata['subscription'] as $key => $dataset )
		{
			$ordProdItem = $this->getOrderProductItem( $dataset['ordprodid'] ) ;

			$item = $subscriptionManager->createItem();
			$item->setOrderBaseId( $ordProdItem->getBaseId() );
			$item->setOrderProductId( $ordProdItem->getId() );
			$item->setDateNext( $dataset['datenext'] );
			$item->setDateEnd( $dataset['dateend'] );
			$item->setInterval( $dataset['interval'] );
			$item->setReason( $dataset['reason'] );
			$item->setStatus( $dataset['status'] );

			$subscriptionManager->saveItem( $item );
		}

		$subscriptionManager->commit();
	}


	/**
	 * Returns the order product ID for the given test data key
	 *
	 * @param string $key Test data key
	 * @return \MShop\Order\Item\Base\Product\Iface Order product item
	 */
	protected function getOrderProductItem( $key )
	{
		$manager = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->additional, 'Standard' )
			->getSubManager( 'base', 'Standard' )->getSubManager( 'product', 'Standard' );

		$parts = explode( '/', $key );

		if( count( $parts ) !== 2 ) {
			throw new \Exception( sprintf( 'Invalid order product key "%1$s"', $key ) );
		}

		$search = $manager->createSearch();
		$expr = [
			$search->compare( '==', 'order.base.product.prodcode', $parts[0] ),
			$search->compare( '==', 'order.base.product.price', $parts[1] ),
		];
		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) !== false ) {
			return $item;
		}

		throw new \Exception( sprintf( 'No order product item found for key "%1$s"', $key ) );
	}
}
