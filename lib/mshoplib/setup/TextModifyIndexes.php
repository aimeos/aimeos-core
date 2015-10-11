<?php

/**
 * @copyright Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Modifies indexes in the text tables.
 */
class TextModifyIndexes extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'add' => array(
			'mshop_text_list' => array(
				'fk_mstexli_pid' => 'ALTER TABLE "mshop_text_list" ADD INDEX "fk_mstexli_pid" ("parentid")',
			)
		),
		'delete' => array(
			'mshop_text_list' => array(
				'fk_mstexli_parentid' => 'ALTER TABLE "mshop_text_list" DROP INDEX "fk_mstexli_parentid"',
				'unq_mstexli_pid_sid_tid_rid_dm' => 'ALTER TABLE "mshop_text_list" DROP INDEX "unq_mstexli_pid_sid_tid_rid_dm"',
			)
		)
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
	 * Adds and modifies indexes in the text tables.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( array $stmts )
	{
		$this->msg( sprintf( 'Modifying indexes in mshop_text tables' ), 0 );
		$this->status( '' );

		foreach( $stmts['add'] as $table => $indexes )
		{
			foreach( $indexes as $index => $stmt )
			{
				$this->msg( sprintf( 'Checking index "%1$s": ', $index ), 1 );

				if( $this->schema->tableExists( $table ) === true
					&& $this->schema->indexExists( $table, $index ) !== true )
				{
					$this->execute( $stmt );
					$this->status( 'added' );
				}
				else
				{
					$this->status( 'OK' );
				}
			}
		}

		foreach( $stmts['delete'] as $table => $indexes )
		{
			foreach( $indexes as $index => $stmt )
			{
				$this->msg( sprintf( 'Checking index "%1$s": ', $index ), 1 );

				if( $this->schema->tableExists( $table ) === true
					&& $this->schema->indexExists( $table, $index ) === true )
				{
					$this->execute( $stmt );
					$this->status( 'dropped' );
				}
				else
				{
					$this->status( 'OK' );
				}
			}
		}
	}

}