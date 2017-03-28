<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Renames domain column to typeid in plugin table and migriates data.
 */
class PluginRenameDomainToTypeid extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_plugin' => array(
			'UPDATE "mshop_plugin" AS p, "mshop_plugin_type" AS t
				SET p."domain" = t."id"
				WHERE p."domain" = t."code"',
			'ALTER TABLE "mshop_plugin" CHANGE "domain" "typeid" INTEGER NOT NULL',
			'ALTER TABLE "mshop_plugin"
				ADD CONSTRAINT "unq_mspul_sid_tid_provider" UNIQUE ("siteid", "typeid", "provider")',
			'ALTER TABLE "mshop_plugin" DROP INDEX "idx_msplu_dn_stat"',
			'ALTER TABLE "mshop_plugin"
				ADD CONSTRAINT "fk_msplu_typeid"
					FOREIGN KEY ("typeid")
					REFERENCES "mshop_plugin_type" ("id") ON DELETE CASCADE ON UPDATE CASCADE',
		),
	);

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'TablesCreateMShop', 'MShopAddTypeData' );
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
		$this->msg( 'Renaming plugin domain', 0 ); $this->status( '' );

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
