<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds label column to text table.
 */
class TextAddLabel extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'ALTER TABLE "mshop_text" ADD "label" VARCHAR(255) NOT NULL AFTER "domain"',
		'UPDATE "mshop_text" SET "label" = "content" WHERE "label" = \'\'',
	);

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'TextChangeTextToContent' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'TablesCreateMShop' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function mysql()
	{
		$this->process( $this->mysql );
	}


	/**
	 * Migrates service text data to list table.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Adding label to mshop text table', 0 );

		if( $this->schema->tableExists( 'mshop_text' ) === true
			&& $this->schema->columnExists( 'mshop_text', 'label' ) === false )
		{
			$this->executeList( $stmts );
			$this->status( 'added' );
		} else {
			$this->status( 'OK' );
		}
	}

}
