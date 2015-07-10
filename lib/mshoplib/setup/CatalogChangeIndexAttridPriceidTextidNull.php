<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Changes the attrid/priceid/textid column to allow NULL in catalog index attr/price/text table.
 */
class MW_Setup_Task_CatalogChangeIndexAttridPriceidTextidNull extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_catalog_index_attribute' => array(
			'attrid' => 'ALTER TABLE "mshop_catalog_index_attribute" MODIFY "attrid" INTEGER NULL',
		),
		'mshop_catalog_index_price' => array(
			'priceid' => 'ALTER TABLE "mshop_catalog_index_price" MODIFY "priceid" INTEGER NULL',
		),
		'mshop_catalog_index_text' => array(
			'textid' => 'ALTER TABLE "mshop_catalog_index_text" MODIFY "textid" INTEGER NULL',
		),
	);

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'CatalogAddIndexPriceidTextid' );
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
		$this->_msg( 'Changing reference ID columns of catalog index tables to NULL', 0 );
		$this->_status( '' );

		foreach( $stmts as $table => $pairs )
		{
			foreach( $pairs as $column => $sql )
			{
				$this->_msg( sprintf( 'Checking table "%1$s" and column "%2$s"', $table, $column ), 1 );

				if( $this->_schema->tableExists( $table ) === true
					&& $this->_schema->columnExists( $table, $column ) === true
					&& $this->_schema->getColumnDetails( $table, $column )->isNullable() === false )
				{
					$this->_execute( $sql );
					$this->_status( 'changed' );
				}
				else
				{
					$this->_status( 'OK' );
				}
			}
		}
	}
}
