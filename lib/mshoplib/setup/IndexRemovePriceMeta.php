<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Removes meta data from index price table
 */
class IndexRemovePriceMeta extends \Aimeos\MW\Setup\Task\Base
{
	private $clear = 'DELETE FROM "mshop_index_price"';
	private $indexes = [
		'unq_msindpr_p_s_prid_lt' => 'ALTER TABLE "mshop_index_price" DROP INDEX "unq_msindpr_p_s_prid_lt"',
		'idx_msindpr_s_lt_cu_ty_va' => 'ALTER TABLE "mshop_index_price" DROP INDEX "idx_msindpr_s_lt_cu_ty_va"',
		'idx_msindpr_p_s_lt_cu_ty_va' => 'ALTER TABLE "mshop_index_price" DROP INDEX "idx_msindpr_p_s_lt_cu_ty_va"',
	];
	private $columns = [
		'priceid' => 'ALTER TABLE "mshop_index_price" DROP "priceid"',
		'listtype' => 'ALTER TABLE "mshop_index_price" DROP "listtype"',
		'type' => 'ALTER TABLE "mshop_index_price" DROP "type"',
		'costs' => 'ALTER TABLE "mshop_index_price" DROP "costs"',
		'rebate' => 'ALTER TABLE "mshop_index_price" DROP "rebate"',
		'taxrate' => 'ALTER TABLE "mshop_index_price" DROP "taxrate"',
		'quantity' => 'ALTER TABLE "mshop_index_price" DROP "quantity"',
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
		$this->msg( 'Remove meta columns from mshop_index_price table', 0 ); $this->status( '' );
		$schema = $this->getSchema( 'db-product' );

		foreach( $this->indexes as $index => $stmt )
		{
			$this->msg( sprintf( 'Checking index "%1$s": ', $index ), 1 );

			if( $schema->tableExists( 'mshop_index_price' ) === true
				&& $schema->indexExists( 'mshop_index_price', $index ) === true )
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

			if( $schema->tableExists( 'mshop_index_price' ) === true
				&& $schema->columnExists( 'mshop_index_price', $column ) === true )
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