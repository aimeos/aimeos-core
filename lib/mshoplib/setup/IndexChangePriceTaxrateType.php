<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Migrate the database schema
 */
class IndexChangePriceTaxrateType extends \Aimeos\MW\Setup\Task\Base
{
	private $list = array(
		'mysql' => 'ALTER TABLE "mshop_index_price" MODIFY "taxrate" DECIMAL(5,2) NOT NULL',
		'pgsql' => 'ALTER TABLE "mshop_index_price" ALTER COLUMN "taxrate" TYPE DECIMAL(5,2)',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array();
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'TablesCreateMShop' );
	}


	/**
	 * Update database schema
	 */
	public function migrate()
	{
		$this->msg( 'Changing length of "mshop_index_price.taxrate"', 0 );

		$schema = $this->getSchema( 'db-product' );

		if( isset( $this->list[$schema->getName()] )
			&& $schema->tableExists( 'mshop_index_price' ) === true
			&& $schema->columnExists( 'mshop_index_price', 'taxrate' ) === true
			&& $schema->getColumnDetails( 'mshop_index_price', 'taxrate' )->getMaxLength() > 5 )
		{
			$this->execute( $this->list[$schema->getName()] );
			$this->status( 'done' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}
