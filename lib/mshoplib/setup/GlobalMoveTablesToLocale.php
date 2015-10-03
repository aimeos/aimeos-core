<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Renames global tables to locale.
 */
class MW_Setup_Task_GlobalMoveTablesToLocale extends MW_Setup_Task_Base
{

	private $mysql = array(
		'mshop_global_currency' => array(
			'RENAME TABLE "mshop_global_currency" TO "mshop_locale_currency"',
		),
		'mshop_global_language' => array(
			'RENAME TABLE "mshop_global_language" TO "mshop_locale_language"',
		),
		'mshop_global_site' => array(
			'RENAME TABLE "mshop_global_site" TO "mshop_locale_site"',
			'ALTER TABLE "mshop_locale_site" DROP INDEX "unq_msglsite_code"',
			'ALTER TABLE "mshop_locale_site"
				ADD CONSTRAINT "unq_mslocsi_code" UNIQUE ("code")',
		),
	);

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array(
			'CatalogTreeToCatalog',
			'DiscountAddForeignKey',
			'MediaAddForeignKey',
			'OrderAddForeignKey',
			'OrderAddSiteId',
			'ProductHousingAddSiteid',
			'StatusToSmallInt',
			'TextAddForeignKey'
		);
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
	 * Renames all order tables if they exist.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Renaming global tables', 0 );
		$this->status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			if( $this->schema->tableExists( $table ) )
			{
				$this->msg( sprintf( 'Changing table "%1$s": ', $table ), 1 );
				$this->executeList( $stmtList );
				$this->status( 'Ok' );
			}
		}
	}

}
