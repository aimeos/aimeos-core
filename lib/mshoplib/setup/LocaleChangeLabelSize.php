<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Changes size of column label for locale tables.
 */
class MW_Setup_Task_LocaleChangeLabelSize extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_locale_site' => 'ALTER TABLE "mshop_locale_site" CHANGE "label" "label" VARCHAR(255) NOT NULL DEFAULT \'\'',
		'mshop_locale_language' => 'ALTER TABLE "mshop_locale_language" CHANGE "label" "label" VARCHAR(255) NOT NULL DEFAULT \'\'',
		'mshop_locale_currency' => 'ALTER TABLE "mshop_locale_currency" CHANGE "label" "label" VARCHAR(255) NOT NULL DEFAULT \'\'',
	);

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'GlobalMoveTablesToLocale' );
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
	 * Changes size of column label for locale tables.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Changing size of column label for locale tables', 0 );
		$this->_status( '' );

		foreach( $stmts as $table => $stmt )
		{
			$this->_msg( sprintf( 'Checking table "%1$s" for label": ', $table ), 1 );
			if( $this->_schema->tableExists( $table ) === true
				&& $this->_schema->columnExists( $table, 'label' ) === true
				&& $this->_schema->getColumnDetails( $table, 'label' )->getMaxLength() < 255 )
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
