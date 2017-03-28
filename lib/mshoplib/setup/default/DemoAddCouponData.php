<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds demo records to coupon tables.
 */
class DemoAddCouponData extends \Aimeos\MW\Setup\Task\MShopAddDataAbstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopAddLocaleDataDefault' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return [];
	}


	/**
	 * Insert service data.
	 */
	public function migrate()
	{
		$this->msg( 'Processing coupon demo data', 0 );

		$context = $this->getContext();
		$value = $context->getConfig()->get( 'setup/default/demo', '' );

		if( $value === '' )
		{
			$this->status( 'OK' );
			return;
		}


		$manager = \Aimeos\MShop\Factory::createManager( $context, 'coupon' );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '=~', 'coupon.label', 'demo-' ) );
		$services = $manager->searchItems( $search );

		$manager->deleteItems( array_keys( $services ) );


		if( $value === '1' )
		{
			$ds = DIRECTORY_SEPARATOR;
			$path = __DIR__ . $ds . 'data' . $ds . 'demo-coupon.php';

			if( ( $data = include( $path ) ) == false ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for coupon domain', $path ) );
			}

			foreach( $data as $entry )
			{
				$item = $manager->createItem();
				$item->setLabel( $entry['label'] );
				$item->setProvider( $entry['provider'] );
				$item->setDateStart( $entry['datestart'] );
				$item->setDateEnd( $entry['dateend'] );
				$item->setConfig( $entry['config'] );
				$item->setStatus( $entry['status'] );

				$manager->saveItem( $item );

				$this->addCodes( $item->getId(), $entry['codes'] );
			}

			$this->status( 'added' );
		}
		else
		{
			$this->status( 'removed' );
		}
	}


	/**
	 * Adds the coupon codes to the database.
	 *
	 * @param string $couponId
	 * @param array $data
	 */
	protected function addCodes( $couponId, array $data )
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'coupon/code' );

		foreach( $data as $entry )
		{
			$item = $manager->createItem();
			$item->setParentId( $couponId );
			$item->setCode( $entry['code'] );
			$item->setCount( $entry['count'] );
			$item->setDateStart( $entry['datestart'] );
			$item->setDateEnd( $entry['dateend'] );

			$manager->saveItem( $item );
		}
	}
}