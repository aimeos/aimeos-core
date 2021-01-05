<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds admin log test data.
 */
class LogAddTestData extends \Aimeos\MW\Setup\Task\Base
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
	 * Adds admin log test data.
	 */
	public function migrate()
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->additional );

		$this->msg( 'Adding admin log test data', 0 );
		$this->additional->setEditor( 'core:lib/mshoplib' );

		$this->addLogTestData();

		$this->status( 'done' );
	}


	/**
	 * Adds the log test data.
	 *
	 * @throws \Aimeos\MW\Setup\Exception If a required ID is not available
	 */
	private function addLogTestData()
	{
		$manager = \Aimeos\MAdmin\Log\Manager\Factory::create( $this->additional, 'Standard' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'log.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for log domain', $path ) );
		}

		foreach( $testdata['log'] as $dataset ) {
			$manager->save( $manager->create()->fromArray( $dataset ), false );
		}
	}
}
