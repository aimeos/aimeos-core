<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Migrate the database schema
 */
class CustomerChangeCodeLength extends \Aimeos\MW\Setup\Task\Base
{
	private $list = array(
		'mysql' => 'ALTER TABLE "mshop_customer" MODIFY "code" VARCHAR(255) NOT NULL',
		'pgsql' => 'ALTER TABLE "mshop_customer" ALTER COLUMN "code" TYPE VARCHAR(255)',
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
		$this->msg( 'Changing length of "mshop_customer.code"', 0 );

		$schema = $this->getSchema( 'db-customer' );

		if( isset( $this->list[$schema->getName()] )
			&& $schema->tableExists( 'mshop_customer' ) === true
			&& $schema->columnExists( 'mshop_customer', 'code' ) === true
			&& $schema->getColumnDetails( 'mshop_customer', 'code' )->getMaxLength() < 255 )
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
