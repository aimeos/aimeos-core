<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Changes action on delete on mshop_locale_site FOREIGN KEY CONSTRAINTS for mshop_locale tables.
 */
class LocaleAlterForeignKeyContraintsOnDelete extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_locale_currency' => array(
			'fk_msloccu_siteid' => array(
				'column' =>	'ALTER TABLE "mshop_locale_currency" CHANGE COLUMN "siteid" "siteid" INTEGER NULL',
				'drop' => 'ALTER TABLE "mshop_locale_currency" DROP FOREIGN KEY "fk_msloccu_siteid"',
				'add' => '
					ALTER TABLE "mshop_locale_currency" ADD CONSTRAINT "fk_msloccu_siteid" FOREIGN KEY ("siteid")
					REFERENCES "mshop_locale_site" ("id") ON DELETE SET NULL ON UPDATE CASCADE
				',
			),
		),
		'mshop_locale_language' => array(
			'fk_mslocla_siteid' => array(
				'column' =>	'ALTER TABLE "mshop_locale_language" CHANGE COLUMN "siteid" "siteid" INTEGER NULL',
				'drop' => 'ALTER TABLE "mshop_locale_language" DROP FOREIGN KEY "fk_mslocla_siteid"',
				'add' => '
					ALTER TABLE "mshop_locale_language" ADD CONSTRAINT "fk_mslocla_siteid" FOREIGN KEY ("siteid")
					REFERENCES "mshop_locale_site" ("id") ON DELETE SET NULL ON UPDATE CASCADE
				',
			),
		),
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
	 * Changes CONSTRAINT action ON DELETE for locale tables.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Change locale siteid foreign key constraints', 0 ); $this->status( '' );

		foreach( $stmts as $table => $stmtLists )
		{
			foreach( $stmtLists as $constraint=>$stmtList )
			{
				$this->msg( sprintf( 'Checking constraint "%1$s": ', $constraint ), 1 );
				if( $this->schema->tableExists( $table ) )
				{
					if( $this->schema->getColumnDetails( $table, 'siteid' )->isNullable() === false )
					{
						$this->execute( $stmtList['column'] );

						if( $this->schema->constraintExists( $table, $constraint ) === true ) {
							$this->execute( $stmtList['drop'] );
						}
					}

					if( $this->schema->constraintExists( $table, $constraint ) !== true )
					{
						$this->execute( $stmtList['add'] );
						$this->status( 'changed' );
					}
					else
					{
						$this->status( 'OK' );
					}
				}
				else
				{
					$this->status( 'OK' );
				}
			}
		}
	}
}
