<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Renames domain column to type in order tables.
 */
class OrderRenameDomainToType extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_order' => array(
			'ALTER TABLE "mshop_order" CHANGE "domain" "type" VARCHAR(8) NOT NULL',
		),
		'mshop_order_base_address' => array(
			'ALTER TABLE "mshop_order_base_address" CHANGE "domain" "type" VARCHAR(8) NOT NULL',
			'ALTER TABLE "mshop_order_base_address" DROP INDEX "unq_msordbaad_bid_dom", ADD CONSTRAINT "unq_msordbaad_bid_type" UNIQUE ("baseid", "type")',
		),
		'mshop_order_base_service' => array(
			'ALTER TABLE "mshop_order_base_service" CHANGE "domain" "type" VARCHAR(8) NOT NULL',
			'ALTER TABLE "mshop_order_base_service" DROP INDEX "unq_msordbase_bid_dom_code", ADD CONSTRAINT "unq_msordbase_bid_type_code" UNIQUE ("baseid", "type", "code")',
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderRenameTables' );
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
		$this->msg( 'Renaming order domain', 0 ); $this->status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->schema->tableExists( $table ) && $this->schema->columnExists( $table, 'domain' ) === true )
			{
				$this->executeList( $stmtList );
				$this->status( 'renamed' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}

}
