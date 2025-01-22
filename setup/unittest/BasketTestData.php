<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2025
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds basket test data.
 */
class BasketTestData extends Base
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
	 * Adds basket test data.
	 */
	public function up()
	{
		$this->info( 'Adding basket test data', 'vv' );
		$context = $this->context()->setEditor( 'core' );

		$path = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'basket.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No file "%1$s" found for baskets', $path ) );
		}

		$orderManager = \Aimeos\MShop::create( $context, 'order', 'Standard' );
		$manager = \Aimeos\MShop::create( $context, 'basket', 'Standard' );
		$manager->begin();

		foreach( $testdata as $entry ) {
			$manager->save( $manager->create()->fromArray( $entry, true )->setItem( $orderManager->create() ) );
		}

		$manager->commit();
	}


	protected function getCustomer() : \Aimeos\MShop\Customer\Item\Iface
	{
		$manager = \Aimeos\MShop::create( $this->context(), 'customer', 'Standard' );
		return $manager->find( 'test@example.com' );
	}
}
