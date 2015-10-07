<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Removes locale constraints from customer tables.
 */
class CustomerDropLocaleConstraints extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_customer_list_type' => array(
			'fk_mscuslity_siteid' => 'ALTER TABLE "mshop_customer_list_type" DROP FOREIGN KEY "fk_mscuslity_siteid"',
		),
		'mshop_customer_list' => array(
			'fk_mscusli_siteid' => 'ALTER TABLE "mshop_customer_list" DROP FOREIGN KEY "fk_mscusli_siteid"',
		),
		'mshop_customer_address' => array(
			'fk_mscusad_siteid' => 'ALTER TABLE "mshop_customer_address" DROP FOREIGN KEY "fk_mscusad_siteid"',
			'fk_mscusad_langid' => 'ALTER TABLE "mshop_customer_address" DROP FOREIGN KEY "fk_mscusad_langid"',
		),
		'mshop_customer' => array(
			'fk_mscus_siteid' => 'ALTER TABLE "mshop_customer" DROP FOREIGN KEY "fk_mscus_siteid"',
			'fk_mscus_langid' => 'ALTER TABLE "mshop_customer" DROP FOREIGN KEY "fk_mscus_langid"',
		),
	);




	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'CustomerRenameConstraints' );
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
	 * Drops local constraints.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Removing locale constraints from customer tables', 0 );
		$this->status( '' );

		$schema = $this->getSchema( 'db-customer' );

		foreach( $stmts as $table => $list )
		{
			if( $schema->tableExists( $table ) === true )
			{
				foreach( $list as $constraint => $stmt )
				{
					$this->msg( sprintf( 'Removing "%1$s" from "%2$s"', $constraint, $table ), 1 );

					if( $schema->constraintExists( $table, $constraint ) !== false )
					{
						$this->execute( $stmt, 'db-customer' );
						$this->status( 'done' );
					} else {
						$this->status( 'OK' );
					}
				}
			}
		}
	}
}