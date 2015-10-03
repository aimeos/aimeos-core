<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Changes site ID to NOT NULL.
 */
class MW_Setup_Task_TablesChangeSiteidNotNullCoupon extends MW_Setup_Task_Base
{
	private $mysql = array(
		'mshop_coupon' => array(
			'UPDATE "mshop_coupon" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_coupon" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_coupon_code' => array(
			'UPDATE "mshop_coupon_code" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_coupon_code" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_order_base_coupon' => array(
			'UPDATE "mshop_order_base_coupon" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
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
	 * Changes site ID to NOT NULL and migrates existing entries.
	 *
	 * @param array $stmts Associative array of tables names and SQL statements.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Changing site ID to NOT NULL in Aimeos Coupon Extension', 0 );
		$this->status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			$this->msg( sprintf( 'Changing table "%1$s": ', $table ), 1 );

			if( $this->schema->tableExists( $table ) &&
				$this->schema->getColumnDetails( $table, 'siteid' )->isNullable() )
			{
				$this->executeList( $stmtList );
				$this->status( 'done' );
			} else {
				$this->status( 'OK' );
			}
		}
	}

}
