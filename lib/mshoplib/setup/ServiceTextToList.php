<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: ServiceTextToList.php 14251 2011-12-09 13:36:27Z nsendetzky $
 */


/**
 * Moves service text references to service list.
 */
class MW_Setup_Task_ServiceTextToList extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_service_text' => array(
			'INSERT INTO "mshop_service_list" ("parentid", "siteid", "domain", "refid", "pos")
				SELECT "serviceid", "siteid", \'text\', "textid", "pos" FROM "mshop_service_text"
			',
			'DROP TABLE "mshop_service_text"',
		),
	);

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array('TablesCreateMShop');
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array();
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Migrates service text data to list table.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Migrating service text table', 0 ); $this->_status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			$this->_msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->_schema->tableExists( $table ) === true )
			{
				$this->_executeList( $stmtList );
				$this->_status( 'migrated' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
	}
}
