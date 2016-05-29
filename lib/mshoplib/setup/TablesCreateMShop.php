<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Creates all required tables.
 */
class TablesCreateMShop extends \Aimeos\MW\Setup\Task\Base
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
	 * Creates the MShop tables
	 */
	public function migrate()
	{
		$this->msg( 'Creating base tables', 0 );
		$this->status( '' );

		$ds = DIRECTORY_SEPARATOR;

		$files = array(
			'db-locale' => __DIR__ . $ds . 'default' . $ds . 'schema' . $ds . 'locale.php',
			'db-attribute' => __DIR__ . $ds . 'default' . $ds . 'schema' . $ds . 'attribute.php',
			'db-customer' => __DIR__ . $ds . 'default' . $ds . 'schema' . $ds . 'customer.php',
			'db-media' => __DIR__ . $ds . 'default' . $ds . 'schema' . $ds . 'media.php',
			'db-order' => __DIR__ . $ds . 'default' . $ds . 'schema' . $ds . 'order.php',
			'db-plugin' => __DIR__ . $ds . 'default' . $ds . 'schema' . $ds . 'plugin.php',
			'db-price' => __DIR__ . $ds . 'default' . $ds . 'schema' . $ds . 'price.php',
			'db-product' => __DIR__ . $ds . 'default' . $ds . 'schema' . $ds . 'product.php',
			'db-service' => __DIR__ . $ds . 'default' . $ds . 'schema' . $ds . 'service.php',
			'db-supplier' => __DIR__ . $ds . 'default' . $ds . 'schema' . $ds . 'supplier.php',
			'db-text' => __DIR__ . $ds . 'default' . $ds . 'schema' . $ds . 'text.php',
			'db-coupon' => __DIR__ . $ds . 'default' . $ds . 'schema' . $ds . 'coupon.php',
			'db-catalog' => __DIR__ . $ds . 'default' . $ds . 'schema' . $ds . 'catalog.php',
			'db-tag' => __DIR__ . $ds . 'default' . $ds . 'schema' . $ds . 'tag.php',
		);

		$this->setupSchema( $files );

		$files = array(
			'db-product' => __DIR__ . $ds . 'default' . $ds . 'schema' . $ds . 'index.php',
		);

		$this->setupSchema( $files );
	}


	/**
	 * Creates all required tables from schema if they don't exist
	 */
	protected function setupSchema( array $files )
	{
		foreach( $files as $rname => $filepath )
		{
			$this->msg( 'Using schema from ' . basename( $filepath ), 1 ); $this->status( '' );

			if( ( $list = include( $filepath ) ) === false ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Unable to get list from file "%1$s"', $filepath ) );
			}

			$dbal = $this->getConnection( $rname )->getRawObject();

			if( !( $dbal instanceof \Doctrine\DBAL\Connection ) ) {
				throw new \Aimeos\MW\Setup\Exception( 'Not a DBAL connection' );
			}

			$dbalschema = new \Doctrine\DBAL\Schema\Schema();;
			$platform = $dbal->getDatabasePlatform();
			$schema = $this->getSchema( $rname );

			if( isset( $list['table'] ) )
			{
				foreach( (array) $list['table'] as $name => $fcn )
				{
					$this->msg( sprintf( 'Checking table "%1$s": ', $name ), 2 );

					if( $schema->tableExists( $name ) !== true ) {
						$this->executeList( $fcn( clone $dbalschema )->toSql( $platform ), $rname );
						$this->status( 'created' );
					} else {
						$this->status( 'OK' );
					}
				}
			}

			if( isset( $list['sequence'] ) )
			{
				foreach( (array) $list['sequence'] as $name => $fcn )
				{
					$this->msg( sprintf( 'Checking sequence "%1$s": ', $name ), 2 );

					if( $schema->supports( $schema::HAS_SEQUENCES ) && $schema->sequenceExists( $name ) !== true ) {
						$this->executeList( $fcn( clone $dbalschema )->toSql( $platform ), $rname );
						$this->status( 'created' );
					} else {
						$this->status( 'OK' );
					}
				}
			}
		}
	}


	/**
	 * Creates all required tables from SQL statements if they don't exist
	 */
	protected function setup( array $files )
	{
		foreach( $files as $rname => $filepath )
		{
			$this->msg( 'Using tables from ' . basename( $filepath ), 1 ); $this->status( '' );

			if( ( $content = file_get_contents( $filepath ) ) === false ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Unable to get content from file "%1$s"', $filepath ) );
			}

			$schema = $this->getSchema( $rname );

			foreach( $this->getTableDefinitions( $content ) as $name => $sql )
			{
				$this->msg( sprintf( 'Checking table "%1$s": ', $name ), 2 );

				if( $schema->tableExists( $name ) !== true ) {
					$this->execute( $sql, $rname );
					$this->status( 'created' );
				} else {
					$this->status( 'OK' );
				}
			}

			foreach( $this->getIndexDefinitions( $content ) as $name => $sql )
			{
				$parts = explode( '.', $name );
				$this->msg( sprintf( 'Checking index "%1$s": ', $name ), 2 );

				if( $schema->indexExists( $parts[0], $parts[1] ) !== true ) {
					$this->execute( $sql, $rname );
					$this->status( 'created' );
				} else {
					$this->status( 'OK' );
				}
			}
		}
	}
}
