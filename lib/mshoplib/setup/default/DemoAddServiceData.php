<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds demo records to service tables.
 */
class MW_Setup_Task_DemoAddServiceData extends MW_Setup_Task_MShopAddDataAbstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopAddTypeDataDefault' );
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
		$this->_msg( 'Processing service demo data', 0 );

		$context = $this->_getContext();
		$value = $context->getConfig()->get( 'setup/default/demo', '' );

		if( $value === '' )
		{
			$this->_status( 'OK' );
			return;
		}


		$manager = MShop_Factory::createManager( $context, 'service' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '=~', 'service.code', 'demo-' ) );
		$services = $manager->searchItems( $search );

		foreach( $services as $item )
		{
			$this->_removeItems( $item->getId(), 'service/list', 'service', 'media' );
			$this->_removeItems( $item->getId(), 'service/list', 'service', 'price' );
			$this->_removeItems( $item->getId(), 'service/list', 'service', 'text' );
		}

		$manager->deleteItems( array_keys( $services ) );


		if( $value === '1' )
		{
			$ds = DIRECTORY_SEPARATOR;
			$path = __DIR__ . $ds . 'data' . $ds . 'demo-service.php';

			if( ( $data = include( $path ) ) == false ) {
				throw new MShop_Exception( sprintf( 'No file "%1$s" found for service domain', $path ) );
			}

			foreach( $data as $entry )
			{
				$item = $manager->createItem();
				$item->setTypeId( $this->_getTypeId( 'service/type', 'service', $entry['type'] ) );
				$item->setCode( $entry['code'] );
				$item->setLabel( $entry['label'] );
				$item->setProvider( $entry['provider'] );
				$item->setPosition( $entry['position'] );
				$item->setConfig( $entry['config'] );
				$item->setStatus( $entry['status'] );

				$manager->saveItem( $item );

				if( isset( $entry['media'] ) ) {
					$this->_addMedia( $item->getId(), $entry['media'], 'service' );
				}

				if( isset( $entry['price'] ) ) {
					$this->_addPrices( $item->getId(), $entry['price'], 'service' );
				}

				if( isset( $entry['text'] ) ) {
					$this->_addTexts( $item->getId(), $entry['text'], 'service' );
				}
			}

			$this->_status( 'added' );
		}
		else
		{
			$this->_status( 'removed' );
		}
	}
}