<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds product domain to tag entries
 */
class TagAddDomain extends \Aimeos\MW\Setup\Task\Base
{
	private $stmt = 'UPDATE "mshop_tag" SET "domain"=\'product\' WHERE "domain" = \'\'';


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'TagMoveProductTag', 'TablesCreateMShop' );
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
	 * Creates the MShop tables
	 */
	public function migrate()
	{
		$this->msg( 'Adding domain to tag tables', 0 );
		$rows = 0;

		if( $this->getSchema( 'db-tag' )->tableExists( 'mshop_tag' ) === true )
		{
			$result = $this->getConnection( 'db-tag' )->create( $this->stmt )->execute();
			$rows = $result->affectedRows();
			$result->finish();
		}

		if( $rows > 0 ) {
			$this->status( 'done' );
		} else {
			$this->status( 'OK' );
		}
	}
}