<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Renames domain column to typeid and updates records.
 */
class MW_Setup_Task_ServiceRenameDomainToTypeid extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
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
		return array('TablesCreateMShop', 'MShopAddTypeData');
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
	 * Renames all order tables if they exist.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Renaming service domain', 0 ); $this->_status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			$this->_msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->_schema->tableExists( $table ) && $this->_schema->columnExists( $table, 'domain' ) === true )
			{
				$this->_executeList( $stmtList );
				$this->_status( 'renamed' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
	}

}
