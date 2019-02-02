<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds product test data
 */
class ProductAddTestData extends \Aimeos\MW\Setup\Task\BaseAddTestData
{

	/**
	 * Returns the list of task names which this task depends on
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return ['MShopSetLocale', 'AttributeAddTestData', 'TagAddTestData'];
	}


	/**
	 * Returns the list of task names which depends on this task
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return ['CatalogRebuildTestIndex'];
	}


	/**
	 * Adds product test data
	 */
	public function migrate()
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->additional );

		$this->msg( 'Adding product test data', 0 );

		$this->additional->setEditor( 'core:unittest' );
		$this->process( __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'product.php' );

		$this->status( 'done' );
	}


	/**
	 * Returns the manager for the current setup task
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object
	 */
	protected function getManager()
	{
		return \Aimeos\MShop\Product\Manager\Factory::create( $this->additional, 'Standard' );
	}


	/**
	 * Adds the product data for the given file
	 *
	 * @param string $path Path to data file
	 */
	protected function process( $path )
	{
		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for product domain', $path ) );
		}

		$manager = $this->getManager();
		$listManager = $this->getManager()->getSubManager( 'lists' );
		$propManager = $this->getManager()->getSubManager( 'property' );

		$manager->begin();

		$this->storeTypes( $testdata, ['product/type', 'product/lists/type', 'product/property/type'] );

		foreach( $testdata['product'] as $entry )
		{
			$item = $manager->createItem()->fromArray( $entry );
			$item = $this->addListData( $listManager, $item, $entry );
			$item = $this->addPropertyData( $propManager, $item, $entry );

			$manager->saveItem( $item );
		}

		$manager->commit();
	}
}