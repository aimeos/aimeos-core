<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

/**
 * Adds a foreign key constraint on mshop_locale_site to mshop_order_base_coupon.
 *
 * 2012-08-08
 * At this time "columne", "drop", "adding" the constrain... adding is removed
 * because of future dependency. see: MW_Setup_Task_OrderDropForeignKeyOfLocale
 * -> Order domain table can be used on a differend database/ server
 */
class MW_Setup_Task_OrderBaseCouponAddSiteidConstraint extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_order_base_coupon' => array(
			'fk_msordbaco_siteid' => array(
				'column' =>	'ALTER TABLE "mshop_order_base_coupon" CHANGE COLUMN "siteid" "siteid" INTEGER NULL',
				'drop' => 'ALTER TABLE "mshop_order_base_coupon" DROP FOREIGN KEY "fk_msordbaco_siteid"',
			),
		),
	);




	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderRenameTables', 'OrderRenameConstraints', 'OrderAddSiteId' );
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
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Add constraint to mshop_order_base_coupon if the contraint doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Change order coupon siteid foreign key constraints', 0 ); $this->_status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			foreach( $stmtList as $constraint => $stmts )
			{
				$this->_msg( sprintf( 'Checking constraint "%1$s": ', $constraint ), 1 );

				if( $this->_schema->tableExists( $table )
					&& $this->_schema->getColumnDetails( $table, 'siteid' )->isNullable() === false )
				{
					$this->_execute( $stmts['column'] );

					if( $this->_schema->constraintExists( $table, $constraint ) === true ) {
						$this->_execute( $stmts['drop'] );
					}

					$this->_status( 'changed' );
				}
				else
				{
					$this->_status( 'OK' );
				}
			}
		}
	}
}
