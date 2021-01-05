<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds stock test data.
 */
class StockAddTestData extends \Aimeos\MW\Setup\Task\BaseAddTestData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['MShopSetLocale', 'ProductAddTestData'];
	}


	/**
	 * Adds product stock test data.
	 */
	public function migrate()
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->additional );

		$this->msg( 'Adding stock test data', 0 );
		$this->additional->setEditor( 'core:lib/mshoplib' );

		$testdata = $this->getData();
		$this->addTypeItems( $testdata, ['stock/type'] );
		$this->createData( $testdata );

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
				$item = $manager->create()->fromArray( $entry );
				$manager->save( $item );
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
		$items = [];
		$manager = $this->getManager( 'stock' );
		$prodManager = $this->getManager( 'product' );
		$codes = map( $testdata['stock'] )->col( 'prodcode' );

		$filter = $prodManager->filter()->add( ['product.code' => $codes] );
		$map = $prodManager->search( $filter )->col( 'product.id', 'product.code' );

		foreach( $testdata['stock'] as $entry )
		{
			$prodid = $map->get( $entry['prodcode'] ?? null, new \Exception( 'No "prodcode" in ' . print_r( $entry, true ) ) );
			$items[] = $manager->create( $entry )->setProductId( $prodid );
		}

		$manager->begin();
		$manager->save( $items );
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
	 * Returns the manager for the current setup task
	 *
	 * @param string $domain Domain name of the manager
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object
	 */
	protected function getManager( $domain )
	{
		if( $domain === 'product' ) {
			return \Aimeos\MShop\Product\Manager\Factory::create( $this->additional, 'Standard' );
		}

		if( $domain === 'stock' ) {
			return \Aimeos\MShop\Stock\Manager\Factory::create( $this->additional, 'Standard' );
		}

		return parent::getManager( $domain );
	}
}
