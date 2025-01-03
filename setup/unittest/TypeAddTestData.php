<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2025
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds type test data
 */
class TypeAddTestData extends Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['MShopSetLocale'];
	}


	/**
	 * Adds type test data.
	 */
	public function up()
	{
		$this->info( 'Adding type test data', 'vv' );
		$this->context()->setEditor( 'core' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'type.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No file "%1$s" found for type domain', $path ) );
		}

		$manager = \Aimeos\MShop::create( $this->context(), 'type', 'Standard' );
		$manager->begin();

		foreach( $testdata as $for => $entries )
		{
			foreach( $entries as $entry )
			{
				$entry['type.for'] = $for;
				$manager->save( $manager->create()->fromArray( $entry ), false );
			}
		}

		$manager->commit();
	}
}
