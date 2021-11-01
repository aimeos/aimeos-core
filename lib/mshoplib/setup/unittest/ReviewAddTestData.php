<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds review test data
 */
class ReviewAddTestData extends Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['Review', 'CustomerAddTestData', 'ProductAddTestData', 'OrderAddTestData'];
	}


	/**
	 * Adds review test data
	 */
	public function up()
	{
		$this->info( 'Adding review test data', 'v' );
		$this->context()->setEditor( 'core:lib/mshoplib' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'review.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No file "%1$s" found for review domain', $path ) );
		}

		$this->addData( $testdata );
	}


	/**
	 * Adds the required review base data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 */
	protected function addData( array $testdata )
	{
		$manager = \Aimeos\MShop\Review\Manager\Factory::create( $this->context(), 'Standard' );
		$custManager = \Aimeos\MShop\Customer\Manager\Factory::create( $this->context(), 'Standard' );

		$manager->begin();

		foreach( $testdata['review'] as $domain => $list )
		{
			$domainManager = \Aimeos\MShop::create( $this->context(), $domain );

			foreach( $list as $dataset )
			{
				$refId = $domainManager->find( $dataset['refid'] )->getId();
				$custId = $custManager->find( $dataset['customerid'] )->getId();

				$ordProdItem = $this->getOrderProductItem( $dataset['ordprodid'] );

				$item = $manager->create()->fromArray( $dataset )
					->setDomain( $domain )->setOrderProductId( $ordProdItem->getId() )
					->setCustomerId( $custId )->setRefId( $refId );

				$manager->save( $item, false );
			}
		}

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
			->getSubManager( 'base', 'Standard' )->getSubManager( 'product', 'Standard' );

		$parts = explode( '/', $key );

		if( count( $parts ) !== 2 ) {
			throw new \RuntimeException( sprintf( 'Invalid order product key "%1$s"', $key ) );
		}

		$search = $manager->filter();
		$expr = [
			$search->compare( '==', 'order.base.product.prodcode', $parts[0] ),
			$search->compare( '==', 'order.base.product.price', $parts[1] ),
		];
		$search->setConditions( $search->and( $expr ) );
		$result = $manager->search( $search );

		if( ( $item = $result->first() ) !== null ) {
			return $item;
		}

		throw new \RuntimeException( sprintf( 'No order product item found for key "%1$s"', $key ) );
	}
}
