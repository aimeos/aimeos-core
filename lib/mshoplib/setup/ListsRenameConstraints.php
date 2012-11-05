<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: ListsRenameConstraints.php 14251 2011-12-09 13:36:27Z nsendetzky $
 */


/**
 * Adds typeid to list constraints.
 */
class MW_Setup_Task_ListsRenameConstraints extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_attribute_list' => array(
			'unq_msattli_pid_sid_dom_rid' => 'ALTER TABLE "mshop_attribute_list" DROP INDEX "unq_msattli_pid_sid_dom_rid", ADD CONSTRAINT "unq_msattli_pid_sid_tid_rid_dm" UNIQUE ("parentid", "siteid", "typeid", "refid", "domain")',
		),
		'mshop_catalog_list' => array(
			'unq_mscatli_pid_sid_dom_rid' => 'ALTER TABLE "mshop_catalog_list" DROP INDEX "unq_mscatli_pid_sid_dom_rid", ADD CONSTRAINT "unq_mscatli_pid_sid_tid_rid_dm" UNIQUE ("parentid", "siteid", "typeid", "refid", "domain")',
		),
		'mshop_media_list' => array(
			'unq_msmedli_pid_sid_dom_rid' => 'ALTER TABLE "mshop_media_list" DROP INDEX "unq_msmedli_pid_sid_dom_rid", ADD CONSTRAINT "unq_msmedli_pid_sid_tid_rid_dm" UNIQUE ("parentid", "siteid", "typeid", "refid", "domain")',
		),
		'mshop_product_list' => array(
			'unq_msproli_pid_sid_dom_ref' => 'ALTER TABLE "mshop_product_list" DROP INDEX "unq_msproli_pid_sid_dom_ref", ADD CONSTRAINT "unq_msproli_pid_sid_tid_rid_dm" UNIQUE ("parentid", "siteid", "typeid", "refid", "domain")',
		),
		'mshop_service_list' => array(
			'unq_msserli_aid_sid_dom_rid' => 'ALTER TABLE "mshop_service_list" DROP INDEX "unq_msserli_aid_sid_dom_rid", ADD CONSTRAINT "unq_msproli_pid_sid_tid_rid_dm" UNIQUE ("parentid", "siteid", "typeid", "refid", "domain")',
		)
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
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array('TablesCreateMShop');
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
		$this->_msg( 'Renaming list constraints', 0 ); $this->_status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			foreach ( $stmtList as $constraint=>$stmt )
			{
				$this->_msg( sprintf( 'Checking constraint "%1$s": ', $constraint ), 1 );

				if( $this->_schema->constraintExists( $table, $constraint ) )
				{
					$this->_execute( $stmt );
					$this->_status( 'renamed' );
				} else {
					$this->_status( 'OK' );
				}
			}
		}
	}
}
