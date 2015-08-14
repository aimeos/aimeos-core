<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds priceid/textid column to catalog index price/text table.
 */
class MW_Setup_Task_CatalogAddIndexPriceidTextid extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_catalog_index_price.priceid' => array(
			'TRUNCATE TABLE "mshop_catalog_index_price"',
			'ALTER TABLE "mshop_catalog_index_price" ADD "priceid" INTEGER NULL AFTER "siteid"',
			'ALTER TABLE "mshop_catalog_index_price" ADD FOREIGN KEY ("priceid") REFERENCES "mshop_price" ("id") ON DELETE CASCADE ON UPDATE CASCADE',
		),
		'mshop_catalog_index_text.textid' => array(
			'TRUNCATE TABLE "mshop_catalog_index_text"',
			'ALTER TABLE "mshop_catalog_index_text" ADD "textid" INTEGER NULL AFTER "siteid"',
			'ALTER TABLE "mshop_catalog_index_text" ADD FOREIGN KEY ("textid") REFERENCES "mshop_text" ("id") ON DELETE CASCADE ON UPDATE CASCADE',
		),
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
	 * Add column to table if it doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( $stmts )
	{
		$this->_msg( 'Adding reference ID columns to catalog index tables', 0 );
		$this->_status( '' );

		foreach( $stmts as $id => $sql )
		{
			$parts = explode( '.', $id );
			$this->_msg( sprintf( 'Checking table "%1$s" for column "%2$s"', $parts[0], $parts[1] ), 1 );

			if( $this->_schema->tableExists( $parts[0] ) === true
				&& $this->_schema->columnExists( $parts[0], $parts[1] ) === false )
			{
				$this->_executeList( $sql );
				$this->_status( 'added' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
	}
}