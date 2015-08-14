<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds demo records to coupon tables.
 */
class MW_Setup_Task_DemoAddCouponData extends MW_Setup_Task_MShopAddDataAbstract
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
		return array();
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process();
	}


	/**
	 * Insert service data.
	 */
	protected function _process()
	{
		$this->_msg( 'Processing coupon demo data', 0 );

		$context = $this->_getContext();
		$manager = MShop_Factory::createManager( $context, 'coupon' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '=~', 'coupon.label', 'demo-' ) );
		$services = $manager->searchItems( $search );

		$manager->deleteItems( array_keys( $services ) );


		if( $context->getConfig()->get( 'setup/default/demo', false ) == true )
		{
			$ds = DIRECTORY_SEPARATOR;
			$path = __DIR__ . $ds . 'data' . $ds . 'demo-coupon.php';

			if( ( $data = include( $path ) ) == false ) {
				throw new MShop_Exception( sprintf( 'No file "%1$s" found for coupon domain', $path ) );
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

				$this->_addCodes( $item->getId(), $entry['codes'] );
			}

			$this->_status( 'added' );
		}
		else
		{
			$this->_status( 'removed' );
		}
	}


	/**
	 * Adds the coupon codes to the database.
	 *
	 * @param string $couponId
	 * @param array $data
	 */
	protected function _addCodes( $couponId, array $data )
	{
		$manager = MShop_Factory::createManager( $this->_getContext(), 'coupon/code' );

		foreach( $data as $entry )
		{
			$item = $manager->createItem();
			$item->setCouponId( $couponId );
			$item->setCode( $entry['code'] );
			$item->setCount( $entry['count'] );
			$item->setDateStart( $entry['datestart'] );
			$item->setDateEnd( $entry['dateend'] );

			$manager->saveItem( $item );
		}
	}
}