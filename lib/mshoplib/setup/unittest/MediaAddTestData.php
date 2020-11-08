<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds attribute test data and all items from other domains.
 */
class MediaAddTestData extends \Aimeos\MW\Setup\Task\BaseAddTestData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['MShopSetLocale'];
	}


	/**
	 * Adds attribute test data.
	 */
	public function migrate()
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->additional );

		$this->msg( 'Adding media test data', 0 );
		$this->additional->setEditor( 'core:lib/mshoplib' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'media.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for media domain', $path ) );
		}

		$this->storeTypes( $testdata, ['media/type', 'media/lists/type'] );
		$this->addMediaData( $testdata );

		$this->status( 'done' );
	}


	/**
	 * Returns the manager for the current setup task
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object
	 */
	protected function getManager( $domain )
	{
		if( $domain === 'media' ) {
			return \Aimeos\MShop\Media\Manager\Factory::create( $this->additional, 'Standard' );
		}

		return parent::getManager( $domain );
	}


	/**
	 * Adds the required media test data for attributes.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	private function addMediaData( array $testdata )
	{
		$mediaManager = \Aimeos\MShop\Media\Manager\Factory::create( $this->additional, 'Standard' );
		$mediaManager->begin();

		foreach( $testdata['media'] as $key => $dataset )
		{
			$media = $mediaManager->create();
			$media->setLanguageId( $dataset['langid'] );
			$media->setType( $dataset['type'] );
			$media->setDomain( $dataset['domain'] );
			$media->setLabel( $dataset['label'] );
			$media->setUrl( $dataset['link'] );
			$media->setStatus( $dataset['status'] );
			$media->setMimeType( $dataset['mimetype'] );

			if( isset( $dataset['preview'] ) ) {
				$media->setPreviews( (array) $dataset['preview'] );
			}

			$mediaManager->saveItem( $media, false );
		}

		$mediaManager->commit();
	}
}
