<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Changes product typeid column to NOT NULL.
 */
class ProductChangeTypeidNotNull extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'UPDATE "mshop_product"
			SET "typeid" = ( SELECT "id" FROM "mshop_product_type" WHERE "siteid" IS NULL AND "domain" = \'product\' AND "code" = \'product\' )
			WHERE "typeid" IS NULL',
		'ALTER TABLE "mshop_product" CHANGE "typeid" "typeid" INTEGER NOT NULL',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'ProductAddTypeid' );
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
	 * Add column to table if it doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( $stmts )
	{
		$this->msg( 'Changing typeid column of product table', 0 ); $this->status( '' );

		$this->msg( sprintf( 'Checking table "%1$s": ', 'mshop_product' ), 1 );

		if( $this->schema->tableExists( 'mshop_product' ) === true
			&& $this->schema->columnExists( 'mshop_product', 'typeid' ) === true
			&& $this->schema->getColumnDetails( 'mshop_product', 'typeid' )->isNullable() === true )
		{
			$this->executeList( $stmts );
			$this->status( 'migrated' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}