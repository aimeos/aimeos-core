<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds mediaurl column to order base service table.
 */
class MW_Setup_Task_OrderAddBaseServiceColumnMediaUrl extends MW_Setup_Task_Abstract
{
	private $mysql = array(
		'mediaurl' => 'ALTER TABLE "mshop_order_base_service" ADD "mediaurl" VARCHAR(255) NOT NULL AFTER "name"',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderBaseServiceRenameLabel' );
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
	 * Add column to table if the column doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( array $stmts )
	{
		$table = 'mshop_order_base_service';
		$column = 'mediaurl';
		
		$this->msg( sprintf( 'Adding column to table "%1$s"', $table ), 0 ); $this->status( '' );

		$this->msg( sprintf( 'Checking table "%1$s" for column "%2$s": ', $table, $column ), 1 );

		if( $this->schema->tableExists( $table )
			&& $this->schema->columnExists( $table, $column ) === false )
		{
			$this->execute( $stmts[$column] );
			$this->status( 'added' );
		} else {
			$this->status( 'OK' );
		}
	}
	
}