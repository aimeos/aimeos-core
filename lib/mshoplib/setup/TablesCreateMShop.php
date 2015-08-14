<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Creates all required tables.
 */
class MW_Setup_Task_TablesCreateMShop extends MW_Setup_Task_Abstract
{
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
		$this->_msg( 'Creating base tables', 0 );
		$this->_status( '' );

		$ds = DIRECTORY_SEPARATOR;

		$files = array(
			'db-locale' => __DIR__ . $ds . 'default' . $ds . 'schema' . $ds . 'mysql' . $ds . 'locale.sql',
			'db-attribute' => __DIR__ . $ds . 'default' . $ds . 'schema' . $ds . 'mysql' . $ds . 'attribute.sql',
			'db-customer' => __DIR__ . $ds . 'default' . $ds . 'schema' . $ds . 'mysql' . $ds . 'customer.sql',
			'db-media' => __DIR__ . $ds . 'default' . $ds . 'schema' . $ds . 'mysql' . $ds . 'media.sql',
			'db-order' => __DIR__ . $ds . 'default' . $ds . 'schema' . $ds . 'mysql' . $ds . 'order.sql',
			'db-plugin' => __DIR__ . $ds . 'default' . $ds . 'schema' . $ds . 'mysql' . $ds . 'plugin.sql',
			'db-price' => __DIR__ . $ds . 'default' . $ds . 'schema' . $ds . 'mysql' . $ds . 'price.sql',
			'db-product' => __DIR__ . $ds . 'default' . $ds . 'schema' . $ds . 'mysql' . $ds . 'product.sql',
			'db-service' => __DIR__ . $ds . 'default' . $ds . 'schema' . $ds . 'mysql' . $ds . 'service.sql',
			'db-supplier' => __DIR__ . $ds . 'default' . $ds . 'schema' . $ds . 'mysql' . $ds . 'supplier.sql',
			'db-text' => __DIR__ . $ds . 'default' . $ds . 'schema' . $ds . 'mysql' . $ds . 'text.sql',
			'db-coupon' => __DIR__ . $ds . 'default' . $ds . 'schema' . $ds . 'mysql' . $ds . 'coupon.sql',
			'db-catalog' => __DIR__ . $ds . 'default' . $ds . 'schema' . $ds . 'mysql' . $ds . 'catalog.sql',
		);

		$this->_setup( $files );

		$files = array(
			'db-product' => __DIR__ . $ds . 'default' . $ds . 'schema' . $ds . 'mysql' . $ds . 'index.sql',
		);

		$this->_setup( $files );
	}


	/**
	 * Creates all required tables if they don't exist
	 */
	protected function _setup( array $files )
	{
		foreach( $files as $rname => $filepath )
		{
			$this->_msg( 'Using tables from ' . basename( $filepath ), 1 ); $this->_status( '' );

			if( ( $content = file_get_contents( $filepath ) ) === false ) {
				throw new MW_Setup_Exception( sprintf( 'Unable to get content from file "%1$s"', $filepath ) );
			}

			$schema = $this->_getSchema( $rname );

			foreach( $this->_getTableDefinitions( $content ) as $name => $sql )
			{
				$this->_msg( sprintf( 'Checking table "%1$s": ', $name ), 2 );

				if( $schema->tableExists( $name ) !== true ) {
					$this->_execute( $sql, $rname );
					$this->_status( 'created' );
				} else {
					$this->_status( 'OK' );
				}
			}

			foreach( $this->_getIndexDefinitions( $content ) as $name => $sql )
			{
				$parts = explode( '.', $name );
				$this->_msg( sprintf( 'Checking index "%1$s": ', $name ), 2 );

				if( $schema->indexExists( $parts[0], $parts[1] ) !== true ) {
					$this->_execute( $sql, $rname );
					$this->_status( 'created' );
				} else {
					$this->_status( 'OK' );
				}
			}
		}
	}
}
