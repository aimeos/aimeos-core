<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Removes locale constraints from service tables.
 */
class MW_Setup_Task_ServiceDropLocaleConstraints extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_service_list_type' => array(
			'fk_msser_siteid' => 'ALTER TABLE "mshop_service_list_type" DROP FOREIGN KEY "fk_msser_siteid"',
		),
		'mshop_service_list' => array(
			'fk_msserli_siteid' => 'ALTER TABLE "mshop_service_list" DROP FOREIGN KEY "fk_msserli_siteid"',
		),
		'mshop_service_type' => array(
			'fk_msserty_siteid' => 'ALTER TABLE "mshop_service_type" DROP FOREIGN KEY "fk_msserty_siteid"',
		),
		'mshop_service' => array(
			'fk_msser_siteid' => 'ALTER TABLE "mshop_service" DROP FOREIGN KEY "fk_msser_siteid"',
		),
	);




	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'AttributeListRenameSiteidConstraints' );
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
	 * Drops local constraints.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Removing locale constraints from service tables', 0 );
		$this->_status( '' );

		$schema = $this->_getSchema( 'db-service' );

		foreach( $stmts as $table => $list )
		{
			if( $schema->tableExists( $table ) === true )
			{
				foreach( $list as $constraint => $stmt )
				{
					$this->_msg( sprintf( 'Removing "%1$s" from "%2$s"', $constraint, $table ), 1 );

					if( $schema->constraintExists( $table, $constraint ) !== false )
					{
						$this->_execute( $stmt, 'db-service' );
						$this->_status( 'done' );
					} else {
						$this->_status( 'OK' );
					}
				}
			}
		}
	}
}