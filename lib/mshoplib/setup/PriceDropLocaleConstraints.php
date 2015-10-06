<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Removes locale constraints from price tables.
 */
class MW_Setup_Task_PriceDropLocaleConstraints extends MW_Setup_Task_Abstract
{
	private $mysql = array(
		'mshop_price_list_type' => array(
			'fk_msprility_siteid' => 'ALTER TABLE "mshop_price_list_type" DROP FOREIGN KEY "fk_msprility_siteid"',
		),
		'mshop_price_list' => array(
			'fk_msprili_siteid' => 'ALTER TABLE "mshop_price_list" DROP FOREIGN KEY "fk_msprili_siteid"',
		),
		'mshop_price_type' => array(
			'fk_msprity_siteid' => 'ALTER TABLE "mshop_price_type" DROP FOREIGN KEY "fk_msprity_siteid"',
		),
		'mshop_price' => array(
			'fk_mspri_siteid' => 'ALTER TABLE "mshop_price" DROP FOREIGN KEY "fk_mspri_siteid"',
			'fk_mspri_curid' => 'ALTER TABLE "mshop_price" DROP FOREIGN KEY "fk_mspri_curid"',
		),
	);




	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'PriceRenameConstraints' );
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
		$this->msg( 'Removing locale constraints from price tables', 0 );
		$this->status( '' );

		$schema = $this->getSchema( 'db-price' );

		foreach( $stmts as $table => $list )
		{
			if( $schema->tableExists( $table ) === true )
			{
				foreach( $list as $constraint => $stmt )
				{
					$this->msg( sprintf( 'Removing "%1$s" from "%2$s"', $constraint, $table ), 1 );

					if( $schema->constraintExists( $table, $constraint ) !== false )
					{
						$this->execute( $stmt, 'db-price' );
						$this->status( 'done' );
					} else {
						$this->status( 'OK' );
					}
				}
			}
		}
	}
}