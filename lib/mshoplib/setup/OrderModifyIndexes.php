<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Modifies indexes in the order tables.
 */
class MW_Setup_Task_OrderModifyIndexes extends MW_Setup_Task_Base
{
	private $mysql = array(
		'add' => array(
			'mshop_order_base_product' => array(
				'unq_msordbapr_bid_pos' => 'ALTER TABLE "mshop_order_base_product" ADD UNIQUE INDEX "unq_msordbapr_bid_pos" ("baseid", "pos")'
			)
		),

		'delete' => array(
			'mshop_order' => array(
				'idx_msord_sid_pstat_dstat_pdate' => 'ALTER TABLE "mshop_order" DROP INDEX "idx_msord_sid_pstat_dstat_pdate"',
				'idx_msord_pdate_pstat_dstat' => 'ALTER TABLE "mshop_order" DROP INDEX "idx_msord_pdate_pstat_dstat"',
				'idx_msord_pstat_dstat_email' => 'ALTER TABLE "mshop_order" DROP INDEX "idx_msord_pstat_dstat_email"',
				'idx_msord_pstat_dstat_flag' => 'ALTER TABLE "mshop_order" DROP INDEX "idx_msord_pstat_dstat_flag"',
			),
			'mshop_order_base_address' => array(
				'idx_msordbaad_bid_typ_sid' => 'ALTER TABLE "mshop_order_base_address" DROP INDEX "idx_msordbaad_bid_typ_sid"'
			),
			'mshop_order_base_product' => array(
				'idx_msordbapr_bid_pcd_sid' => 'ALTER TABLE "mshop_order_base_product" DROP INDEX "idx_msordbapr_bid_pcd_sid"'
			),
			'mshop_order_base_product_attr' => array(
				'idx_msordbaprat_oid_cd_val_sid' => 'ALTER TABLE "mshop_order_base_product_attr" DROP INDEX "idx_msordbaprat_oid_cd_val_sid"'
			),
			'mshop_order_base_service' => array(
				'idx_msordbase_bid_cd_typ_sid' => 'ALTER TABLE "mshop_order_base_service" DROP INDEX "idx_msordbase_bid_cd_typ_sid"'
			),
			'mshop_order_base_service_attr' => array(
				'idx_msordbaseat_oid_cd_val_sid' => 'ALTER TABLE "mshop_order_base_service_attr" DROP INDEX "idx_msordbaseat_oid_cd_val_sid"'
			)
		)
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
	 * Adds and modifies indexes in the mshop_order tables.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( array $stmts )
	{
		$this->msg( sprintf( 'Modifying indexes in mshop_order tables' ), 0 );
		$this->status( '' );

		foreach( $stmts['add'] as $table => $indexes )
		{
			foreach( $indexes as $index => $stmt )
			{
				$this->msg( sprintf( 'Checking index "%1$s": ', $index ), 1 );

				if( $this->schema->tableExists( $table ) === true
					&& $this->schema->indexExists( $table, $index ) !== true )
				{
					$this->execute( $stmt );
					$this->status( 'added' );
				}
				else
				{
					$this->status( 'OK' );
				}
			}
		}

		foreach( $stmts['delete'] as $table => $indexes )
		{
			foreach( $indexes as $index => $stmt )
			{
				$this->msg( sprintf( 'Checking index "%1$s": ', $index ), 1 );

				if( $this->schema->tableExists( $table ) === true
					&& $this->schema->indexExists( $table, $index ) === true )
				{
					$this->execute( $stmt );
					$this->status( 'dropped' );
				}
				else
				{
					$this->status( 'OK' );
				}
			}
		}
	}

}