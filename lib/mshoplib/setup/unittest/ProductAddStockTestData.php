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
		\Aimeos\MW\Common\Base::checkClass( '\\Aimeos\\MShop\\Context\\Item\\Iface', $this->additional );

		$this->msg( 'Adding stock test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$config = $this->additional->getConfig();
		$name = $config->get( 'mshop/stock/manager/name' );

		\Aimeos\MShop\Factory::clear();
		$config->set( 'mshop/stock/manager/name', 'Standard' );

		$this->createData( $this->getData() );

		$config->set( 'mshop/stock/manager/name', $name );
		\Aimeos\MShop\Factory::clear();

		$this->status( 'done' );
	}


	/**
	 * Creates the test data
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	protected function createData( array $testdata )
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->additional, 'stock' );
		$typeIds = $this->getTypeIds( $testdata, ['stock/type'] );
		$items = [];

		foreach( $testdata['stock'] as $key => $entry )
		{
			if( !isset( $typeIds['stock/type'][$entry['stock.type']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No stock type ID found for "%1$s"', $entry['stock.type'] ) );
			}

			list( $domain, $code ) = explode( '/', $entry['stock.type'] );
			$items[] = $manager->createItem( $code, $domain, $entry )->setId( null );
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


	/**
	 * Creates the type test data and returns their IDs
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @param array $domains List of domain names
	 * @return array Associative list of type/key/ID triples
	 */
	protected function getTypeIds( array $testdata, array $domains )
	{
		$typeIds = [];

		foreach( $domains as $domain )
		{
			$manager = \Aimeos\MShop\Factory::createManager( $this->additional, $domain );

			foreach( $testdata[$domain] as $key => $entry )
			{
				$item = $manager->createItem();
				$item->fromArray( $entry );

				$typeIds[$domain][$key] = $manager->saveItem( $item )->getId();
			}
		}

		return $typeIds;
	}
}