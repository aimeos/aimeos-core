<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Drops the text index without domain in the catalog index text table
 */
class IndexDropTextValueIndex extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'idx_msindte_p_s_lt_la_ty_va' => 'ALTER TABLE "mshop_index_text" DROP INDEX "idx_msindte_p_s_lt_la_ty_va"',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'CatalogAddIndexTextDomain', 'CatalogIndexRenameDomainIndexes' );
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
	 * Drops the text index without domain in the catalog index text table
	 *
	 * @param array $stmts List of SQL statements to execute
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Drop text index without domain in mshop_index_text table', 0 );
		$this->status( '' );

		foreach( $stmts as $index => $stmt )
		{
			$this->msg( sprintf( 'Checking index "%1$s": ', $index ), 1 );

			if( $this->schema->tableExists( 'mshop_index_text' ) === true
				&& $this->schema->indexExists( 'mshop_index_text', $index ) === true )
			{
				$this->execute( $stmt );
				$this->status( 'done' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}

}