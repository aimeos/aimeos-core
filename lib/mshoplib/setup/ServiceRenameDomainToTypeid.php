<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Renames domain column to typeid and updates records.
 */
class ServiceRenameDomainToTypeid extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_service' => array(
			'UPDATE "mshop_service" AS ms, "mshop_service_type" AS mstype SET ms."domain" = mstype."id" WHERE ms."domain" = mstype."code"',
			'ALTER TABLE "mshop_service" CHANGE "domain" "typeid" INTEGER NOT NULL',
			'ALTER TABLE "mshop_service" ADD CONSTRAINT "fk_mstyp_typeid" FOREIGN KEY ("typeid")
				REFERENCES "mshop_service_type" ("id") ON DELETE CASCADE ON UPDATE CASCADE',
			'ALTER TABLE "mshop_service" DROP INDEX "unq_msser_siteid_domain_code", ADD CONSTRAINT "unq_msser_siteid_typeid_code" UNIQUE ("siteid", "typeid", "code")'
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopAddTypeData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return [];
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function mysql()
	{
		$this->process( $this->mysql );
	}


	/**
	 * Renames all order tables if they exist.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Renaming service domain', 0 ); $this->status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->schema->tableExists( $table ) && $this->schema->columnExists( $table, 'domain' ) === true )
			{
				$this->executeList( $stmtList );
				$this->status( 'renamed' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}

}
