<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Prepares the mshop_index_text table for simplification
 */
class IndexPrepareSimpleText extends \Aimeos\MW\Setup\Task\Base
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
		$this->msg( 'Prepare mshop_index_text table for simplification', 0 );
		$schema = $this->getSchema( 'db-product' );

		if( $schema->tableExists( 'mshop_index_text' ) === true
			&& $schema->constraintExists( 'mshop_index_text', 'unq_msindte_p_s_tid_lt' ) === true
		) {
			$this->execute( 'DELETE FROM "mshop_index_text"' );
			$this->status( 'done' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}
