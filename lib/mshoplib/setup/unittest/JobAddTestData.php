<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: JobAddTestData.php 1365 2012-10-31 13:54:32Z doleiynyk $
 */


/**
 * Adds admin job test data.
 */
class MW_Setup_Task_JobAddTestData extends MW_Setup_Task_Abstract
{

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'LocaleAddTestData', 'OrderAddTestData' );
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
	protected function _mysql()
	{
		$this->_process();
	}


	/**
	 * Adds admin job test data.
	 */
	protected function _process()
	{
		$iface = 'MShop_Context_Item_Interface';
		if( !( $this->_additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->_msg( 'Adding admin test data', 0 );
		$this->_additional->setEditor( 'core:unittest' );

		$this->_addJobTestData();

		$this->_status( 'done' );
	}


	/**
	 * Adds the job test data.
	 *
	 * @throws MW_Setup_Exception If a required ID is not available
	 */
	private function _addJobTestData()
	{
		$adminJobManager = MAdmin_Job_Manager_Factory::createManager( $this->_additional, 'Default' );

		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'data' . $ds . 'job.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for job domain', $path ) );
		}

		$job = $adminJobManager->createItem();

		$this->_conn->begin();

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

		$this->_conn->commit();
	}
}