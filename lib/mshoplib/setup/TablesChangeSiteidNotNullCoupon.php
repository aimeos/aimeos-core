<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Changes site ID to NOT NULL.
 */
class MW_Setup_Task_TablesChangeSiteidNotNullCoupon extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
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
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}

	/**
	 * Changes site ID to NOT NULL and migrates existing entries.
	 *
	 * @param array $stmts Associative array of tables names and SQL statements.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Changing site ID to NOT NULL in Arcavias Coupon Extension', 0 );
		$this->_status('');

		foreach ( $stmts as $table => $stmtList )
		{
			$this->_msg(sprintf('Changing table "%1$s": ', $table), 1);

			if( $this->_schema->tableExists($table) &&
				$this->_schema->getColumnDetails( $table, 'siteid' )->isNullable() )
			{
				$this->_executeList( $stmtList );
				$this->_status( 'done' );
			} else {
				$this->_status( 'OK' );
			}
		}
	}

}
