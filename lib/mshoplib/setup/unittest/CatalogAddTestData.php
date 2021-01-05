<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds catalog test data.
 */
class CatalogAddTestData extends \Aimeos\MW\Setup\Task\BaseAddTestData
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
	 * Adds catalog test data
	 */
	public function migrate()
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->additional );

		$this->msg( 'Adding catalog test data', 0 );

		$this->additional->setEditor( 'core:lib/mshoplib' );
		$this->process( $this->getData() );

		$this->status( 'done' );
	}


	/**
	 * Returns the test data array
	 *
	 * @return array Multi-dimensional array of test data
	 */
	protected function getData()
	{
		$path = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'catalog.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for catalog domain', $path ) );
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
		if( $domain === 'catalog' ) {
			return \Aimeos\MShop\Catalog\Manager\Factory::create( $this->additional, 'Standard' );
		}

		return parent::getManager( $domain );
	}


	/**
	 * Adds the catalog test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @param string|null $parentId ID of parent category or null for root category
	 * @throws \Aimeos\MW\Setup\Exception If a required ID is not available
	 */
	protected function process( array $testdata, $parentId = null )
	{
		$manager = $this->getManager( 'catalog' );
		$listManager = $manager->getSubManager( 'lists' );

		$manager->begin();
		$this->storeTypes( $testdata, ['catalog/type', 'catalog/lists/type'] );
		$manager->commit();

		foreach( $testdata['catalog'] as $entry )
		{
			$item = $manager->create()->fromArray( $entry );
			$item = $this->addListData( $listManager, $item, $entry );

			$id = $manager->insert( $item, $parentId )->getId();

			if( isset( $entry['catalog'] ) ) {
				$this->process( $entry, $id );
			}
		}
	}
}
