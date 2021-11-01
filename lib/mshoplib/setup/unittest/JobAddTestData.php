<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds admin job test data.
 */
class JobAddTestData extends Base
{

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['Job', 'MShopSetLocale'];
	}


	/**
	 * Adds admin job test data.
	 */
	public function up()
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->context() );

		$this->info( 'Adding admin test data', 'v' );
		$this->context()->setEditor( 'core:lib/mshoplib' );

		$this->addJobTestData();
	}


	/**
	 * Adds the job test data.
	 *
	 * @throws \RuntimeException If a required ID is not available
	 */
	private function addJobTestData()
	{
		$manager = \Aimeos\MAdmin\Job\Manager\Factory::create( $this->context(), 'Standard' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'job.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No file "%1$s" found for job domain', $path ) );
		}

		foreach( $testdata['job'] as $dataset ) {
			$manager->save( $manager->create()->fromArray( $dataset ), false );
		}
	}
}
