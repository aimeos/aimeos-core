<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2018
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds media property test data.
 */
class MediaAddPropertyTestData extends \Aimeos\MW\Setup\Task\Base
{

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return ['MediaAddTestData'];
	}


	/**
	 * Adds media test data.
	 */
	public function migrate()
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->additional );

		$this->msg( 'Adding media property test data', 0 );
		$this->additional->setEditor( 'core:lib/mshoplib' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'media-property.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for media domain', $path ) );
		}

		$this->addMediaPropertyData( $testdata );

		$this->status( 'done' );
	}

	/**
	 * Adds the media property test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	private function addMediaPropertyData( array $testdata )
	{
		$mediaManager = \Aimeos\MShop\Media\Manager\Factory::create( $this->additional, 'Standard' );
		$mediaPropertyManager = $mediaManager->getSubManager( 'property', 'Standard' );
		$mediaPropertyTypeManager = $mediaPropertyManager->getSubManager( 'type', 'Standard' );

		$type = $mediaPropertyTypeManager->createItem();
		$prodIds = $this->getMediaIds( $mediaManager );

		$mediaManager->begin();

		foreach( $testdata['media/property/type'] as $key => $dataset )
		{
			$type->setId( null );
			$type->setCode( $dataset['code'] );
			$type->setDomain( $dataset['domain'] );
			$type->setLabel( $dataset['label'] );
			$type->setStatus( $dataset['status'] );

			$mediaPropertyTypeManager->saveItem( $type );
		}

		$prodProperty = $mediaPropertyManager->createItem();
		foreach( $testdata['media/property'] as $key => $dataset )
		{
			$prodProperty->setId( null );
			$prodProperty->setParentId( $prodIds[$dataset['parentid']] );
			$prodProperty->setLanguageId( $dataset['langid'] );
			$prodProperty->setValue( $dataset['value'] );
			$prodProperty->setType( $dataset['type'] );

			$mediaPropertyManager->saveItem( $prodProperty, false );
		}

		$mediaManager->commit();
	}


	/**
	 * Retrieves the media IDs for the used codes
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $mediaManager Media manager object
	 * @return array Associative list of media codes as key (e.g. media/CNC) and IDs as value
	 */
	protected function getMediaIds( \Aimeos\MShop\Common\Manager\Iface $mediaManager )
	{
		$entry = [];
		$search = $mediaManager->createSearch();

		foreach( $mediaManager->searchItems( $search ) as $id => $item ) {
			$entry['media/' . $item->getUrl()] = $id;
		}

		return $entry;
	}
}
