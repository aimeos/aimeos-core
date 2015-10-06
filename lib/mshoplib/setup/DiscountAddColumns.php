<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

/*
 * Creates all required columns
 */
class MW_Setup_Task_DiscountAddColumns extends MW_Setup_Task_Abstract
{
	private $mysql = array(
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

		foreach( $stmts as $table=>$stmtList )
		{
			$this->msg( sprintf( 'Adding columns to table "%1$s"', $table ), 0 ); $this->status( '' );

			if( $this->schema->tableExists( $table ) === true )
			{
				foreach( $stmtList as $column=>$stmt )
				{
					$this->msg( sprintf( 'Checking column "%1$s": ', $column ), 1 );

					if( $this->schema->columnExists( $table, $column ) === false )
					{
						$this->execute( $stmt );
						$this->status( 'added' );
					} else {
						$this->status( 'OK' );
					}
				}
			}
		}
	}
}