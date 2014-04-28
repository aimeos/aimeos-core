<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH
 * @version $Id: DiscountAddForeignKey.php 37 2012-08-08 17:37:40Z fblasel $
 */


/**
 * Creates all required tables.
 */
class MW_Setup_Task_DiscountAddForeignKey extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_discount' => array(
			'ALTER TABLE "mshop_discount" ADD CONSTRAINT "fk_msdis_siteid" FOREIGN KEY (`siteid`) REFERENCES "mshop_global_site" ("id") ON DELETE CASCADE ON UPDATE CASCADE'
		),
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
	 * Migrates service text data to list table.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Adding foreign keys to mshop discount table', 0 ); $this->_status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			$this->_msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->_schema->tableExists( $table ) === true
				&& $this->_schema->constraintExists( $table, 'fk_msdis_siteid' ) === false )
			{
				$this->_executeList( $stmtList );
				$this->_status( 'migrated' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
	}
}
