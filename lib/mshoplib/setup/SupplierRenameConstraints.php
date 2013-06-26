<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Renames the foreign key constraints for supplier tables to match style guidlines.
 */
class MW_Setup_Task_SupplierRenameConstraints extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_supplier' => array(
			'fk_mssupp_siteid' => '
				ALTER TABLE "mshop_supplier" DROP FOREIGN KEY "fk_mssupp_siteid",
				ADD CONSTRAINT "fk_mssup_siteid" FOREIGN KEY ("siteid")
				REFERENCES "mshop_locale_site" ("id")
				ON DELETE CASCADE ON UPDATE CASCADE
			',
		),
		'mshop_supplier_address' => array(
			'fk_mssuppaddr_refid' => '
				ALTER TABLE "mshop_supplier_address" DROP FOREIGN KEY "fk_mssuppaddr_refid",
				ADD CONSTRAINT "fk_mssupad_refid" FOREIGN KEY ("refid")
				REFERENCES "mshop_supplier" ("id")
				ON DELETE CASCADE ON UPDATE CASCADE
			',
			'fk_mssuppaddr_siteid' => '
				ALTER TABLE "mshop_supplier_address" DROP FOREIGN KEY "fk_mssuppaddr_siteid",
				ADD CONSTRAINT "fk_mssupad_siteid" FOREIGN KEY ("siteid")
				REFERENCES "mshop_locale_site" ("id")
				ON DELETE CASCADE ON UPDATE CASCADE
			',
			'fk_mssuppaddr_langid' => '
				ALTER TABLE "mshop_supplier_address" DROP FOREIGN KEY "fk_mssuppaddr_langid",
				ADD CONSTRAINT "fk_mssupad_langid" FOREIGN KEY ("langid")
				REFERENCES "mshop_locale_language" ("id")
				ON DELETE CASCADE ON UPDATE CASCADE
			',
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array('SubjectToCustomerSupplier');
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
	 * Renames supplier constraints if necessary.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Renaming supplier constraints', 0 ); $this->_status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			foreach ( $stmtList as $constraint=>$stmt )
			{
				$this->_msg( sprintf( 'Checking constraint "%1$s": ', $constraint ), 1 );

				if( $this->_schema->tableExists( $table ) && $this->_schema->constraintExists( $table, $constraint ) )
				{
					$this->_execute( $stmt );
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
