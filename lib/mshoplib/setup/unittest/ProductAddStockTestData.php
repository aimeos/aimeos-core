<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds stock test data.
 */
class ProductAddStockTestData extends \Aimeos\MW\Setup\Task\Base
{

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return [];
	}


	/**
	 * Adds product stock test data.
	 */
	public function migrate()
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->additional );

		$this->msg( 'Adding stock test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$testdata = $this->getData();
		$config = $this->additional->getConfig();
		$name = $config->get( 'mshop/stock/manager/name' );

		\Aimeos\MShop::clear();
		$config->set( 'mshop/stock/manager/name', 'Standard' );

		$this->addTypeItems( $testdata, ['stock/type'] );
		$this->createData( $testdata );

		$config->set( 'mshop/stock/manager/name', $name );
		\Aimeos\MShop::clear();

		$this->status( 'done' );
	}


	/**
	 * Creates the type test data
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @param array $domains List of domain names
	 */
	protected function addTypeItems( array $testdata, array $domains )
	{
		foreach( $domains as $domain )
		{
			$manager = \Aimeos\MShop::create( $this->additional, $domain );

			foreach( $testdata[$domain] as $key => $entry )
			{
				$item = $manager->createItem()->fromArray( $entry );
				$manager->saveItem( $item );
			}
		}
	}


	/**
	 * Creates the test data
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	protected function createData( array $testdata )
	{
		$manager = \Aimeos\MShop::create( $this->additional, 'stock' );
		$items = [];

		foreach( $testdata['stock'] as $key => $entry ) {
			$items[] = $manager->createItem( $entry )->setId( null );
		}

		$manager->begin();
		$manager->saveItems( $items );
		$manager->commit();
	}


	/**
	 * Returns the test data
	 *
	 * @return array Multi-dimensional associative array
	 */
	protected function getData()
	{
		$path = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'stock.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for stock domain', $path ) );
		}

		return $testdata;
	}
}