<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds admin job test data.
 */
class MW_Setup_Task_JobAddTestData extends MW_Setup_Task_Base
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
		return array();
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function mysql()
	{
		$this->process();
	}


	/**
	 * Adds admin job test data.
	 */
	protected function process()
	{
		$iface = 'MShop_Context_Item_Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding admin test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$this->addJobTestData();

		$this->status( 'done' );
	}


	/**
	 * Adds the job test data.
	 *
	 * @throws MW_Setup_Exception If a required ID is not available
	 */
	private function addJobTestData()
	{
		$adminJobManager = MAdmin_Job_Manager_Factory::createManager( $this->additional, 'Standard' );

		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'data' . $ds . 'job.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for job domain', $path ) );
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