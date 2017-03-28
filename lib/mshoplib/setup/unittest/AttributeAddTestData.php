<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds attribute test data and all items from other domains.
 */
class AttributeAddTestData extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'TextAddTestData', 'MediaAddTestData' );
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
	 * Adds attribute test data.
	 */
	public function migrate()
	{
		$iface = '\\Aimeos\\MShop\\Context\\Item\\Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding attribute test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'attribute.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for attribute domain', $path ) );
		}

		$this->addAttributeData( $testdata );

		$this->status( 'done' );
	}


	/**
	 * Adds the attribute test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws \Aimeos\MW\Setup\Exception If a required ID is not available
	 */
	private function addAttributeData( array $testdata )
	{
		$attributeManager = \Aimeos\MShop\Attribute\Manager\Factory::createManager( $this->additional, 'Standard' );
		$attributeTypeManager = $attributeManager->getSubManager( 'type', 'Standard' );

		$atypeIds = [];
		$atype = $attributeTypeManager->createItem();

		$this->conn->begin();

		foreach( $testdata['attribute/type'] as $key => $dataset )
		{
			$atype->setId( null );
			$atype->setCode( $dataset['code'] );
			$atype->setDomain( $dataset['domain'] );
			$atype->setLabel( $dataset['label'] );
			$atype->setStatus( $dataset['status'] );

			$attributeTypeManager->saveItem( $atype );
			$atypeIds[$key] = $atype->getId();
		}

		$attribute = $attributeManager->createItem();
		foreach( $testdata['attribute'] as $key => $dataset )
		{
			if( !isset( $atypeIds[$dataset['typeid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No attribute type ID found for "%1$s"', $dataset['typeid'] ) );
			}

			$attribute->setId( null );
			$attribute->setDomain( $dataset['domain'] );
			$attribute->setTypeId( $atypeIds[$dataset['typeid']] );
			$attribute->setCode( $dataset['code'] );
			$attribute->setLabel( $dataset['label'] );
			$attribute->setStatus( $dataset['status'] );
			$attribute->setPosition( $dataset['pos'] );

			$attributeManager->saveItem( $attribute, false );
		}

		$this->conn->commit();
	}
}