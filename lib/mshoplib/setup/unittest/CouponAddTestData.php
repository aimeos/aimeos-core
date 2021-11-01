<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds the coupon test data.
 */
class CouponAddTestData extends Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['Coupon', 'Media', 'MShopSetLocale', 'ProductAddTestData'];
	}


	/**
	 * Adds coupon test data.
	 */
	public function up()
	{
		$this->info( 'Adding coupon test data', 'v' );
		$this->context()->setEditor( 'core:lib/mshoplib' );

		$path = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'coupon.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No file "%1$s" found for coupon test data', $path ) );
		}

		$this->addCouponData( $testdata );
	}


	/**
	 * Adds the coupon test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 */
	private function addCouponData( array $testdata )
	{
		$manager = \Aimeos\MShop\Coupon\Manager\Factory::create( $this->context(), 'Standard' );
		$codeManager = $manager->getSubmanager( 'code' );

		foreach( $testdata['coupon'] ?? [] as $entry )
		{
			$id = $manager->save( $manager->create()->fromArray( $entry ) )->getId();

			foreach( $entry['codes'] ?? [] as $values ) {
				$codeManager->save( $codeManager->create()->fromArray( $values )->setParentId( $id ), false );
			}
		}
	}
}
