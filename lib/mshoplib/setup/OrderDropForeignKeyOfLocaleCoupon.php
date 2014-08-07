<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

/**
 * Removes foreign key constraints to locale tables from order tables.
 */
class MW_Setup_Task_OrderDropForeignKeyOfLocaleCoupon extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
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
		return array('TablesCreateCoupon');
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Delete foreign keys to locale domain in mshop order tables.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Delete foreign keys to locale domain in mshop order tables', 0 );
		$this->_status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			$this->_msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->_schema->tableExists( $table ) === true)
			{
				$this->_status( '' );

				foreach( $stmtList AS $constraint => $stmt )
				{
					$this->_msg( sprintf( 'Checking constraint "%1$s": ', $constraint ), 1 );

					if ( $this->_schema->constraintExists( $table, $constraint ) === true )
					{
						$this->_execute( $stmt );
						$this->_status( 'deleted' );
					}
					else
					{
						$this->_status( 'OK' );
					}
				}
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
	}
}
