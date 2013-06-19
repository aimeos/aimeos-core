<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Changes product typeid column to NOT NULL.
 */
class MW_Setup_Task_ProductChangeTypeidNotNull extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'UPDATE "mshop_product"
			SET "typeid" = ( SELECT "id" FROM "mshop_product_type" WHERE "siteid" IS NULL AND "domain" = \'product\' AND "code" = \'product\' )
			WHERE "typeid" IS NULL',
		'ALTER TABLE "mshop_product" CHANGE "typeid" "typeid" INTEGER NOT NULL',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array('ProductAddTypeid');
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array('TablesCreateMShop');
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}

	/**
	 * Add column to table if it doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( $stmts )
	{
		$this->_msg( 'Changing typeid column of product table', 0 ); $this->_status( '' );

		$this->_msg( sprintf( 'Checking table "%1$s": ', 'mshop_product' ), 1 );

		if( $this->_schema->tableExists( 'mshop_product' ) === true
			&& $this->_schema->columnExists( 'mshop_product', 'typeid' ) === true
			&& $this->_schema->getColumnDetails( 'mshop_product', 'typeid' )->isNullable() === true )
		{
			$this->_executeList( $stmts );
			$this->_status( 'migrated' );
		}
		else
		{
			$this->_status( 'OK' );
		}
	}
}