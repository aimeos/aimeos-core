<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Changes length of langid columns.
 */
class MW_Setup_Task_ColumnLangidChangeLength extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_customer_address' => array(
			'langid' => array(
				'ALTER TABLE "mshop_customer_address" CHANGE "langid" "langid" VARCHAR(5) NOT NULL',
			),
		),
		'mshop_locale' => array(
			'langid' => array(
				'ALTER TABLE "mshop_locale" CHANGE "langid" "langid" VARCHAR(5) NOT NULL',
			),
		),
		'mshop_media' => array(
			'langid' => array(
				'ALTER TABLE "mshop_media" CHANGE "langid" "langid" VARCHAR(5) NULL',
			),
		),
		'mshop_order_base' => array(
			'langid' => array(
				'ALTER TABLE "mshop_order_base" CHANGE "langid" "langid" VARCHAR(5) NOT NULL',
			),
		),
		'mshop_order_base_address' => array(
			'langid' => array(
				'ALTER TABLE "mshop_order_base_address" CHANGE "langid" "langid" VARCHAR(5) NOT NULL',
			),
		),
		'mshop_product_tag' => array(
			'langid' => array(
				'ALTER TABLE "mshop_product_tag" CHANGE "langid" "langid" VARCHAR(5) NULL',
			),
		),
		'mshop_supplier_address' => array(
			'langid' => array(
				'ALTER TABLE "mshop_supplier_address" CHANGE "langid" "langid" VARCHAR(5) NOT NULL',
			),
		),
		'mshop_text' => array(
			'langid' => array(
				'ALTER TABLE "mshop_text" CHANGE "langid" "langid" VARCHAR(5) NULL',
			),
		),
		'mshop_locale_language' => array(
			'id' => array(
				'ALTER TABLE "mshop_locale_language" CHANGE "id" "id" VARCHAR(5) NOT NULL',
			),
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderAddProductidAndLangid', 'ProductTagLangidNull', 'TextChangeLangidNull' );
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
	 * Migrates service text data to list table.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Changing "langid" & "mshop_locale_language.id" columns', 0 ); $this->_status( '' );

		foreach( $stmts as $table=>$columns )
		{
			$this->_msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			foreach( $columns AS $column=>$stmtList )
			{
				if( $this->_schema->tableExists( $table ) === true
					&& $this->_schema->columnExists( $table, $column ) === true
					&& $this->_schema->getColumnDetails( $table, $column )->getDataType() == "char"
					&& $this->_schema->getColumnDetails( $table, $column )->getMaxLength() != 5 )
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
}
