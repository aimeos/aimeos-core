<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'OrderAddTestData', 'JobAddTestData' );
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
	 * Adds admin log test data.
	 */
	public function migrate()
	{
		$iface = '\\Aimeos\\MShop\\Context\\Item\\Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding admin log test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

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
		$adminLogManager = \Aimeos\MAdmin\Log\Manager\Factory::createManager( $this->additional, 'Standard' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'log.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for log domain', $path ) );
		}

		$log = $adminLogManager->createItem();

		$this->conn->begin();

		foreach( $testdata['log'] as $dataset )
		{
			$log->setId( null );
			$log->setFacility( $dataset['facility'] );
			$log->setPriority( $dataset['priority'] );
			$log->setMessage( $dataset['message'] );
			$log->setRequest( $dataset['request'] );

			$adminLogManager->saveItem( $log, false );
		}

		$this->conn->commit();
	}

}