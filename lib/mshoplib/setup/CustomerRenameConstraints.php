<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

/**
 * Renames the foreign key constraints for customer tables to match style guidlines.
 */
class MW_Setup_Task_CustomerRenameConstraints extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_customer' => array(
			'fk_mscust_siteid' => '
				ALTER TABLE "mshop_customer" DROP FOREIGN KEY "fk_mscust_siteid",
				ADD CONSTRAINT "fk_mscus_siteid" FOREIGN KEY ("siteid")
				REFERENCES "mshop_locale_site" ("id")
				ON DELETE CASCADE ON UPDATE CASCADE
			',
			'fk_mscust_langid' => '
				ALTER TABLE "mshop_customer" DROP FOREIGN KEY "fk_mscust_langid",
				ADD CONSTRAINT "fk_mscus_langid" FOREIGN KEY ("langid")
				REFERENCES "mshop_locale_language" ("id")
				ON DELETE CASCADE ON UPDATE CASCADE
			',
		),
		'mshop_customer_address' => array(
			'fk_mscustaddr_siteid' => '
				ALTER TABLE "mshop_customer_address" DROP FOREIGN KEY "fk_mscustaddr_siteid",
				ADD CONSTRAINT "fk_mscusad_siteid" FOREIGN KEY ("siteid")
				REFERENCES "mshop_locale_site" ("id")
				ON DELETE CASCADE ON UPDATE CASCADE
			',
			'fk_mscustaddr_refid' => '
				ALTER TABLE "mshop_customer_address" DROP FOREIGN KEY "fk_mscustaddr_refid",
				ADD CONSTRAINT "fk_mscusad_refid" FOREIGN KEY ("refid")
				REFERENCES "mshop_customer" ("id")
				ON DELETE CASCADE ON UPDATE CASCADE
			',
			'fk_mscustaddr_langid' => '
				ALTER TABLE "mshop_customer_address" DROP FOREIGN KEY "fk_mscustaddr_langid",
				ADD CONSTRAINT "fk_mscusad_langid" FOREIGN KEY ("langid")
				REFERENCES "mshop_locale_language" ("id")
				ON DELETE CASCADE ON UPDATE CASCADE
			',
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'SubjectToCustomerSupplier' );
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
	 * Renames customer constraints if necessary.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Renaming customer constraints', 0 ); $this->_status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			foreach( $stmtList as $constraint=>$stmt )
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
