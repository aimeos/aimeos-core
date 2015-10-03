<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Changes the column type of mshop_order_base_*_attr.value from VARCHAR to TEXT.
 */
class MW_Setup_Task_OrderBaseAttributeChangeValueType extends MW_Setup_Task_Base
{
	private $mysql = array(
		'mshop_order_base_product_attr' => array(
			'index' => array(
				'idx_msordbaprat_si_oi_ty_cd_va' => 'DROP INDEX "idx_msordbaprat_si_oi_ty_cd_va" ON "mshop_order_base_product_attr"',
				'idx_msordbaprat_si_cd_va' => 'DROP INDEX "idx_msordbaprat_si_cd_va" ON "mshop_order_base_product_attr"',
			),
			'column' => 'ALTER TABLE "mshop_order_base_product_attr" MODIFY "value" TEXT NOT NULL',
		),
		'mshop_order_base_service_attr' => array(
			'index' => array(
				'idx_msordbaseat_si_oi_ty_cd_va' => 'DROP INDEX "idx_msordbaseat_si_oi_ty_cd_va" ON "mshop_order_base_service_attr"',
				'idx_msordbaseat_si_cd_va' => 'DROP INDEX "idx_msordbaseat_si_cd_va" ON "mshop_order_base_service_attr"',
			),
			'column' => 'ALTER TABLE "mshop_order_base_service_attr" MODIFY "value" TEXT NOT NULL',
		),
	);

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderRenameTables' );
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
		$this->msg( 'Changing attribute value type in order domain', 0 );
		$this->status( '' );

		foreach( $stmts as $table => $stmts )
		{
			$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->schema->tableExists( $table ) === true &&
				$this->schema->columnExists( $table, 'value' ) === true &&
				$this->schema->getColumnDetails( $table, 'value' )->getDataType() === 'varchar'
			) {
				foreach( $stmts['index'] as $index => $sql )
				{
					if( $this->schema->indexExists( $table, $index ) === true ) {
						$this->execute( $sql );
					}
				}

				$this->execute( $stmts['column'] );
				$this->status( 'changed' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}

}
