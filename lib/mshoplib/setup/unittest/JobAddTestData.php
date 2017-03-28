<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds admin job test data.
 */
class JobAddTestData extends \Aimeos\MW\Setup\Task\Base
{

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'OrderAddTestData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return [];
	}


	/**
	 * Adds admin job test data.
	 */
	public function migrate()
	{
		$iface = '\\Aimeos\\MShop\\Context\\Item\\Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding admin test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$this->addJobTestData();

		$this->status( 'done' );
	}


	/**
	 * Adds the job test data.
	 *
	 * @throws \Aimeos\MW\Setup\Exception If a required ID is not available
	 */
	private function addJobTestData()
	{
		$adminJobManager = \Aimeos\MAdmin\Job\Manager\Factory::createManager( $this->additional, 'Standard' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'job.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for job domain', $path ) );
		}

		$job = $adminJobManager->createItem();

		$this->conn->begin();

		foreach( $testdata['job'] as $dataset )
		{
			$job->setId( null );
			$job->setLabel( $dataset['label'] );
			$job->setMethod( $dataset['method'] );
			$job->setParameter( $dataset['parameter'] );
			$job->setResult( $dataset['result'] );
			$job->setStatus( $dataset['status'] );

			$adminJobManager->saveItem( $job, false );
		}

		$this->conn->commit();
	}
}