<?php

/**
 * @copyright Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Removes foreign key constraints to locale tables from order tables.
 */
class OrderDropForeignKeyOfLocale extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_order_base' => array(
			'fk_msordba_langid' => 'ALTER TABLE "mshop_order_base" DROP FOREIGN KEY "fk_msordba_langid"',
			'fk_msordba_curid' => 'ALTER TABLE "mshop_order_base" DROP FOREIGN KEY "fk_msordba_curid"',
			'fk_msordba_siteid' => 'ALTER TABLE "mshop_order_base" DROP FOREIGN KEY "fk_msordba_siteid"',
		),
		'mshop_order' => array(
			'fk_msord_siteid' => 'ALTER TABLE "mshop_order" DROP FOREIGN KEY "fk_msord_siteid"',
		),
		'mshop_order_base_address' => array(
			'fk_msordbaad_siteid' => 'ALTER TABLE "mshop_order_base_address" DROP FOREIGN KEY "fk_msordbaad_siteid"',
		),
		'mshop_order_base_product' => array(
			'fk_msordbapr_siteid' => 'ALTER TABLE "mshop_order_base_product" DROP FOREIGN KEY "fk_msordbapr_siteid"',
		),
		'mshop_order_base_product_attr' => array(
			'fk_msordbaprat_siteid' => 'ALTER TABLE "mshop_order_base_product_attr" DROP FOREIGN KEY "fk_msordbaprat_siteid"',
		),
		'mshop_order_base_service' => array(
			'fk_msordbase_siteid' => 'ALTER TABLE "mshop_order_base_service" DROP FOREIGN KEY "fk_msordbase_siteid"',
		),
		'mshop_order_base_service_attr' => array(
			'fk_msordbaseat_siteid' => 'ALTER TABLE "mshop_order_base_service_attr" DROP FOREIGN KEY "fk_msordbaseat_siteid"',
		),
		'mshop_order_status' => array(
			'fk_msordst_siteid' => 'ALTER TABLE "mshop_order_status" DROP FOREIGN KEY "fk_msordst_siteid"',
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderAlterForeignKeyContraintsOnDelete', 'OrderRenameTables' );
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
	 * Delete foreign keys to locale domain in mshop order tables.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Delete foreign keys to locale domain in mshop order tables', 0 );
		$this->status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->schema->tableExists( $table ) === true )
			{
				$this->status( '' );

				foreach( $stmtList as $constraint => $stmt )
				{
					$this->msg( sprintf( 'Checking constraint "%1$s": ', $constraint ), 2 );

					if( $this->schema->constraintExists( $table, $constraint ) === true )
					{
						$this->execute( $stmt );
						$this->status( 'deleted' );
					}
					else
					{
						$this->status( 'OK' );
					}
				}
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}
}
