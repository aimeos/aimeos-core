<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

namespace Aimeos\MW\Setup\Task;


/**
 * Migrates "Complete" plugin to "BasketLimits".
 */
class PluginMigrateCompleteToBasketLimits extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'minorder' => array(
			'select' => 'SELECT COUNT(*) AS "cnt" FROM "mshop_plugin" WHERE "config" LIKE \'%minorder%\'',
			'update' => 'UPDATE "mshop_plugin" SET "config" = REPLACE("config", \'minorder\', \'min-value\') WHERE "config" LIKE \'%minorder%\'',
		),
		'minproducts' => array(
			'select' => 'SELECT COUNT(*) AS "cnt" FROM "mshop_plugin" WHERE "config" LIKE \'%minproducts%\'',
			'update' => 'UPDATE "mshop_plugin" SET "config" = REPLACE("config", \'minproducts\', \'min-products\') WHERE "config" LIKE \'%minproducts%\'',
		),
		'Complete' => array(
			'select' => 'SELECT COUNT(*) AS "cnt" FROM "mshop_plugin" WHERE "provider" LIKE \'%Complete%\'',
			'update' => 'UPDATE "mshop_plugin" SET "provider" = REPLACE("provider", \'Complete\', \'BasketLimits\') WHERE "provider" LIKE \'%Complete%\'',
		),
	);

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'TablesCreateMShop' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return [];
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function mysql()
	{
		$this->process( $this->mysql );
	}


	/**
	 * Migrates "Complete" plugin if necessary.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Migrating "Complete" to "BasketLimits" plugin', 0 ); $this->status( '' );

		if( $this->schema->columnExists( 'mshop_plugin', 'config' ) === true
			&& $this->schema->columnExists( 'mshop_plugin', 'provider' ) === true
		) {
			foreach( $stmts as $key => $list )
			{
				$this->msg( sprintf( 'Migrating "%1$s"', $key ), 1 );

				if( $this->getValue( $list['select'], 'cnt' ) > 0 )
				{
					$this->execute( $list['update'] );
					$this->status( 'migrated' );
				}
				else
				{
					$this->status( 'OK' );
				}
			}
		}
	}
}