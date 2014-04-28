<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH
 * @version $Id: DiscountAddColumns.php 37 2012-08-08 17:37:40Z fblasel $
 */

/*
 * Creates all required columns
 */
class MW_Setup_Task_DiscountAddColumns extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_discount' => array(
			'start' => 'ALTER TABLE "mshop_discount" ADD "start" DATETIME DEFAULT NULL AFTER "config"',
			'end' => 'ALTER TABLE "mshop_discount" ADD "end" DATETIME DEFAULT NULL AFTER "start"',
		),
		'mshop_discount_code' => array(
			'start' => 'ALTER TABLE "mshop_discount_code" ADD "start" DATETIME DEFAULT NULL AFTER "count"',
			'end' => 'ALTER TABLE "mshop_discount_code" ADD "end" DATETIME DEFAULT NULL AFTER "start"',
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
	 * Add column to table if the column doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{

		foreach( $stmts AS $table=>$stmtList )
		{
			$this->_msg( sprintf( 'Adding columns to table "%1$s"', $table ), 0 ); $this->_status( '' );

			if( $this->_schema->tableExists( $table ) === true )
			{
				foreach ( $stmtList AS $column=>$stmt )
				{
					$this->_msg( sprintf( 'Checking column "%1$s": ', $column ), 1 );

					if( $this->_schema->columnExists( $table, $column ) === false )
					{
						$this->_execute( $stmt );
						$this->_status( 'added' );
					} else {
						$this->_status( 'OK' );
					}
				}
			}
		}
	}
}