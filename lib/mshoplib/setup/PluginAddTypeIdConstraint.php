<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds constraint for typeid in plugin tag table.
 */
class MW_Setup_Task_PluginAddTypeIdConstraint extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
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
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Adds typeid constraint to mshop_plugin if necessary.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$table = 'mshop_plugin';
		$constraint = 'fk_msplu_typeid';

		$this->_msg( 'Adding constraint for table mshop_plugin', 0 ); $this->_status( '' );

		$this->_msg( sprintf( 'Checking constraint "%1$s": ', $constraint ), 1 );

		if( $this->_schema->tableExists( $table ) === true
			&& $this->_schema->columnExists( $table, 'typeid' )
			&& $this->_schema->constraintExists( $table, $constraint ) === false )
		{
			$this->_execute( $stmts[ $constraint ] );
			$this->_status( 'added' );
		}
		else
		{
			$this->_status( 'OK' );
		}
	}

}
