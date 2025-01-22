<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2025
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
		return ['Customer', 'Group', 'Text', 'GroupAddTestData', 'ProductAddTestData'];
	}


	/**
	 * Adds customer test data.
	 */
	public function up()
	{
		$this->info( 'Adding customer test data', 'vv' );
		$this->context()->setEditor( 'core' );

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

		$items = [];
		$manager = $this->getManager( 'customer' );

		foreach( $testdata['customer'] as $entry )
		{
			$item = $manager->create()->fromArray( $entry, true );
			$item = $this->addGroupData( $item, $entry );
			$item = $this->addPropertyData( $manager, $item, $entry );
			$item = $this->addAddressData( $manager, $item, $entry );
			$items[] = $this->addListData( $manager, $item, $entry );
		}

		$manager->begin();
		$manager->save( $items );
		$manager->commit();
	}


	/**
	 * Adds the group test data
	 *
	 * @param \Aimeos\MShop\Customer\Item\Iface $item Item object
	 * @param array $data List of key/list pairs lists
	 * @return \Aimeos\MShop\Customer\Item\Iface Modified item object
	 */
	protected function addGroupData( \Aimeos\MShop\Customer\Item\Iface $item, array $data )
	{
		if( isset( $data['group'] ) )
		{
			$manager = $this->getManager( 'group' );
			$list = $manager->search( $manager->filter()->slice( 0, 10000 ) )->getCode();

			$item->setGroups( $list->intersect( $data['group'] )->all() );
		}

		return $item;
	}
}
