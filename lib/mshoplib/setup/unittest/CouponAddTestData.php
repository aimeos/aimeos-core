<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds the coupon test data.
 */
class CouponAddTestData extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Adds coupon test data.
	 */
	public function migrate()
	{
		$this->msg( 'Adding coupon test data', 0 );
		$this->additional->setEditor( 'core:lib/mshoplib' );

		$path = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'coupon.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for coupon test data', $path ) );
		}

		$this->addCouponData( $testdata );

		$this->status( 'done' );
	}


	/**
	 * Adds the coupon test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws \Aimeos\MW\Setup\Exception If a required ID is not available
	 */
	private function addCouponData( array $testdata )
	{
		$manager = \Aimeos\MShop\Coupon\Manager\Factory::create( $this->additional, 'Standard' );
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
