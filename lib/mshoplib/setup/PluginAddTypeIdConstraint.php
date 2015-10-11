<?php

/**
 * @copyright Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds constraint for typeid in plugin tag table.
 */
class PluginAddTypeIdConstraint extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'fk_msplu_typeid' => 'ALTER TABLE "mshop_plugin" ADD CONSTRAINT "fk_msplu_typeid" FOREIGN KEY ("typeid") REFERENCES "mshop_plugin_type" ("id") ON DELETE CASCADE ON UPDATE CASCADE',
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
	 * Adds typeid constraint to mshop_plugin if necessary.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$table = 'mshop_plugin';
		$constraint = 'fk_msplu_typeid';

		$this->msg( 'Adding constraint for table mshop_plugin', 0 ); $this->status( '' );

		$this->msg( sprintf( 'Checking constraint "%1$s": ', $constraint ), 1 );

		if( $this->schema->tableExists( $table ) === true
			&& $this->schema->columnExists( $table, 'typeid' )
			&& $this->schema->constraintExists( $table, $constraint ) === false )
		{
			$this->execute( $stmts[$constraint] );
			$this->status( 'added' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}

}
