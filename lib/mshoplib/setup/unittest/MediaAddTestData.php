<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds attribute test data and all items from other domains.
 */
class MW_Setup_Task_MediaAddTestData extends MW_Setup_Task_Abstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'LocaleAddTestData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array();
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process();
	}


	/**
	 * Adds attribute test data.
	 */
	protected function _process()
	{
		$iface = 'MShop_Context_Item_Interface';
		if( !( $this->_additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->_msg( 'Adding media test data', 0 );
		$this->_additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'data' . $ds . 'media.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for media domain', $path ) );
		}

		$this->_addMediaData( $testdata );

		$this->_status( 'done' );
	}


	/**
	 * Adds the required media test data for attributes.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws MW_Setup_Exception If no type ID is found
	 */
	private function _addMediaData( array $testdata )
	{
		$mediaManager = MShop_Media_Manager_Factory::createManager( $this->_additional, 'Default' );
		$mediaTypeManager = $mediaManager->getSubManager( 'type', 'Default' );

		$mtypeIds = array();
		$mtype = $mediaTypeManager->createItem();

		$this->_conn->begin();

		foreach( $testdata['media/type'] as $key => $dataset )
		{
			$mtype->setId( null );
			$mtype->setCode( $dataset['code'] );
			$mtype->setDomain( $dataset['domain'] );
			$mtype->setLabel( $dataset['label'] );
			$mtype->setStatus( $dataset['status'] );

			$mediaTypeManager->saveItem( $mtype );
			$mtypeIds[ $key ] = $mtype->getId();
		}

		$media = $mediaManager->createItem();
		foreach( $testdata['media'] as $key => $dataset )
		{
			if( !isset( $mtypeIds[ $dataset['typeid'] ] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No media type ID found for "%1$s"', $dataset['typeid'] ) );
			}

			$media->setId( null );
			$media->setLanguageId( $dataset['langid'] );
			$media->setTypeId( $mtypeIds[ $dataset['typeid'] ] );
			$media->setDomain( $dataset['domain'] );
			$media->setLabel( $dataset['label'] );
			$media->setUrl( $dataset['link'] );
			$media->setStatus( $dataset['status'] );
			$media->setMimeType( $dataset['mimetype'] );

			$mediaManager->saveItem( $media, false );
		}

		$this->_conn->commit();
	}
}