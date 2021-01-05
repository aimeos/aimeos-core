<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	public function getPreDependencies() : array
	{
		return ['ProductAddTestData'];
	}


	/**
	 * Adds customer test data.
	 */
	public function migrate()
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->additional );

		$this->msg( 'Adding customer test data', 0 );

		$this->additional->setEditor( 'core:lib/mshoplib' );
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

		$manager = $this->getManager( 'customer' );
		$listManager = $manager->getSubManager( 'lists' );
		$groupManager = $manager->getSubManager( 'group' );
		$addrManager = $manager->getSubManager( 'address' );
		$propManager = $manager->getSubManager( 'property' );

		$manager->begin();

		$this->storeTypes( $testdata, ['customer/lists/type', 'customer/property/type'] );
		$this->addGroupItems( $groupManager, $testdata );

		$items = [];
		foreach( $testdata['customer'] as $entry )
		{
			$item = $manager->create()->fromArray( $entry, true );
			$item = $this->addGroupData( $groupManager, $item, $entry );
			$item = $this->addPropertyData( $propManager, $item, $entry );
			$item = $this->addAddressData( $addrManager, $item, $entry );
			$items[] = $this->addListData( $listManager, $item, $entry );
		}

		$manager->save( $items );
		$manager->commit();
	}


	/**
	 * Returns the manager for the current setup task
	 *
	 * @param string $domain Domain name of the manager
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object
	 */
	protected function getManager( $domain )
	{
		if( $domain === 'customer' ) {
			return \Aimeos\MShop\Customer\Manager\Factory::create( $this->additional, 'Standard' );
		}

		return \Aimeos\MShop::create( $this->additional, $domain );
	}


	/**
	 * Adds the group test data
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $groupManager Customer group manager
	 * @param \Aimeos\MShop\Customer\Item\Iface $item Item object
	 * @param array $data List of key/list pairs lists
	 * @return \Aimeos\MShop\Customer\Item\Iface Modified item object
	 */
	protected function addGroupData( \Aimeos\MShop\Common\Manager\Iface $groupManager, \Aimeos\MShop\Customer\Item\Iface $item, array $data )
	{
		if( isset( $data['group'] ) )
		{
			$grpIds = $list = [];
			$search = $groupManager->filter()->slice( 0, 10000 );

			foreach( $groupManager->search( $search ) as $id => $groupItem ) {
				$list[$groupItem->getCode()] = $id;
			}

			foreach( $data['group'] as $code )
			{
				if( isset( $list[$code] ) ) {
					$grpIds[] = $list[$code];
				}
			}

			$item->setGroups( $grpIds );
		}

		return $item;
	}


	/**
	 * Adds the customer group items
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $groupManager Customer group manager
	 * @param array $data Associative list of key/list pairs
	 * @throws \Aimeos\MW\Setup\Exception If a required ID is not available
	 */
	protected function addGroupItems( \Aimeos\MShop\Common\Manager\Iface $groupManager, array $data )
	{
		if( isset( $data['customer/group'] ) )
		{
			foreach( $data['customer/group'] as $entry )
			{
				try {
					$groupManager->save( $groupManager->create()->fromArray( $entry ), false );
				} catch( \Exception $e ) { echo $e->getMessage(); } // ignore duplicates
			}
		}
	}
}
