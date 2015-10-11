<?php

/**
 * @copyright Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Removes locale constraints from service tables.
 */
class ServiceDropLocaleConstraints extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_service_list_type' => array(
			'fk_msser_siteid' => 'ALTER TABLE "mshop_service_list_type" DROP FOREIGN KEY "fk_msser_siteid"',
		),
		'mshop_service_list' => array(
			'fk_msserli_siteid' => 'ALTER TABLE "mshop_service_list" DROP FOREIGN KEY "fk_msserli_siteid"',
		),
		'mshop_service_type' => array(
			'fk_msserty_siteid' => 'ALTER TABLE "mshop_service_type" DROP FOREIGN KEY "fk_msserty_siteid"',
		),
		'mshop_service' => array(
			'fk_msser_siteid' => 'ALTER TABLE "mshop_service" DROP FOREIGN KEY "fk_msser_siteid"',
		),
	);




	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'AttributeListRenameSiteidConstraints' );
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
		$this->msg( 'Removing locale constraints from service tables', 0 );
		$this->status( '' );

		$schema = $this->getSchema( 'db-service' );

		foreach( $stmts as $table => $list )
		{
			if( $schema->tableExists( $table ) === true )
			{
				foreach( $list as $constraint => $stmt )
				{
					$this->msg( sprintf( 'Removing "%1$s" from "%2$s"', $constraint, $table ), 1 );

					if( $schema->constraintExists( $table, $constraint ) !== false )
					{
						$this->execute( $stmt, 'db-service' );
						$this->status( 'done' );
					} else {
						$this->status( 'OK' );
					}
				}
			}
		}
	}
}