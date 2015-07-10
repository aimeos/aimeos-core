<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Removes foreign key constraints to locale tables from order tables.
 */
class MW_Setup_Task_OrderDropForeignKeyOfLocale extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_order_base' => array(
			'fk_msordba_langid' => 'ALTER TABLE "mshop_order_base" DROP FOREIGN KEY "fk_msordba_langid"',
			'fk_msordba_curid' => 'ALTER TABLE "mshop_order_base" DROP FOREIGN KEY "fk_msordba_curid"',
			'fk_msordba_siteid' => 'ALTER TABLE "mshop_order_base" DROP FOREIGN KEY "fk_msordba_siteid"',
		),
		'mshop_order' => array(
			'fk_msord_siteid' => 'ALTER TABLE "mshop_order" DROP FOREIGN KEY "fk_msord_siteid"',
		),
		'mshop_order_base_address' => array(
			'fk_msordbaad_siteid' => 'ALTER TABLE "mshop_order_base_address" DROP FOREIGN KEY "fk_msordbaad_siteid"',
		),
		'mshop_order_base_product' => array(
			'fk_msordbapr_siteid' => 'ALTER TABLE "mshop_order_base_product" DROP FOREIGN KEY "fk_msordbapr_siteid"',
		),
		'mshop_order_base_product_attr' => array(
			'fk_msordbaprat_siteid' => 'ALTER TABLE "mshop_order_base_product_attr" DROP FOREIGN KEY "fk_msordbaprat_siteid"',
		),
		'mshop_order_base_service' => array(
			'fk_msordbase_siteid' => 'ALTER TABLE "mshop_order_base_service" DROP FOREIGN KEY "fk_msordbase_siteid"',
		),
		'mshop_order_base_service_attr' => array(
			'fk_msordbaseat_siteid' => 'ALTER TABLE "mshop_order_base_service_attr" DROP FOREIGN KEY "fk_msordbaseat_siteid"',
		),
		'mshop_order_status' => array(
			'fk_msordst_siteid' => 'ALTER TABLE "mshop_order_status" DROP FOREIGN KEY "fk_msordst_siteid"',
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderAlterForeignKeyContraintsOnDelete', 'OrderRenameTables' );
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
	 * Delete foreign keys to locale domain in mshop order tables.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Delete foreign keys to locale domain in mshop order tables', 0 );
		$this->_status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			$this->_msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->_schema->tableExists( $table ) === true)
			{
				$this->_status( '' );

				foreach( $stmtList AS $constraint => $stmt )
				{
					$this->_msg( sprintf( 'Checking constraint "%1$s": ', $constraint ), 2 );

					if ( $this->_schema->constraintExists( $table, $constraint ) === true )
					{
						$this->_execute( $stmt );
						$this->_status( 'deleted' );
					}
					else
					{
						$this->_status( 'OK' );
					}
				}
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
	}
}
