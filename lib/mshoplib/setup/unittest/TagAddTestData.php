<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds tag test data
 */
class TagAddTestData extends \Aimeos\MW\Setup\Task\Base
{

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'MediaListAddTestData', 'PriceListAddTestData', 'ProductAddTestData' );
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

		$this->msg( 'Adding tag test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'tag.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for tag domain', $path ) );
		}

		$this->addTagData( $testdata );

		$this->status( 'done' );
	}

	/**
	 * Adds the tag test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	private function addTagData( array $testdata )
	{
		$tagManager = \Aimeos\MShop\Tag\Manager\Factory::createManager( $this->additional, 'Standard' );
		$tagTypeManager = $tagManager->getSubManager( 'type', 'Standard' );

		$typeIds = [];
		$typeItem = $tagTypeManager->createItem();

		$this->conn->begin();

		foreach( $testdata['tag/type'] as $key => $dataset )
		{
			$typeItem->setId( null );
			$typeItem->setCode( $dataset['code'] );
			$typeItem->setDomain( $dataset['domain'] );
			$typeItem->setLabel( $dataset['label'] );
			$typeItem->setStatus( $dataset['status'] );

			$tagTypeManager->saveItem( $typeItem );
			$typeIds[$key] = $typeItem->getId();
		}

		$tagItem = $tagManager->createItem();
		foreach( $testdata['tag'] as $key => $dataset )
		{
			if( !isset( $typeIds[$dataset['typeid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No tag type ID found for "%1$s"', $dataset['typeid'] ) );
			}

			$tagItem->setId( null );
			$tagItem->setDomain( $dataset['domain'] );
			$tagItem->setLanguageId( $dataset['langid'] );
			$tagItem->setTypeId( $typeIds[$dataset['typeid']] );
			$tagItem->setLabel( $dataset['label'] );

			$tagManager->saveItem( $tagItem, false );
		}

		$this->conn->commit();
	}
}