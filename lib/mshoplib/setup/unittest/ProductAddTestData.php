<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds product test data.
 */
class ProductAddTestData extends \Aimeos\MW\Setup\Task\Base
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
		return array( 'CatalogRebuildTestIndex' );
	}


	/**
	 * Adds product test data.
	 */
	public function migrate()
	{
		$iface = '\\Aimeos\\MShop\\Context\\Item\\Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding product test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'product.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for product domain', $path ) );
		}

		$this->addProductData( $testdata );

		$this->status( 'done' );
	}



	/**
	 * Adds the product test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	private function addProductData( array $testdata )
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->additional, 'Standard' );
		$productTypeManager = $productManager->getSubManager( 'type', 'Standard' );

		$typeIds = [];
		$type = $productTypeManager->createItem();

		$this->conn->begin();

		foreach( $testdata['product/type'] as $key => $dataset )
		{
			$type->setId( null );
			$type->setCode( $dataset['code'] );
			$type->setDomain( $dataset['domain'] );
			$type->setLabel( $dataset['label'] );
			$type->setStatus( $dataset['status'] );

			$productTypeManager->saveItem( $type );
			$typeIds[$key] = $type->getId();
		}

		$product = $productManager->createItem();
		foreach( $testdata['product'] as $key => $dataset )
		{
			if( !isset( $typeIds[$dataset['typeid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No product type ID found for "%1$s"', $dataset['typeid'] ) );
			}

			$product->setId( null );
			$product->setTypeId( $typeIds[$dataset['typeid']] );
			$product->setCode( $dataset['code'] );
			$product->setLabel( $dataset['label'] );
			$product->setStatus( $dataset['status'] );

			if( isset( $dataset['config'] ) ) {
				$product->setConfig( $dataset['config'] );
			} else {
				$product->setConfig( [] );
			}

			$productManager->saveItem( $product, false );
		}

		$this->conn->commit();
	}
}