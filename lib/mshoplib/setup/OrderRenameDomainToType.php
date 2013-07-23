<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Renames domain column to type in order tables.
 */
class MW_Setup_Task_OrderRenameDomainToType extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_order' => array(
			'ALTER TABLE "mshop_order" CHANGE "domain" "type" VARCHAR(8) NOT NULL',
		),
		'mshop_order_base_address' => array(
			'ALTER TABLE "mshop_order_base_address" CHANGE "domain" "type" VARCHAR(8) NOT NULL',
			'ALTER TABLE "mshop_order_base_address" DROP INDEX "unq_msordbaad_bid_dom", ADD CONSTRAINT "unq_msordbaad_bid_type" UNIQUE ("baseid", "type")',
		),
		'mshop_order_base_service' => array(
			'ALTER TABLE "mshop_order_base_service" CHANGE "domain" "type" VARCHAR(8) NOT NULL',
			'ALTER TABLE "mshop_order_base_service" DROP INDEX "unq_msordbase_bid_dom_code", ADD CONSTRAINT "unq_msordbase_bid_type_code" UNIQUE ("baseid", "type", "code")',
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
	 * Renames all order tables if they exist.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Renaming order domain', 0 ); $this->_status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			$this->_msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->_schema->tableExists( $table ) && $this->_schema->columnExists( $table, 'domain' ) === true )
			{
				$this->_executeList( $stmtList );
				$this->_status( 'renamed' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
	}

}
