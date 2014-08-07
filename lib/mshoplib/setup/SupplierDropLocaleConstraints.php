<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Removes locale constraints from supplier tables.
 */
class MW_Setup_Task_SupplierDropLocaleConstraints extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_supplier_address' => array(
			'fk_mssupad_siteid' => 'ALTER TABLE "mshop_supplier_address" DROP FOREIGN KEY "fk_mssupad_siteid"',
			'fk_mssupad_langid' => 'ALTER TABLE "mshop_supplier_address" DROP FOREIGN KEY "fk_mssupad_langid"',
		),
		'mshop_supplier' => array(
			'fk_mssup_siteid' => 'ALTER TABLE "mshop_supplier" DROP FOREIGN KEY "fk_mssup_siteid"',
		),
	);




	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'SupplierRenameConstraints' );
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
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Drops local constraints.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Removing locale constraints from supplier tables', 0 );
		$this->_status( '' );

		$schema = $this->_getSchema( 'db-supplier' );

		foreach( $stmts as $table => $list )
		{
			if( $schema->tableExists( $table ) === true )
			{
				foreach( $list as $constraint => $stmt )
				{
					$this->_msg( sprintf( 'Removing "%1$s" from "%2$s"', $constraint, $table ), 1 );

					if( $schema->constraintExists( $table, $constraint ) !== false )
					{
						$this->_execute( $stmt, 'db-supplier' );
						$this->_status( 'done' );
					} else {
						$this->_status( 'OK' );
					}
				}
			}
		}
	}
}