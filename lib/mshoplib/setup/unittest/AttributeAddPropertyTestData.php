<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2018
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds attribute property test data.
 */
class AttributeAddPropertyTestData extends \Aimeos\MW\Setup\Task\Base
{

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'AttributeAddTestData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return ['CatalogRebuildTestIndex'];
	}


	/**
	 * Adds attribute test data.
	 */
	public function migrate()
	{
		\Aimeos\MW\Common\Base::checkClass( '\\Aimeos\\MShop\\Context\\Item\\Iface', $this->additional );

		$this->msg( 'Adding attribute property test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'attribute-property.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for attribute domain', $path ) );
		}

		$this->addAttributePropertyData( $testdata );

		$this->status( 'done' );
	}

	/**
	 * Adds the attribute property test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	private function addAttributePropertyData( array $testdata )
	{
		$attributeManager = \Aimeos\MShop\Attribute\Manager\Factory::createManager( $this->additional, 'Standard' );
		$attributePropertyManager = $attributeManager->getSubManager( 'property', 'Standard' );
		$attributePropertyTypeManager = $attributePropertyManager->getSubManager( 'type', 'Standard' );

		$typeIds = [];
		$type = $attributePropertyTypeManager->createItem();
		$prodIds = $this->getAttributeIds( $attributeManager );

		$attributeManager->begin();

		foreach( $testdata['attribute/property/type'] as $key => $dataset )
		{
			$type->setId( null );
			$type->setCode( $dataset['code'] );
			$type->setDomain( $dataset['domain'] );
			$type->setLabel( $dataset['label'] );
			$type->setPosition( $dataset['position'] );
			$type->setStatus( $dataset['status'] );

			$attributePropertyTypeManager->saveItem( $type );
			$typeIds[ $key ] = $type->getId();
		}

		$prodProperty = $attributePropertyManager->createItem();
		foreach( $testdata['attribute/property'] as $key => $dataset )
		{
			if( !isset( $typeIds[ $dataset['typeid'] ] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No attribute property type ID found for "%1$s"', $dataset['typeid'] ) );
			}

			$prodProperty->setId( null );
			$prodProperty->setParentId( $prodIds[ $dataset['parentid'] ] );
			$prodProperty->setTypeId( $typeIds[ $dataset['typeid'] ] );
			$prodProperty->setLanguageId( $dataset['langid'] );
			$prodProperty->setValue( $dataset['value'] );

			$attributePropertyManager->saveItem( $prodProperty, false );
		}

		$attributeManager->commit();
	}


	/**
	 * Retrieves the attribute IDs for the used codes
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $attributeManager Attribute manager object
	 * @return array Associative list of attribute codes as key (e.g. attribute/CNC) and IDs as value
	 */
	protected function getAttributeIds( \Aimeos\MShop\Common\Manager\Iface $attributeManager )
	{
		$entry = [];
		$search = $attributeManager->createSearch();

		foreach( $attributeManager->searchItems( $search ) as $id => $item ) {
			$entry[ 'attribute/' . $item->getDomain() . '/' . $item->getType() . '/' . $item->getCode() ] = $id;
		}

		return $entry;

	}
}