<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Moves data from subject tables to customer and supplier tables.
 */
class SubjectToCustomerSupplier extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_subject_customer' => array(
			'INSERT INTO "mshop_customer"("id", "label", "status") SELECT msc."id", msc."label" FROM "mshop_subject_customer" AS msc',
			'DROP TABLE "mshop_subject_customer"',
		),
		'mshop_subject_supplier' => array(
			'INSERT INTO "mshop_supplier" ("id", "label", "status") SELECT mss."id", mss."label", mss."status" FROM "mshop_subject_supplier" AS mss',
			'DROP TABLE "mshop_subject_supplier"',
		),
		'mshop_subject_common_address' => array(
			'INSERT INTO "mshop_customer_address" ("siteid", "refid", "company", "salutation",
				"title", "firstname", "lastname", "address1", "address2", "address3", "postal", "city", "state",
				"langid", "telephone", "email", "telefax", "website", "pos") SELECT ms."siteid", msca."refid", msca."company", msca."salutation",
				msca."title", msca."firstname", msca."lastname", msca."address1", msca."address2", msca."address3", msca."postal", msca."city", msca."state",
				msca."countryid", msca."telephone", msca."email", msca."telefax", msca."website", msca."pos"
				FROM "mshop_subject_common_address" AS msca
				LEFT JOIN "mshop_subject" AS ms ON ( msca."domain" = ms."domain" )
				WHERE msca."domain" = \'customer\'',
			'INSERT INTO "mshop_supplier_address" ("siteid", "refid", "company", "salutation",
				"title", "firstname", "lastname", "address1", "address2", "address3", "postal", "city", "state",
				"langid", "telephone", "email", "telefax", "website", "pos") SELECT ms."siteid", msca."refid", msca."company", msca."salutation",
				msca."title", msca."firstname", msca."lastname", msca."address1", msca."address2", msca."address3", msca."postal", msca."city", msca."state",
				msca."countryid", msca."telephone", msca."email", msca."telefax", msca."website", msca."pos"
				FROM "mshop_subject_common_address" AS msca
				LEFT JOIN "mshop_subject" AS ms ON ( msca."domain" = ms."domain" )
				WHERE msca."domain" = \'supplier\'',
			'DROP TABLE "mshop_subject_common_address"',
		),
		'mshop_subject' => array(
			'DROP TABLE "mshop_subject"'
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return [];
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
	 * Migrates subject domain tables to customer and supplier domain.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Migrating subject tables', 0 ); $this->status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->schema->tableExists( $table ) === true )
			{
				$this->executeList( $stmtList );
				$this->status( 'migrated' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}
}
