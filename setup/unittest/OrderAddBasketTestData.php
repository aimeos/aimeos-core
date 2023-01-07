<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds order basket test data.
 */
class OrderAddBasketTestData extends Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['Order'];
	}


	/**
	 * Adds order test data.
	 */
	public function up()
	{
		$this->info( 'Adding order basket test data', 'vv' );

		$context = $this->context()->setEditor( 'core' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'order-basket.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No file "%1$s" found for order baskets', $path ) );
		}

		$manager = \Aimeos\MShop::create( $context, 'order/basket', 'Standard' );
		$orderManager = \Aimeos\MShop::create( $context, 'order', 'Standard' );

		foreach( $testdata as $entry ) {
			$manager->save( $manager->create()->fromArray( $entry, true )->setItem( $orderManager->create() ) );
		}
	}


	protected function getCustomer() : \Aimeos\MShop\Customer\Item\Iface
	{
		$manager = \Aimeos\MShop::create( $this->context(), 'customer', 'Standard' );
		return $manager->find( 'test@example.com' );
	}
}
