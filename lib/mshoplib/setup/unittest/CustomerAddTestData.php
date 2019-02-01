<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds customer test data.
 */
class CustomerAddTestData extends \Aimeos\MW\Setup\Task\BaseAddTestData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return ['MShopSetLocale', 'ProductAddTestData'];
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return ['CatalogRebuildTestIndex'];
	}


	/**
	 * Adds customer test data.
	 */
	public function migrate()
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->additional );

		$this->msg( 'Adding customer test data', 0 );

		$this->additional->setEditor( 'core:unittest' );
		$this->process( __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'customer.php' );

		$this->status( 'done' );

	}


	/**
	 * Adds the customer data
	 *
	 * @param string $path Path to data file
	 * @throws \Aimeos\MShop\Exception
	 */
	protected function process( $path )
	{
		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for customer domain', $path ) );
		}

		$manager = $this->getManager();
		$manager->begin();

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '=~', 'customer.code', 'UTC00' ) );
		$manager->deleteItems( array_keys( $manager->searchItems( $search ) ) );

		$this->addTypeItems( $testdata, ['customer/lists/type', 'customer/property/type'] );
		$this->addGroupItems( $testdata );

		$items = [];
		foreach( $testdata['customer'] as $entry )
		{
			$item = $manager->createItem()->fromArray( $entry );
			$item = $this->addAddressData( $item, $entry );
			$item = $this->addPropertyData( $item, $entry );
			$item = $this->addListData( $item, $entry );
			$items[] = $this->addGroupData( $item, $entry );
		}

		$manager->saveItems( $items );
		$manager->commit();
	}


	/**
	 * Adds the group test data
	 *
	 * @param \Aimeos\MShop\Common\Item\ListRef\Iface $item Item object
	 * @param array $data List of key/list pairs lists
	 * @return \Aimeos\MShop\Common\Item\ListRef\Iface Modified item object
	 */
	protected function addGroupData( \Aimeos\MShop\Common\Item\PropertyRef\Iface $item, array $data )
	{
		if( isset( $data['group'] ) )
		{
			$manager = $this->getManager()->getSubManager( 'group' );
			$grpItems = $this->getGroupItems();
			$grpIds = [];

			foreach( $data['group'] as $code )
			{
				if( isset( $grpItems[$code] ) ) {
					$grpIds[] = $grpItems[$code]->getId();
				}
			}

			$item->setGroups( $grpIds );
		}

		return $item;
	}


	/**
	 * Adds the customer group items
	 *
	 * @param array $data Associative list of key/list pairs
	 * @param \Aimeos\MShop\Common\Manager\Iface $customerGroupManager Customer group manager
	 * @throws \Aimeos\MW\Setup\Exception If a required ID is not available
	 */
	protected function addGroupItems( array $data )
	{
		if( isset( $data['customer/group'] ) )
		{
			$manager = $this->getManager()->getSubManager( 'group' );

			foreach( $data['customer/group'] as $entry )
			{
				try {
					$manager->saveItem( $manager->createItem()->fromArray( $entry ), false );
				} catch( \Exception $e ) {} // ignore duplicates
			}
		}
	}


	/**
	 * Returns the available group items
	 *
	 * @return array Associative list of code/item pairs
	 */
	protected function getGroupItems()
	{
		$list = [];

		$manager = $this->getManager()->getSubManager( 'group' );
		$search = $manager->createSearch()->setSlice( 0, 10000 );

		foreach( $manager->searchItems( $search ) as $item ) {
			$list[$item->getCode()] = $item;
		}

		return $list;
	}


	/**
	 * Returns the manager for the current setup task
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object
	 */
	protected function getManager()
	{
		return \Aimeos\MShop\Customer\Manager\Factory::create( $this->additional, 'Standard' );
	}
}