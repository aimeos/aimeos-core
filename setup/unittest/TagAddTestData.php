<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds tag test data
 */
class TagAddTestData extends Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['Tag', 'MShopSetLocale'];
	}


	/**
	 * Adds product test data.
	 */
	public function up()
	{
		$this->info( 'Adding tag test data', 'vv' );
		$this->context()->setEditor( 'core' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'tag.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No file "%1$s" found for tag domain', $path ) );
		}

		$this->addTagData( $testdata );
	}

	/**
	 * Adds the tag test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 */
	private function addTagData( array $testdata )
	{
		$tagManager = \Aimeos\MShop::create( $this->context(), 'tag', 'Standard' );
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
