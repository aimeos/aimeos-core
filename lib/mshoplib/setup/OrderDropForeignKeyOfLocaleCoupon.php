<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

/**
 * Removes foreign key constraints to locale tables from order tables.
 */
class MW_Setup_Task_OrderDropForeignKeyOfLocaleCoupon extends MW_Setup_Task_Base
{
	private $mysql = array(
		'mshop_order_base_coupon' => array(
			'fk_msordbaco_siteid' => 'ALTER TABLE "mshop_order_base_coupon" DROP FOREIGN KEY "fk_msordbaco_siteid"',
		),
	);




	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderBaseCouponAddSiteidConstraint' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'TablesCreateCoupon' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function mysql()
	{
		$this->process( $this->mysql );
	}


	/**
	 * Delete foreign keys to locale domain in mshop order tables.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Delete foreign keys to locale domain in mshop order tables', 0 );
		$this->status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->schema->tableExists( $table ) === true )
			{
				$this->status( '' );

				foreach( $stmtList as $constraint => $stmt )
				{
					$this->msg( sprintf( 'Checking constraint "%1$s": ', $constraint ), 1 );

					if( $this->schema->constraintExists( $table, $constraint ) === true )
					{
						$this->execute( $stmt );
						$this->status( 'deleted' );
					}
					else
					{
						$this->status( 'OK' );
					}
				}
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}
}
