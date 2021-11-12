<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds subscription test data
 */
class SubscriptionAddTestData extends Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['Subscription', 'OrderAddTestData', 'SubscriptionMigratePeriod', 'SubscriptionMigrateProdcode'];
	}


	/**
	 * Adds subscription test data
	 */
	public function up()
	{
		$this->info( 'Adding subscription test data', 'v' );
		$this->context()->setEditor( 'core:lib/mshoplib' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'subscription.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No file "%1$s" found for subscription domain', $path ) );
		}

		$this->import( $testdata );
	}


	/**
	 * Adds the required subscription base data.
	 *
	 * @param array $data List of arrays
	 */
	protected function import( array $data )
	{
		$list = [];
		$manager = \Aimeos\MShop\Subscription\Manager\Factory::create( $this->context(), 'Standard' );

		foreach( $data as $entry )
		{
			$ordProdItem = $this->getOrderProductItem( $entry['ordprodid'] );

			$list[] = $manager->create()->fromArray( $entry, true )
				->setOrderBaseId( $ordProdItem->getBaseId() )
				->setOrderProductId( $ordProdItem->getId() )
				->setProductId( $ordProdItem->getProductId() );
		}

		$manager->begin();
		$manager->save( $list );
		$manager->commit();
	}


	/**
	 * Returns the order product ID for the given test data key
	 *
	 * @param string $key Test data key
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order product item
	 */
	protected function getOrderProductItem( $key )
	{
		$manager = \Aimeos\MShop\Order\Manager\Factory::create( $this->context(), 'Standard' )
			->getSubManager( 'base' )->getSubManager( 'product' );

		$parts = explode( '/', $key );

		if( count( $parts ) !== 2 ) {
			throw new \RuntimeException( sprintf( 'Invalid order product key "%1$s"', $key ) );
		}

		$search = $manager->filter()->add( [
			'order.base.product.prodcode' => $parts[0],
			'order.base.product.price' => $parts[1],
		] );

		return $manager->search( $search )->first( new \RuntimeException( 'No order product item found for ' . $key ) );
	}
}
