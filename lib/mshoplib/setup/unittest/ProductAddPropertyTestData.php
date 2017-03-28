<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds product property test data.
 */
class ProductAddPropertyTestData extends \Aimeos\MW\Setup\Task\Base
{

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'ProductAddTestData' );
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

		$this->msg( 'Adding product property test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'productproperty.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for product domain', $path ) );
		}

		$this->addProductPropertyData( $testdata );

		$this->status( 'done' );
	}

	/**
	 * Adds the product property test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	private function addProductPropertyData( array $testdata )
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->additional, 'Standard' );
		$productPropertyManager = $productManager->getSubManager( 'property', 'Standard' );
		$productPropertyTypeManager = $productPropertyManager->getSubManager( 'type', 'Standard' );

		$typeIds = [];
		$type = $productPropertyTypeManager->createItem();
		$prodIds = $this->getProductIds( $productManager );

		$this->conn->begin();

		foreach( $testdata['product/property/type'] as $key => $dataset )
		{
			$type->setId( null );
			$type->setCode( $dataset['code'] );
			$type->setDomain( $dataset['domain'] );
			$type->setLabel( $dataset['label'] );
			$type->setStatus( $dataset['status'] );

			$productPropertyTypeManager->saveItem( $type );
			$typeIds[ $key ] = $type->getId();
		}

		$prodProperty = $productPropertyManager->createItem();
		foreach( $testdata['product/property'] as $key => $dataset )
		{
			if( !isset( $typeIds[ $dataset['typeid'] ] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No product property type ID found for "%1$s"', $dataset['typeid'] ) );
			}

			$prodProperty->setId( null );
			$prodProperty->setParentId( $prodIds[ $dataset['parentid'] ] );
			$prodProperty->setTypeId( $typeIds[ $dataset['typeid'] ] );
			$prodProperty->setLanguageId( $dataset['langid'] );
			$prodProperty->setValue( $dataset['value'] );

			$productPropertyManager->saveItem( $prodProperty, false );
		}

		$this->conn->commit();
	}


	/**
	 * Retrieves the product IDs for the used codes
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $productManager Product manager object
	 * @return array Associative list of product codes as key (e.g. product/CNC) and IDs as value
	 */
	protected function getProductIds( \Aimeos\MShop\Common\Manager\Iface $productManager )
	{
		$entry = [];
		$search = $productManager->createSearch();

		foreach( $productManager->searchItems( $search ) as $id => $item ) {
			$entry[ 'product/' . $item->getCode() ] = $id;
		}

		return $entry;

	}
}