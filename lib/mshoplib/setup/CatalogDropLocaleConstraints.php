<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Removes locale constraints from catalog tables.
 */
class MW_Setup_Task_CatalogDropLocaleConstraints extends MW_Setup_Task_Abstract
{
	private $mysql = array(
		'mshop_catalog_list_type' => array(
			'fk_mscatlity_siteid' => 'ALTER TABLE "mshop_catalog_list_type" DROP FOREIGN KEY "fk_mscatlity_siteid"',
		),
		'mshop_catalog_list' => array(
			'fk_mscatli_siteid' => 'ALTER TABLE "mshop_catalog_list" DROP FOREIGN KEY "fk_mscatli_siteid"',
		),
		'mshop_catalog' => array(
			'fk_mscat_siteid' => 'ALTER TABLE "mshop_catalog" DROP FOREIGN KEY "fk_mscat_siteid"',
		),
	);




	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array();
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
		$this->msg( 'Removing locale constraints from catalog tables', 0 );
		$this->status( '' );

		$schema = $this->getSchema( 'db-catalog' );

		foreach( $stmts as $table => $list )
		{
			if( $schema->tableExists( $table ) === true )
			{
				foreach( $list as $constraint => $stmt )
				{
					$this->msg( sprintf( 'Removing "%1$s" from "%2$s"', $constraint, $table ), 1 );

					if( $schema->constraintExists( $table, $constraint ) !== false )
					{
						$this->execute( $stmt, 'db-catalog' );
						$this->status( 'done' );
					} else {
						$this->status( 'OK' );
					}
				}
			}
		}
	}
}