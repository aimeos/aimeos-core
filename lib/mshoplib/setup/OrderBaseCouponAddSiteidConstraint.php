<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

namespace Aimeos\MW\Setup\Task;


/**
 * Adds a foreign key constraint on mshop_locale_site to mshop_order_base_coupon.
 *
 * 2012-08-08
 * At this time "columne", "drop", "adding" the constrain... adding is removed
 * because of future dependency. see: \Aimeos\MW\Setup\Task\OrderDropForeignKeyOfLocale
 * -> Order domain table can be used on a differend database/ server
 */
class OrderBaseCouponAddSiteidConstraint extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
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
	protected function mysql()
	{
		$this->process( $this->mysql );
	}


	/**
	 * Add constraint to mshop_order_base_coupon if the contraint doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Change order coupon siteid foreign key constraints', 0 ); $this->status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			foreach( $stmtList as $constraint => $stmts )
			{
				$this->msg( sprintf( 'Checking constraint "%1$s": ', $constraint ), 1 );

				if( $this->schema->tableExists( $table )
					&& $this->schema->getColumnDetails( $table, 'siteid' )->isNullable() === false )
				{
					$this->execute( $stmts['column'] );

					if( $this->schema->constraintExists( $table, $constraint ) === true ) {
						$this->execute( $stmts['drop'] );
					}

					$this->status( 'changed' );
				}
				else
				{
					$this->status( 'OK' );
				}
			}
		}
	}
}
