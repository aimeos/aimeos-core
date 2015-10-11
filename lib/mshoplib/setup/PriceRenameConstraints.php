<?php

/**
 * @copyright Metaways Infosystems GmbH
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Renames constraints for price tables.
 */
class PriceRenameConstraints extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_price' => array(
			'fk_mspr_siteid' => '
				ALTER TABLE "mshop_price" DROP FOREIGN KEY "fk_mspr_siteid",
				ADD CONSTRAINT "fk_mspri_siteid" FOREIGN KEY ("siteid")
				REFERENCES "mshop_locale_site" ("id")
				ON DELETE CASCADE ON UPDATE CASCADE
			',
			'fk_mspr_typeid' => '
				ALTER TABLE "mshop_price" DROP FOREIGN KEY "fk_mspr_typeid",
				ADD CONSTRAINT "fk_mspri_typeid" FOREIGN KEY ("typeid")
				REFERENCES "mshop_price_type" ("id")
				ON DELETE CASCADE ON UPDATE CASCADE
			',
			'fk_mspr_curid' => '
				ALTER TABLE "mshop_price" DROP FOREIGN KEY "fk_mspr_curid",
				ADD CONSTRAINT "fk_mspri_curid" FOREIGN KEY ("currencyid")
				REFERENCES "mshop_locale_currency" ("id")
				ON DELETE CASCADE ON UPDATE CASCADE
			',
		),
		'mshop_price_type' => array(
			'fk_msprty_siteid' => '
				ALTER TABLE "mshop_price_type" DROP FOREIGN KEY "fk_msprty_siteid",
				ADD CONSTRAINT "fk_msprity_siteid" FOREIGN KEY ("siteid")
				REFERENCES "mshop_locale_site" ("id")
				ON DELETE CASCADE ON UPDATE CASCADE
			',
			'unq_msprty_sid_dom_code' => '
				ALTER TABLE "mshop_price_type" DROP INDEX "unq_msprty_sid_dom_code",
				ADD CONSTRAINT "unq_msprity_sid_dom_code" UNIQUE ("siteid", "domain", "code")
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
		return array( '' );
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
	 * Renames price constraints if necessary.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Renaming price constraints', 0 ); $this->status( '' );

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
