<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Creates all required tables.
 */
class DiscountMoveTablesAndColumesToCoupon extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_discount' => array(
			'RENAME TABLE "mshop_discount" TO "mshop_coupon"',
		),
		'mshop_discount_code' => array(
			'RENAME TABLE "mshop_discount_code" TO "mshop_coupon_code"',
		),
		'mshop_order_base_discount' => array(
			'RENAME TABLE "mshop_order_base_discount" TO "mshop_order_base_coupon"',
			'ALTER TABLE "mshop_order_base_coupon"
				DROP FOREIGN KEY "fk_msordbadi_baseid",
				DROP INDEX "fk_msordbadi_baseid"',
			'ALTER TABLE "mshop_order_base_coupon"
				ADD CONSTRAINT "fk_msordbaco_baseid"
					FOREIGN KEY ("baseid")
					REFERENCES "mshop_order_base" ("id") ON UPDATE CASCADE ON DELETE CASCADE',
			'ALTER TABLE "mshop_order_base_coupon" DROP FOREIGN KEY "fk_msordbadi_ordprodid"',
			'ALTER TABLE "mshop_order_base_coupon" DROP INDEX "fk_msordbadi_ordbaprod"',
			'ALTER TABLE "mshop_order_base_coupon"
				ADD CONSTRAINT "fk_msordbaco_ordprodid"
					FOREIGN KEY ("ordprodid")
					REFERENCES "mshop_order_base_product" ("id") ON UPDATE CASCADE ON DELETE CASCADE'
		),

		'mshop_coupon_code' => array(
			'ALTER TABLE "mshop_coupon_code"
				DROP INDEX "idx_msdisco_count_start_end"',
			'ALTER TABLE "mshop_coupon_code"
				ADD INDEX "idx_mscouco_count_start_end" ("count", "start", "end")',
			'ALTER TABLE "mshop_coupon_code"
				DROP INDEX "pk_msdisco_code_siteid"',
			'ALTER TABLE "mshop_coupon_code"
				ADD CONSTRAINT "unq_mscouco_code_siteid"
					UNIQUE ("code", "siteid")',
			'ALTER TABLE "mshop_coupon_code"
				DROP FOREIGN KEY "fk_msdisco_discountid",
				DROP INDEX "fk_msdisco_discountid"',
			'ALTER TABLE "mshop_coupon_code"
				CHANGE "discountid" "couponid" INTEGER NOT NULL',
			'ALTER TABLE "mshop_coupon_code"
				DROP FOREIGN KEY "fk_msdisco_siteid",
				DROP INDEX "fk_msdisco_siteid"',
			'ALTER TABLE "mshop_coupon_code"
				ADD INDEX "fk_mscouco_siteid" ("siteid")',
			'ALTER TABLE "mshop_coupon_code"
				ADD CONSTRAINT "fk_mscouco_siteid"
					FOREIGN KEY ("siteid")
					REFERENCES "mshop_locale_site" ("id") ON UPDATE CASCADE ON DELETE CASCADE',
			'ALTER TABLE "mshop_coupon_code"
				ADD CONSTRAINT "fk_mscouco_couponid"
					FOREIGN KEY ("couponid")
					REFERENCES "mshop_coupon" ("id") ON UPDATE CASCADE ON DELETE CASCADE',
			'ALTER TABLE "mshop_coupon_code"
				ADD INDEX "idx_mscouco_code" ("code")',
		),
		'mshop_coupon' => array(
			'ALTER TABLE "mshop_coupon"
				DROP FOREIGN KEY "fk_msdis_siteid",
				DROP INDEX "fk_msdis_siteid"',
			'ALTER TABLE "mshop_coupon"
				ADD CONSTRAINT "fk_mscou_siteid"
					FOREIGN KEY ("siteid")
					REFERENCES "mshop_locale_site" ("id") ON UPDATE CASCADE ON DELETE CASCADE',
			'ALTER TABLE "mshop_coupon"
				DROP INDEX "idx_msdis_start_end"',
			'ALTER TABLE "mshop_coupon"
				ADD INDEX "idx_mscou_start_end" ("start", "end")'
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array(
			'StatusToSmallInt',
			'OrderRenameTables',
			'OrderAddComment',
			'DiscountAddColumns',
			'DiscountAddForeignKey',
			'PriceRenameColumnDiscountToRebate',
			'OrderRenameConstraints',
			'OrderAddSiteId',
		);
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
	 * Renames all order tables if they exist.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Renaming discount tables', 0 ); $this->status( '' );

		if( $this->schema->tableExists( 'mshop_discount' ) )
		{
			foreach( $stmts as $table => $stmtList )
			{
				if( $this->schema->tableExists( $table ) )
				{
					$this->msg( sprintf( 'Process table "%1$s": ', $table ), 1 );
					$this->executeList( $stmtList );
					$this->status( 'migrated' );
				}
			}
		}
	}

}
