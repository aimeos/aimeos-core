<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

/*
 * Removing siteid from PRIMARY KEY on locale tables.
 */
class MW_Setup_Task_LocaleChangePrimary extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
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
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array('LocaleAddSiteidToLangAndCurrency');
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array('TablesCreateMShop');
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Change critical PRIMARY KEY-constellation in locale tables.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Changing PRIMARY KEYS for locale', 0 ); $this->_status( '' );

		$search = '
			SELECT COUNT(INDEX_NAME) "counter" FROM
			INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE()
			AND TABLE_NAME = \'%1$s\' AND INDEX_NAME = \'PRIMARY\'
			AND COLUMN_NAME IN(\'id\', \'siteid\')
		';

		foreach( $stmts as $table => $stmt )
		{
			$this->_msg( sprintf( 'Checking table "%1$s" for PRIMARY": ', $table ), 1 );
			$counter = 0;
			$counter = $this->_getValue( sprintf( $search, $table ), 'counter' );

			if( $counter == 2 )
			{
				$this->_execute( $stmt );
				$this->_status( 'changed' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
	}
}