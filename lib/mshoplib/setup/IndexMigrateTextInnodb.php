<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Migrates the mshop_index_text table engine to InnoDB
 */
class IndexMigrateTextInnodb extends \Aimeos\MW\Setup\Task\Base
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
		$this->msg( 'Migrate mshop_index_text table engine to InnoDB', 0 );

		$schema = $this->getSchema( 'db-product' );
		$sql = sprintf( '
			SELECT ENGINE FROM INFORMATION_SCHEMA.TABLES
			WHERE TABLE_SCHEMA = \'%1$s\' AND TABLE_NAME = \'mshop_index_text\'
		', $schema->getDBName() );

		if( $schema->getName() === 'mysql' && $schema->tableExists( 'mshop_index_text' )
			&& $this->getValue( $sql, 'ENGINE', 'db-index' ) === 'MyISAM'
		) {
			$this->execute( 'ALTER TABLE "mshop_index_text" ENGINE=InnoDB' );
			$this->status( 'done' );
			return;
		}

		$this->status( 'OK' );
	}
}
