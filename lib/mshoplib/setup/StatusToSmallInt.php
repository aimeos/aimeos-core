<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Changes status columns to SMALLINT values.
 */
class MW_Setup_Task_StatusToSmallInt extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_attribute' => 'ALTER TABLE "mshop_attribute" CHANGE "status" "status" SMALLINT NOT NULL DEFAULT 0',
		'mshop_catalog' => 'ALTER TABLE "mshop_catalog" CHANGE "status" "status" SMALLINT NOT NULL DEFAULT 0',
		'mshop_customer' => 'ALTER TABLE "mshop_customer" CHANGE "status" "status" SMALLINT NOT NULL DEFAULT 0',
		'mshop_discount' => 'ALTER TABLE "mshop_discount" CHANGE "status" "status" SMALLINT NOT NULL DEFAULT 0',
		'mshop_global_language' => 'ALTER TABLE "mshop_global_language" CHANGE "status" "status" SMALLINT NOT NULL DEFAULT 0',
		'mshop_global_currency' => 'ALTER TABLE "mshop_global_currency" CHANGE "status" "status" SMALLINT NOT NULL DEFAULT 0',
		'mshop_global_site' => 'ALTER TABLE "mshop_global_site" CHANGE "status" "status" SMALLINT NOT NULL DEFAULT 0',
		'mshop_media' => 'ALTER TABLE "mshop_media" CHANGE "status" "status" SMALLINT NOT NULL DEFAULT 0',
		'mshop_order_base' => 'ALTER TABLE "mshop_order_base" CHANGE "status" "status" SMALLINT NOT NULL DEFAULT 0',
		'mshop_plugin' => 'ALTER TABLE "mshop_plugin" CHANGE "status" "status" SMALLINT NOT NULL DEFAULT 0',
		'mshop_price' => 'ALTER TABLE "mshop_price" CHANGE "status" "status" SMALLINT NOT NULL DEFAULT 0',
		'mshop_product' => 'ALTER TABLE "mshop_product" CHANGE "status" "status" SMALLINT NOT NULL DEFAULT 0',
		'mshop_service' => 'ALTER TABLE "mshop_service" CHANGE "status" "status" SMALLINT NOT NULL DEFAULT 0',
		'mshop_supplier' => 'ALTER TABLE "mshop_supplier" CHANGE "status" "status" SMALLINT NOT NULL DEFAULT 0',
		'mshop_text' => 'ALTER TABLE "mshop_text" CHANGE "status" "status" SMALLINT NOT NULL DEFAULT 0',
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
		return array('TablesCreateMShop');
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Migrates service text data to list table.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$column = 'status';
		$this->_msg( 'Changing status columns', 0 ); $this->_status( '' );

		foreach( $stmts as $table=>$stmt )
		{
			$this->_msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->_schema->tableExists( $table ) === true
				&& $this->_schema->columnExists( $table, $column ) === true
				&& $this->_schema->getColumnDetails( $table, $column )->getDataType() !== "smallint" )
			{
				$this->_execute( $stmt );
				$this->_status( 'migrated' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
	}
}
