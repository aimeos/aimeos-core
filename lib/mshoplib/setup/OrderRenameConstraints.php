<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Renames constraints to new schema of order base tables.
 */
class OrderRenameConstraints extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_order_base' => array(
			'pk_ordba_id' => 'ALTER TABLE "mshop_order_base" DROP PRIMARY KEY, ADD CONSTRAINT "pk_ordba_id" PRIMARY KEY ("id")',
		),
		'mshop_order_base_address' => array(
			'pk_msordad_id' => 'ALTER TABLE "mshop_order_base_address" DROP PRIMARY KEY, ADD CONSTRAINT "pk_ordbaad_id" PRIMARY KEY ("id")',
			'unq_msordad_baseid_domain' => 'ALTER TABLE "mshop_order_base_address" DROP INDEX "unq_msordad_baseid_domain", ADD CONSTRAINT "unq_msordbaad_bid_dom" UNIQUE ("baseid", "domain")',
			'fk_msordad_orderid' => 'ALTER TABLE "mshop_order_base_address" DROP FOREIGN KEY "fk_msordad_orderid", ADD CONSTRAINT "fk_msordbaad_baseid" FOREIGN KEY ("baseid") REFERENCES "mshop_order_base" ("id") ON UPDATE CASCADE ON DELETE CASCADE',
		),
		'mshop_order_base_product' => array(
			'pk_msordpr_id' => 'ALTER TABLE "mshop_order_base_product" DROP PRIMARY KEY, ADD CONSTRAINT "pk_ordbapr_id" PRIMARY KEY ("id")',
			'fk_msordpr_baseid' => 'ALTER TABLE "mshop_order_base_product" DROP FOREIGN KEY "fk_msordpr_baseid", DROP INDEX "fk_msordpr_baseid", ADD INDEX "fk_msordbapr_baseid" ("baseid"), ADD CONSTRAINT "fk_msordbapr_baseid" FOREIGN KEY ("baseid") REFERENCES "mshop_order_base" ("id") ON UPDATE CASCADE ON DELETE CASCADE',
		),
		'mshop_order_base_discount' => array(
			'pk_msorddi_id' => 'ALTER TABLE "mshop_order_base_discount" DROP PRIMARY KEY, ADD CONSTRAINT "pk_msordbadi_id" PRIMARY KEY ("id")',
			'fk_msorddi_baseid' => 'ALTER TABLE "mshop_order_base_discount" DROP FOREIGN KEY "fk_msorddi_baseid", DROP INDEX "fk_msorddi_baseid", ADD INDEX "fk_msordbadi_baseid" ("baseid"), ADD CONSTRAINT "fk_msordbadi_baseid" FOREIGN KEY ("baseid") REFERENCES "mshop_order_base" ("id") ON UPDATE CASCADE ON DELETE CASCADE',
			'fk_msorddi_ordprodid' => 'ALTER TABLE "mshop_order_base_discount" DROP FOREIGN KEY "fk_msorddi_ordprodid", DROP INDEX "fk_msorddi_ordprodid", ADD INDEX "fk_msordbadi_ordbaprod" ("ordprodid"), ADD CONSTRAINT "fk_msordbadi_ordprodid" FOREIGN KEY ("ordprodid") REFERENCES "mshop_order_base_product" ("id") ON UPDATE CASCADE ON DELETE CASCADE',
		),
		'mshop_order_base_service' => array(
			'pk_msordse_id' => 'ALTER TABLE "mshop_order_base_service" DROP PRIMARY KEY, ADD CONSTRAINT "pk_msordbase_id" PRIMARY KEY ("id")',
			'unq_msordse_bid_dn_code' => 'ALTER TABLE "mshop_order_base_service" DROP INDEX "unq_msordse_bid_dn_code", ADD CONSTRAINT "unq_msordbase_bid_dom_code" UNIQUE ("baseid", "domain", "code")',
			'fk_msordse_baseid' => 'ALTER TABLE "mshop_order_base_service" DROP FOREIGN KEY "fk_msordse_baseid", ADD CONSTRAINT "fk_msordbase_baseid" FOREIGN KEY ("baseid") REFERENCES "mshop_order_base" ("id") ON UPDATE CASCADE ON DELETE CASCADE',
		),
		'mshop_order_base_service_attr' => array(
			'unq_msordseat_ordservid_code' => 'ALTER TABLE "mshop_order_base_service_attr" DROP INDEX "unq_msordseat_ordservid_code", ADD CONSTRAINT "unq_msordbaseat_ordservid_code" UNIQUE ("ordservid", "code")',
			'fk_msordseat_ordservid' => 'ALTER TABLE "mshop_order_base_service_attr" DROP FOREIGN KEY "fk_msordseat_ordservid", ADD CONSTRAINT "fk_msordbaseat_ordservid" FOREIGN KEY ("ordservid") REFERENCES "mshop_order_base_service" ("id") ON UPDATE CASCADE ON DELETE CASCADE',
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
		$this->msg( 'Renaming order constraints', 0 ); $this->status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			foreach( $stmtList as $constraint=>$stmt )
			{
				$this->msg( sprintf( 'Checking constraint "%1$s": ', $constraint ), 1 );

				if( $this->schema->constraintExists( $table, $constraint ) )
				{
					$this->execute( $stmt );
					$this->status( 'renamed' );
				} else {
					$this->status( 'OK' );
				}
			}
		}
	}
}
