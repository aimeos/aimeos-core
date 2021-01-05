<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Drop the mshop_index_text table if it contains no ID column
 */
class IndexDropTextWithoutId extends \Aimeos\MW\Setup\Task\Base
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
		$this->msg( 'Drop the mshop_index_text table without ID column', 0 );
		$schema = $this->getSchema( 'db-product' );

		if( $schema->tableExists( 'mshop_index_text' ) === true
			&& $schema->columnExists( 'mshop_index_text', 'id' ) === false
		) {
			$this->execute( 'DROP TABLE "mshop_index_text"' );
			$this->status( 'done' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}
