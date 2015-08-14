<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Renames price column to value in price table.
 */
class MW_Setup_Task_PriceRenameColumnPriceToValue extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_price' => 'ALTER TABLE "mshop_price" CHANGE "price" "value" DECIMAL(12,2) NOT NULL',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderRenameTables', 'OrderAddComment' );
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
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Renames all order tables if they exist.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Renaming column "price" to "value"', 0 ); $this->_status( '' );

		foreach( $stmts as $table=>$stmt )
		{
			$this->_msg( sprintf( 'Checking table "%1$s"', $table ), 1 );

			if( $this->_schema->tableExists( $table ) && $this->_schema->columnExists( $table, 'price' ) === true )
			{
				$this->_execute( $stmt );
				$this->_status( 'renamed' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
	}
}
