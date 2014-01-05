<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Changes the column type of mshop_order_base_*_attr.value from VARCHAR to TEXT.
 */
class MW_Setup_Task_OrderBaseAttributeChangeValueType extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
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
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderRenameTables' );
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
	 * Add column to table if the column doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Changing attribute value type in order domain', 0 );
		$this->_status( '' );

		foreach( $stmts as $table => $stmts )
		{
			$this->_msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->_schema->tableExists( $table ) === true &&
				$this->_schema->columnExists( $table, 'value' ) === true &&
				$this->_schema->getColumnDetails( $table, 'value' )->getDataType() === 'varchar'
			) {
				foreach( $stmts['index'] as $index => $sql )
				{
					if( $this->_schema->indexExists( $table, $index ) === true ) {
						$this->_execute( $sql );
					}
				}

				$this->_execute( $stmts['column'] );
				$this->_status( 'changed' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
	}

}
