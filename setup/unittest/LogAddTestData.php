<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2022
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds admin log test data.
 */
class LogAddTestData extends Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['Log', 'MShopSetLocale'];
	}


	/**
	 * Adds admin log test data.
	 */
	public function up()
	{
		map( [$this->context()] )->implements( \Aimeos\MShop\ContextIface::class, true );

		$this->info( 'Adding admin log test data', 'v' );
		$this->context()->setEditor( 'core:lib/mshoplib' );

		$this->addLogTestData();
	}


	/**
	 * Adds the log test data.
	 */
	private function addLogTestData()
	{
		$manager = \Aimeos\MAdmin\Log\Manager\Factory::create( $this->context(), 'Standard' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'log.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No file "%1$s" found for log domain', $path ) );
		}

		foreach( $testdata['log'] as $dataset ) {
			$manager->save( $manager->create()->fromArray( $dataset ), false );
		}
	}
}
