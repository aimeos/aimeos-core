<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Removing siteid from PRIMARY KEY on locale tables.
 */
class LocaleChangePrimary extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_locale_currency' => '
			ALTER TABLE "mshop_locale_currency" DROP PRIMARY KEY,
			ADD CONSTRAINT "pk_msloccu_id" PRIMARY KEY ("id")
		',
		'mshop_locale_language' => '
			ALTER TABLE "mshop_locale_language" DROP PRIMARY KEY,
			ADD CONSTRAINT "pk_mslocla_id" PRIMARY KEY ("id")
		',
	);



	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'LocaleAddSiteidToLangAndCurrency' );
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
	 * Change critical PRIMARY KEY-constellation in locale tables.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Changing PRIMARY KEYS for locale', 0 ); $this->status( '' );

		$search = '
			SELECT COUNT(INDEX_NAME) "counter" FROM
			INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE()
			AND TABLE_NAME = \'%1$s\' AND INDEX_NAME = \'PRIMARY\'
			AND COLUMN_NAME IN(\'id\', \'siteid\')
		';

		foreach( $stmts as $table => $stmt )
		{
			$this->msg( sprintf( 'Checking table "%1$s" for PRIMARY": ', $table ), 1 );
			$counter = $this->getValue( sprintf( $search, $table ), 'counter' );

			if( $counter == 2 )
			{
				$this->execute( $stmt );
				$this->status( 'changed' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}
}