<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Renames the foreign key constraints for supplier tables to match style guidlines.
 */
class SupplierRenameConstraints extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
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
	protected function mysql()
	{
		$this->process( $this->mysql );
	}


	/**
	 * Renames supplier constraints if necessary.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Renaming supplier constraints', 0 ); $this->status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			foreach( $stmtList as $constraint=>$stmt )
			{
				$this->msg( sprintf( 'Checking constraint "%1$s": ', $constraint ), 1 );

				if( $this->schema->tableExists( $table ) && $this->schema->constraintExists( $table, $constraint ) )
				{
					$this->execute( $stmt );
					$this->status( 'changed' );
				}
				else
				{
					$this->status( 'OK' );
				}
			}
		}
	}
}
