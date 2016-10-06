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
	 * Removes old columns and sequences
	 */
	public function clean()
	{
		$this->msg( 'Cleaning base tables', 0 );
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

		$this->setupSchema( $files, true );

		$files = array(
			'db-product' => __DIR__ . $ds . 'default' . $ds . 'schema' . $ds . 'index.php',
		);

		$this->setupSchema( $files, true );
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
	protected function setupSchema( array $files, $clean = false )
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

			$dbalschema = new \Doctrine\DBAL\Schema\Schema();
			$dbalManager = $dbal->getSchemaManager();
			$platform = $dbal->getDatabasePlatform();
			$schema = $this->getSchema( $rname );

			if( isset( $list['table'] ) )
			{
				foreach( (array) $list['table'] as $name => $fcn )
				{
					$this->msg( sprintf( 'Checking table "%1$s": ', $name ), 2 );

					$table = $dbalManager->listTableDetails( $name );
					$tables = ( $table->getColumns() !== array() ? array( $table ) : array() );

					$tableSchema = new \Doctrine\DBAL\Schema\Schema( $tables );
					$schemaDiff = \Doctrine\DBAL\Schema\Comparator::compareSchemas( $tableSchema, $fcn( clone $dbalschema ) );
					$stmts = $this->remove( $this->exclude( $schemaDiff, $list ), $clean )->toSaveSql( $platform );

					$this->executeList( $stmts, $rname );
					$this->status( 'done' );
				}
			}

			if( isset( $list['sequence'] ) && $schema->supports( $schema::HAS_SEQUENCES ) )
			{
				$sequences = $dbalManager->listSequences();

				foreach( (array) $list['sequence'] as $name => $fcn )
				{
					$this->msg( sprintf( 'Checking sequence "%1$s": ', $name ), 2 );

					$seqSchema = new \Doctrine\DBAL\Schema\Schema( array(), $sequences );
					$schemaDiff = \Doctrine\DBAL\Schema\Comparator::compareSchemas( $seqSchema, $fcn( clone $dbalschema ) );
					$stmts = $this->remove( $schemaDiff, $clean )->toSaveSql( $platform );

					$this->executeList( $stmts, $rname );
					$this->status( 'done' );
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


	/**
	 * Removes excluded indexes from DBAL schema diff
	 *
	 * @param \Doctrine\DBAL\Schema\SchemaDiff $schemaDiff DBAL schema diff object
	 * @param array $list Associative list with "exclude", "table" and "sequence" keys
	 * @return \Doctrine\DBAL\Schema\SchemaDiff Modified DBAL schema diff object
	 */
	private function exclude( \Doctrine\DBAL\Schema\SchemaDiff $schemaDiff, array $list )
	{
		if( isset( $list['exclude'] ) )
		{
			foreach( $schemaDiff->changedTables as $tableDiff )
			{
				foreach( $tableDiff->removedIndexes as $idx => $index )
				{
					if( in_array( $index->getName(), $list['exclude'] ) ) {
						unset( $tableDiff->removedIndexes[$idx] );
					}
				}
			}
		}

		return $schemaDiff;
	}


	/**
	 * Keeps removed columns and sequences if not in cleanup mode
	 *
	 * @param \Doctrine\DBAL\Schema\SchemaDiff $schemaDiff DBAL schema diff object
	 * @param boolean $clean If old columns and sequences should be removed
	 * @return \Doctrine\DBAL\Schema\SchemaDiff Modified DBAL schema diff object
	 */
	private function remove( \Doctrine\DBAL\Schema\SchemaDiff $schemaDiff, $clean )
	{
		if( $clean !== true )
		{
			foreach( $schemaDiff->changedTables as $tableDiff )
			{
				$tableDiff->removedColumns = array();
				$tableDiff->removedIndexes = array();
				$tableDiff->renamedIndexes = array();
			}

			$schemaDiff->removedSequences = array();
		}

		return $schemaDiff;
	}
}
