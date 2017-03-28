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
class MediaAddTestData extends \Aimeos\MW\Setup\Task\Base
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
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return [];
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

		$this->msg( 'Adding media test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'media.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for media domain', $path ) );
		}

		$this->addMediaData( $testdata );

		$this->status( 'done' );
	}


	/**
	 * Adds the required media test data for attributes.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	private function addMediaData( array $testdata )
	{
		$mediaManager = \Aimeos\MShop\Media\Manager\Factory::createManager( $this->additional, 'Standard' );
		$mediaTypeManager = $mediaManager->getSubManager( 'type', 'Standard' );

		$mtypeIds = [];
		$mtype = $mediaTypeManager->createItem();

		$this->conn->begin();

		foreach( $testdata['media/type'] as $key => $dataset )
		{
			$mtype->setId( null );
			$mtype->setCode( $dataset['code'] );
			$mtype->setDomain( $dataset['domain'] );
			$mtype->setLabel( $dataset['label'] );
			$mtype->setStatus( $dataset['status'] );

			$mediaTypeManager->saveItem( $mtype );
			$mtypeIds[$key] = $mtype->getId();
		}

		$media = $mediaManager->createItem();
		foreach( $testdata['media'] as $key => $dataset )
		{
			if( !isset( $mtypeIds[$dataset['typeid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No media type ID found for "%1$s"', $dataset['typeid'] ) );
			}

			$media->setId( null );
			$media->setLanguageId( $dataset['langid'] );
			$media->setTypeId( $mtypeIds[$dataset['typeid']] );
			$media->setDomain( $dataset['domain'] );
			$media->setLabel( $dataset['label'] );
			$media->setUrl( $dataset['link'] );
			$media->setStatus( $dataset['status'] );
			$media->setMimeType( $dataset['mimetype'] );

			if( isset( $dataset['preview'] ) ) {
				$media->setPreview( $dataset['preview'] );
			}

			$mediaManager->saveItem( $media, false );
		}

		$this->conn->commit();
	}
}