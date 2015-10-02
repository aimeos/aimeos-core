<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Changes size of column label for locale tables.
 */
class MW_Setup_Task_LocaleChangeLabelSize extends MW_Setup_Task_Abstract
{
	private $mysql = array(
		'mshop_locale_site' => 'ALTER TABLE "mshop_locale_site" CHANGE "label" "label" VARCHAR(255) NOT NULL DEFAULT \'\'',
		'mshop_locale_language' => 'ALTER TABLE "mshop_locale_language" CHANGE "label" "label" VARCHAR(255) NOT NULL DEFAULT \'\'',
		'mshop_locale_currency' => 'ALTER TABLE "mshop_locale_currency" CHANGE "label" "label" VARCHAR(255) NOT NULL DEFAULT \'\'',
	);

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'GlobalMoveTablesToLocale' );
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
	 * Changes size of column label for locale tables.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Changing size of column label for locale tables', 0 );
		$this->status( '' );

		foreach( $stmts as $table => $stmt )
		{
			$this->msg( sprintf( 'Checking table "%1$s" for label": ', $table ), 1 );
			if( $this->schema->tableExists( $table ) === true
				&& $this->schema->columnExists( $table, 'label' ) === true
				&& $this->schema->getColumnDetails( $table, 'label' )->getMaxLength() < 255 )
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
