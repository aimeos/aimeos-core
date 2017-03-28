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
class TextAddTestData extends \Aimeos\MW\Setup\Task\Base
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
	 * Adds attribute test data.
	 */
	public function migrate()
	{
		$iface = '\\Aimeos\\MShop\\Context\\Item\\Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding text test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'text.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for text domain', $path ) );
		}

		$this->addTextData( $testdata );

		$this->status( 'done' );
	}


	/**
	 * Adds the required text test data for text.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	private function addTextData( array $testdata )
	{
		$textManager = \Aimeos\MShop\Text\Manager\Factory::createManager( $this->additional, 'Standard' );
		$textTypeManager = $textManager->getSubManager( 'type', 'Standard' );

		$ttypeIds = [];
		$ttype = $textTypeManager->createItem();

		$this->conn->begin();

		foreach( $testdata['text/type'] as $key => $dataset )
		{
			$ttype->setId( null );
			$ttype->setCode( $dataset['code'] );
			$ttype->setDomain( $dataset['domain'] );
			$ttype->setLabel( $dataset['label'] );
			$ttype->setStatus( $dataset['status'] );

			$textTypeManager->saveItem( $ttype );
			$ttypeIds[$key] = $ttype->getId();
		}

		$text = $textManager->createItem();
		foreach( $testdata['text'] as $key => $dataset )
		{
			if( !isset( $ttypeIds[$dataset['typeid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No text type ID found for "%1$s"', $dataset['typeid'] ) );
			}

			$text->setId( null );
			$text->setLanguageId( $dataset['langid'] );
			$text->setTypeId( $ttypeIds[$dataset['typeid']] );
			$text->setDomain( $dataset['domain'] );
			$text->setLabel( $dataset['label'] );
			$text->setContent( $dataset['content'] );
			$text->setStatus( $dataset['status'] );

			$textManager->saveItem( $text, false );
		}

		$this->conn->commit();
	}
}