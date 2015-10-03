<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds typeid column to product table.
 */
class MW_Setup_Task_ProductAddTypeid extends MW_Setup_Task_Base
{
	private $mysql = array(
		'ALTER TABLE "mshop_product" ADD "typeid" INTEGER NULL AFTER "id"',
		'ALTER TABLE "mshop_product" ADD CONSTRAINT "fk_mspro_typeid" FOREIGN KEY ( "typeid" ) REFERENCES "mshop_product_type" ("id") ON DELETE CASCADE ON UPDATE CASCADE',
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
	 * Add column to table if it doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( $stmts )
	{
		$this->msg( 'Adding typeid column to product table', 0 ); $this->status( '' );

		$this->msg( sprintf( 'Checking table "%1$s": ', 'mshop_product' ), 1 );

		if( $this->schema->tableExists( 'mshop_product' ) === true
			&& $this->schema->columnExists( 'mshop_product', 'typeid' ) === false )
		{
			$this->executeList( $stmts );
			$this->status( 'added' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}