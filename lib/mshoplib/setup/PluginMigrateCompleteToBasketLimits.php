<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

/**
 * Migrates "Complete" plugin to "BasketLimits".
 */

class MW_Setup_Task_PluginMigrateCompleteToBasketLimits extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
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
		return array();
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Migrates "Complete" plugin if necessary.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Migrating "Complete" to "BasketLimits" plugin', 0 ); $this->_status( '' );

		if( $this->_schema->columnExists( 'mshop_plugin', 'config' ) === true
			&& $this->_schema->columnExists( 'mshop_plugin', 'provider' ) === true
		) {
			foreach( $stmts as $key => $list )
			{
				$this->_msg( sprintf( 'Migrating "%1$s"', $key ), 1 );

				if( $this->_getValue( $list['select'], 'cnt' ) > 0 )
				{
					$this->_execute( $list['update'] );
					$this->_status( 'migrated' );
				}
				else
				{
					$this->_status( 'OK' );
				}
			}
		}
	}
}