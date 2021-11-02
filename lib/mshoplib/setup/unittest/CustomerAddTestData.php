<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds customer test data.
 */
class CustomerAddTestData extends BaseAddTestData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['Customer', 'Text', 'ProductAddTestData'];
	}


	/**
	 * Adds customer test data.
	 */
	public function up()
	{
		$this->info( 'Adding customer test data', 'v' );

		$this->context()->setEditor( 'core:lib/mshoplib' );
		$this->process();
	}


	/**
	 * Adds the customer data
	 *
	 * @throws \RuntimeException
	 */
	protected function process()
	{
		$path = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'customer.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No file "%1$s" found for customer domain', $path ) );
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
	protected function getManager( string $domain ) : \Aimeos\MShop\Common\Manager\Iface
	{
		if( $domain === 'customer' ) {
			return \Aimeos\MShop\Customer\Manager\Factory::create( $this->context(), 'Standard' );
		}

		return \Aimeos\MShop::create( $this->context(), $domain );
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
	 */
	protected function addGroupItems( \Aimeos\MShop\Common\Manager\Iface $groupManager, array $data )
	{
		if( isset( $data['customer/group'] ) )
		{
			foreach( $data['customer/group'] as $entry )
			{
				try {
					$groupManager->save( $groupManager->find( $entry['customer.group.code'] )->fromArray( $entry ) );
				} catch( \Exception $e ) {
					$groupManager->save( $groupManager->create()->fromArray( $entry ), false );
				}
			}
		}
	}
}
