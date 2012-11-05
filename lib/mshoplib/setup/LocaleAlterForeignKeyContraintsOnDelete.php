<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: LocaleAlterForeignKeyContraintsOnDelete.php 14809 2012-01-11 18:08:49Z fblasel $
 */


/**
 * Changes action on delete on mshop_locale_site FOREIGN KEY CONSTRAINTS for mshop_locale tables.
 */
class MW_Setup_Task_LocaleAlterForeignKeyContraintsOnDelete extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_locale_currency' => array (
			'fk_msloccu_siteid' => array(
				'column' =>	'ALTER TABLE "mshop_locale_currency" CHANGE COLUMN "siteid" "siteid" INTEGER NULL',
				'drop' => 'ALTER TABLE "mshop_locale_currency" DROP FOREIGN KEY "fk_msloccu_siteid"',
				'add' => '
					ALTER TABLE "mshop_locale_currency" ADD CONSTRAINT "fk_msloccu_siteid" FOREIGN KEY ("siteid")
					REFERENCES "mshop_locale_site" ("id") ON DELETE SET NULL ON UPDATE CASCADE
				',
			),
		),
		'mshop_locale_language' => array (
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
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'LocaleAddSiteidToLangAndCurrency' );
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
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Changes CONSTRAINT action ON DELETE for locale tables.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Change locale siteid foreign key constraints', 0 ); $this->_status( '' );

		foreach( $stmts as $table => $stmtLists )
		{
			foreach ( $stmtLists as $constraint=>$stmtList )
			{
				$this->_msg( sprintf( 'Checking constraint "%1$s": ', $constraint ), 1 );
				if( $this->_schema->tableExists( $table ) )
				{
					if( $this->_schema->getColumnDetails( $table, 'siteid' )->isNullable() === false )
					{
						$this->_execute( $stmtList['column'] );

						if( $this->_schema->constraintExists( $table, $constraint ) === true ) {
							$this->_execute( $stmtList['drop'] );
						}
					}

					if( $this->_schema->constraintExists( $table, $constraint ) !== true )
					{
						$this->_execute( $stmtList['add'] );
						$this->_status( 'changed' );
					}
					else
					{
						$this->_status( 'OK' );
					}
				}
				else
				{
					$this->_status( 'OK' );
				}
			}
		}
	}
}
