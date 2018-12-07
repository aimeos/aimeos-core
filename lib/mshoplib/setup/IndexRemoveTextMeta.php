<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Removes meta data from index text table
 */
class IndexRemoveTextMeta extends \Aimeos\MW\Setup\Task\Base
{
	private $clear = 'DELETE FROM "mshop_index_text"';
	private $indexes = [
		'idx_msindte_value' => 'ALTER TABLE "mshop_index_text" DROP INDEX "idx_msindte_value"',
		'unq_msindte_p_s_tid_lt' => 'ALTER TABLE "mshop_index_text" DROP INDEX "unq_msindte_p_s_tid_lt"',
		'idx_msindte_p_s_lt_la_ty_do_va' => 'ALTER TABLE "mshop_index_text" DROP INDEX "idx_msindte_p_s_lt_la_ty_do_va"',
	];
	private $columns = [
		'textid' => 'ALTER TABLE "mshop_index_text" DROP "textid"',
		'listtype' => 'ALTER TABLE "mshop_index_text" DROP "listtype"',
		'domain' => 'ALTER TABLE "mshop_index_text" DROP "domain"',
		'type' => 'ALTER TABLE "mshop_index_text" DROP "type"',
	];


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return [];
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return ['TablesCreateMShop'];
	}


	/**
	 * Executes the task
	 */
	public function migrate()
	{
		$this->msg( 'Remove meta columns from mshop_index_text table', 0 ); $this->status( '' );
		$schema = $this->getSchema( 'db-product' );

		foreach( $this->indexes as $index => $stmt )
		{
			$this->msg( sprintf( 'Checking index "%1$s": ', $index ), 1 );

			if( $schema->tableExists( 'mshop_index_text' ) === true
				&& $schema->indexExists( 'mshop_index_text', $index ) === true )
			{
				$this->execute( $this->clear );
				$this->execute( $stmt );
				$this->status( 'done' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}

		foreach( $this->columns as $column => $stmt )
		{
			$this->msg( sprintf( 'Checking column "%1$s": ', $column ), 1 );

			if( $schema->tableExists( 'mshop_index_text' ) === true
				&& $schema->columnExists( 'mshop_index_text', $column ) === true )
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