<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	public function getPreDependencies() : array
	{
		return ['MShopSetLocale'];
	}


	/**
	 * Adds product test data.
	 */
	public function migrate()
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->additional );

		$this->msg( 'Adding tag test data', 0 );
		$this->additional->setEditor( 'core:lib/mshoplib' );

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
		$tagManager = \Aimeos\MShop\Tag\Manager\Factory::create( $this->additional, 'Standard' );
		$tagTypeManager = $tagManager->getSubManager( 'type', 'Standard' );

		$tagManager->begin();

		foreach( $testdata['tag/type'] as $dataset ) {
			$tagTypeManager->save( $tagTypeManager->create()->fromArray( $dataset ), false );
		}

		foreach( $testdata['tag'] as $dataset ) {
			$tagManager->save( $tagManager->create()->fromArray( $dataset ), false );
		}

		$tagManager->commit();
	}
}
