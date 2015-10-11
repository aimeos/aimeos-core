<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Changes langid column to allow NULL values.
 */
class TextChangeLangidNull extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'langid' => 'ALTER TABLE "mshop_text" CHANGE "langid" "langid" CHAR(2) DEFAULT NULL',
	);

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array();
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
		$this->msg( 'Changing langid of mshop_text table', 0 ); $this->status( '' );

		foreach( $stmts as $column => $stmt )
		{
			$this->msg( sprintf( 'Checking column "%1$s": ', $column ), 1 );

			if( $this->schema->tableExists( 'mshop_text' ) === true
				&& $this->schema->columnExists( 'mshop_text', $column ) === true
				&& $this->schema->getColumnDetails( 'mshop_text', $column )->isNullable() === false )
			{
				$this->execute( $stmt );
				$this->status( 'migrated' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}
}
