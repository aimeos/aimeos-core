<?php
/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id$
 */

/**
 * Adds parentid column to catalog and locale site table.
 */
class MW_Setup_Task_TreeAddParentId extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_catalog' => array(
			'ALTER TABLE "mshop_catalog" ADD "parentid" INTEGER NOT NULL AFTER "id"',
			'UPDATE mshop_catalog mc1 SET parentid = (
				SELECT id FROM
					( SELECT * FROM mshop_catalog )
				AS mc2
				WHERE
					mc2.siteid = mc1.siteid AND
					mc2.nleft < mc1.nleft AND
					mc2.nright > mc1.nright AND
					mc2.level = mc1.level - 1
				LIMIT 1
			) WHERE parentid = 0'
		),

		'mshop_locale_site' => array(
			'ALTER TABLE "mshop_locale_site" ADD "parentid" INTEGER NOT NULL AFTER "id"',
			'UPDATE mshop_locale_site ml1 SET parentid = (
				SELECT id FROM
					( SELECT * FROM mshop_locale_site )
				AS ml2
				WHERE
					ml2.nleft < ml1.nleft AND
					ml2.nright > ml1.nright AND
					ml2.level = ml1.level - 1
				LIMIT 1
			) WHERE parentid = 0'
		)
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'CatalogTreeToCatalog' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
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
	 * Add column to table if it doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( $stmts )
	{
		$this->_msg( 'Adding parentid column to catalog and locale_site', 0 );

		foreach( $this->_mysql as $table => $stmt )
		{
			$msg = sprintf( 'Checking parentid column in "%1$s"', $table );
			$this->_msg( $msg, 1 );
			if( $this->_schema->tableExists( $table ) === true	&& $this->_schema->columnExists( $table, 'parentid' ) === false )
			{
				$this->_executeList( $stmt );
				$this->_status( 'added' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
	}
}