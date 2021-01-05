<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Remove ctime and editor columns from index tables
 */
class IndexRemoveCtimeEditor extends \Aimeos\MW\Setup\Task\Base
{
	private $sql = [
		'mshop_index_attribute' => [
			'ALTER TABLE "mshop_index_attribute" DROP "ctime"',
			'ALTER TABLE "mshop_index_attribute" DROP "editor"',
		],
		'mshop_index_catalog' => [
			'ALTER TABLE "mshop_index_catalog" DROP "ctime"',
			'ALTER TABLE "mshop_index_catalog" DROP "editor"',
		],
		'mshop_index_price' => [
			'ALTER TABLE "mshop_index_price" DROP "ctime"',
			'ALTER TABLE "mshop_index_price" DROP "editor"',
		],
		'mshop_index_supplier' => [
			'ALTER TABLE "mshop_index_supplier" DROP "ctime"',
			'ALTER TABLE "mshop_index_supplier" DROP "editor"',
		],
		'mshop_index_text' => [
			'ALTER TABLE "mshop_index_text" DROP "ctime"',
			'ALTER TABLE "mshop_index_text" DROP "editor"',
		],
	];


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['TablesCreateMShop'];
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies() : array
	{
		return ['MShopSetLocale'];
	}


	/**
	 * Executes the task
	 */
	public function migrate()
	{
		$this->msg( 'Remove ctime/editor from index tables', 0 ); $this->status( '' );
		$schema = $this->getSchema( 'db-product' );

		foreach( $this->sql as $table => $stmtList )
		{
			$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $schema->tableExists( $table ) === true
				&& $schema->columnExists( $table, 'ctime' ) === true
				&& $schema->columnExists( $table, 'editor' ) === true )
			{
				$this->executeList( $stmtList );
				$this->status( 'done' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}
}
