<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Moves product text and media references to product list table.
 */
class ProductTextMediaToList extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
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
		return [];
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function mysql()
	{
		$this->process( $this->mysql );
	}


	/**
	 * Migrate product text and media data into the mshop_product_list table.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Migrating product text and media tables to list', 0 ); $this->status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->schema->tableExists( $table ) === true )
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
