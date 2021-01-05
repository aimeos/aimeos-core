<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Removes char constraints from locale table before migrating to varchar
 */
class LocaleRemoveCharConstraints extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
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
		$schema = $this->getSchema( 'db-locale' );

		$this->msg( sprintf( 'Remove mshop_locale char constraints' ), 0 );
		$this->status( '' );

		$this->msg( 'Checking constraint for "langid"', 1 );

		if( $schema->constraintExists( 'mshop_locale', 'fk_msloc_langid' )
			&& $schema->getColumnDetails( 'mshop_locale', 'langid' )->getDataType() === 'char'
		) {
			$this->execute( 'ALTER TABLE "mshop_locale" DROP FOREIGN KEY "fk_msloc_langid"', 'db-locale' );
			$this->status( 'done' );
		}
		else
		{
			$this->status( 'OK' );
		}

		$this->msg( 'Checking constraint for "currencyid"', 1 );

		if( $schema->constraintExists( 'mshop_locale', 'fk_msloc_currid' )
			&& $schema->getColumnDetails( 'mshop_locale', 'currencyid' )->getDataType() === 'char'
		) {
			$this->execute( 'ALTER TABLE "mshop_locale" DROP FOREIGN KEY "fk_msloc_currid"', 'db-locale' );
			$this->status( 'done' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}
