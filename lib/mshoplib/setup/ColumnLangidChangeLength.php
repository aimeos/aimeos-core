<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Changes length of langid columns.
 */
class ColumnLangidChangeLength extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
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
	protected function mysql()
	{
		$this->process( $this->mysql );
	}


	/**
	 * Migrates service text data to list table.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Changing "langid" & "mshop_locale_language.id" columns', 0 ); $this->status( '' );

		foreach( $stmts as $table=>$columns )
		{
			$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			foreach( $columns as $column=>$stmtList )
			{
				if( $this->schema->tableExists( $table ) === true
					&& $this->schema->columnExists( $table, $column ) === true
					&& $this->schema->getColumnDetails( $table, $column )->getDataType() == "char"
					&& $this->schema->getColumnDetails( $table, $column )->getMaxLength() != 5 )
				{
					$this->executeList( $stmtList );
					$this->status( 'migrated' );
				}
				else
				{
					$this->status( 'OK' );
				}
			}
		}
	}
}
