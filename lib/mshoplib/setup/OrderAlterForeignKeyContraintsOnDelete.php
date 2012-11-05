<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: OrderAlterForeignKeyContraintsOnDelete.php 14841 2012-01-12 21:51:09Z nsendetzky $
 */


/**
 * Changes action on delete on mshop_locale_site FOREIGN KEY CONSTRAINTS for mshop_order tables.
 *
 * 2012-08-08
 * At this time "columne", "drop", "adding" the constrain... adding is removed
 * because of future dependency. see: MW_Setup_Task_OrderDropForeignKeyOfLocale
 * -> Order domain table can be used on a differend database/ server
 */
class MW_Setup_Task_OrderAlterForeignKeyContraintsOnDelete extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_order' => array (
			'fk_msord_siteid' => array(
				'column' =>	'ALTER TABLE "mshop_order" CHANGE COLUMN "siteid" "siteid" INTEGER NULL',
				'drop' => 'ALTER TABLE "mshop_order" DROP FOREIGN KEY "fk_msord_siteid"',
			),
		),
		'mshop_order_base' => array (
			'fk_msordba_siteid' => array(
				'column' =>	'ALTER TABLE "mshop_order_base" CHANGE COLUMN "siteid" "siteid" INTEGER NULL',
				'drop' => 'ALTER TABLE "mshop_order_base" DROP FOREIGN KEY "fk_msordbaad_siteid"',
			),
		),
		'mshop_order_base_address' => array (
			'fk_msordbaad_siteid' => array(
				'column' =>	'ALTER TABLE "mshop_order_base_address" CHANGE COLUMN "siteid" "siteid" INTEGER NULL',
				'drop' => 'ALTER TABLE "mshop_order_base_address" DROP FOREIGN KEY "fk_msordbaad_siteid"',
			),
		),
		'mshop_order_base_product' => array (
			'fk_msordbapr_siteid' => array(
				'column' =>	'ALTER TABLE "mshop_order_base_product" CHANGE COLUMN "siteid" "siteid" INTEGER NULL',
				'drop' => 'ALTER TABLE "mshop_order_base_product" DROP FOREIGN KEY "fk_msordbapr_siteid"',
			),
		),
		'mshop_order_base_product_attr' => array (
			'fk_msordbaprat_siteid' => array(
				'column' =>	'ALTER TABLE "mshop_order_base_product_attr" CHANGE COLUMN "siteid" "siteid" INTEGER NULL',
				'drop' => 'ALTER TABLE "mshop_order_base_product_attr" DROP FOREIGN KEY "fk_msordbaprat_siteid"',
			),
		),
		'mshop_order_base_service' => array (
			'fk_msordbase_siteid' => array(
				'column' =>	'ALTER TABLE "mshop_order_base_service" CHANGE COLUMN "siteid" "siteid" INTEGER NULL',
				'drop' => 'ALTER TABLE "mshop_order_base_service" DROP FOREIGN KEY "fk_msordbase_siteid"',
			),
		),
		'mshop_order_base_service_attr' => array (
			'fk_msordbaseat_siteid' => array(
				'column' =>	'ALTER TABLE "mshop_order_base_service_attr" CHANGE COLUMN "siteid" "siteid" INTEGER NULL',
				'drop' => 'ALTER TABLE "mshop_order_base_service_attr" DROP FOREIGN KEY "fk_msordbaseat_siteid"',
			),
		),
		'mshop_order_status' => array (
			'fk_msordst_siteid' => array(
				'column' =>	'ALTER TABLE "mshop_order_status" CHANGE COLUMN "siteid" "siteid" INTEGER NULL',
				'drop' => 'ALTER TABLE "mshop_order_status" DROP FOREIGN KEY "fk_msordst_siteid"',
			),
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array('OrderRenameTables', 'OrderRenameConstraints', 'OrderAddSiteId');
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'TablesCreateMShop' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Changes CONSTRAINT action ON DELETE for order tables.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Change order siteid foreign key constraints', 0 ); $this->_status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			foreach ( $stmtList as $constraint => $stmts )
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
