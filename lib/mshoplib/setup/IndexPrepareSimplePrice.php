<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Prepares the mshop_index_price table for simplification
 */
class IndexPrepareSimplePrice extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies() : array
	{
		return ['TablesCreateMShop'];
	}


	/**
	 * Executes the task
	 */
	public function migrate()
	{
		$this->msg( 'Prepare mshop_index_price table for simplification', 0 );
		$schema = $this->getSchema( 'db-product' );

		if( $schema->tableExists( 'mshop_index_price' ) === true
			&& $schema->constraintExists( 'mshop_index_price', 'unq_msindpr_p_s_prid_lt' ) === true
		) {
			$this->execute( 'DELETE FROM "mshop_index_price"' );
			$this->status( 'done' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}
