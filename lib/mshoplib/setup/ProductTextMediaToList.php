<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Moves product text and media references to product list table.
 */
class MW_Setup_Task_ProductTextMediaToList extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_product_text' => array(
			'INSERT INTO "mshop_product_list"("parentid", "siteid", "domain", "refid", "pos") SELECT "prodid", "siteid", \'text\', "textid", "pos" FROM "mshop_product_text"',
			'DROP TABLE "mshop_product_text"',
		),
		'mshop_product_media' => array(
			'INSERT INTO "mshop_product_list"("parentid", "siteid", "domain", "refid", "pos") SELECT "prodid", "siteid", \'media\', "mediaid", "pos" FROM "mshop_product_media"',
			'DROP TABLE "mshop_product_media"',
		)
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'TablesCreateMShop' );
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
	 * Migrate product text and media data into the mshop_product_list table.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Migrating product text and media tables to list', 0 ); $this->_status( '' );

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
