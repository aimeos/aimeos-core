<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2023
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds customer test data.
 */
class GroupAddTestData extends BaseAddTestData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['Group', 'MShopSetLocale'];
	}


	/**
	 * Adds customer test data.
	 */
	public function up()
	{
		$this->info( 'Adding group test data', 'vv' );
		$this->context()->setEditor( 'core' );

		$this->process();
	}


	/**
	 * Adds the customer data
	 *
	 * @throws \RuntimeException
	 */
	protected function process()
	{
		$path = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'group.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No file "%1$s" found for group domain', $path ) );
		}

		$manager = $this->getManager( 'group' );
		$manager->begin();

		foreach( $testdata['group'] as $entry )
		{
			try {
				$manager->save( $manager->find( $entry['group.code'] )->fromArray( $entry ) );
			} catch( \Exception $e ) {
				$manager->save( $manager->create()->fromArray( $entry ), false );
			}
		}

		$manager->commit();
	}
}
