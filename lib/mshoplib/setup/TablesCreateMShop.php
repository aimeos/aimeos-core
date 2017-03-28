<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
		return [];
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
	 * Removes old columns and sequences
	 */
	public function clean()
	{
		$this->msg( 'Cleaning base tables', 0 );
		$this->status( '' );

		$ds = DIRECTORY_SEPARATOR;

		$files = array(
			'db-locale' => 'default' . $ds . 'schema' . $ds . 'locale.php',
			'db-attribute' => 'default' . $ds . 'schema' . $ds . 'attribute.php',
			'db-customer' => 'default' . $ds . 'schema' . $ds . 'customer.php',
			'db-media' => 'default' . $ds . 'schema' . $ds . 'media.php',
			'db-order' => 'default' . $ds . 'schema' . $ds . 'order.php',
			'db-plugin' => 'default' . $ds . 'schema' . $ds . 'plugin.php',
			'db-price' => 'default' . $ds . 'schema' . $ds . 'price.php',
			'db-product' => 'default' . $ds . 'schema' . $ds . 'product.php',
			'db-stock' => 'default' . $ds . 'schema' . $ds . 'stock.php',
			'db-service' => 'default' . $ds . 'schema' . $ds . 'service.php',
			'db-supplier' => 'default' . $ds . 'schema' . $ds . 'supplier.php',
			'db-text' => 'default' . $ds . 'schema' . $ds . 'text.php',
			'db-coupon' => 'default' . $ds . 'schema' . $ds . 'coupon.php',
			'db-catalog' => 'default' . $ds . 'schema' . $ds . 'catalog.php',
			'db-tag' => 'default' . $ds . 'schema' . $ds . 'tag.php',
		);

		$this->setupSchema( $files, true );

		$files = array(
			'db-product' => 'default' . $ds . 'schema' . $ds . 'index.php',
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
			'db-locale' => 'default' . $ds . 'schema' . $ds . 'locale.php',
			'db-attribute' => 'default' . $ds . 'schema' . $ds . 'attribute.php',
			'db-customer' => 'default' . $ds . 'schema' . $ds . 'customer.php',
			'db-media' => 'default' . $ds . 'schema' . $ds . 'media.php',
			'db-order' => 'default' . $ds . 'schema' . $ds . 'order.php',
			'db-plugin' => 'default' . $ds . 'schema' . $ds . 'plugin.php',
			'db-price' => 'default' . $ds . 'schema' . $ds . 'price.php',
			'db-product' => 'default' . $ds . 'schema' . $ds . 'product.php',
			'db-stock' => 'default' . $ds . 'schema' . $ds . 'stock.php',
			'db-service' => 'default' . $ds . 'schema' . $ds . 'service.php',
			'db-supplier' => 'default' . $ds . 'schema' . $ds . 'supplier.php',
			'db-text' => 'default' . $ds . 'schema' . $ds . 'text.php',
			'db-coupon' => 'default' . $ds . 'schema' . $ds . 'coupon.php',
			'db-catalog' => 'default' . $ds . 'schema' . $ds . 'catalog.php',
			'db-tag' => 'default' . $ds . 'schema' . $ds . 'tag.php',
		);

		$this->setupSchema( $files );

		$files = array(
			'db-product' => 'default' . $ds . 'schema' . $ds . 'index.php',
		);

		$this->setupSchema( $files );
	}


	/**
	 * Returns the schema objects for the given type and relative path
	 *
	 * @param string $type Schema type, e.g. "table" or "sequence"
	 * @param string $relpath Relative path to the scheme file
	 * @return \Doctrine\DBAL\Schema\Schema[] Associative list of names as keys and schema objects as values
	 */
	protected function getSchemaObjects( $type, $relpath )
	{
		$schemaList = [];
		$dbalschema = new \Doctrine\DBAL\Schema\Schema();

		foreach( $this->getSetupPaths() as $abspath )
		{
			$filepath = $abspath . DIRECTORY_SEPARATOR . $relpath;

			if( !file_exists( $filepath ) ) {
				continue;
			}

			if( ( $list = include( $filepath ) ) === false ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Unable to get list from file "%1$s"', $filepath ) );
			}

			if( !isset( $list[$type] ) ) {
				continue;
			}

			foreach( (array) $list[$type] as $name => $fcn )
			{
				if( !isset( $schemaList[$name] ) ) {
					$schemaList[$name] = clone $dbalschema;
				}

				$schemaList[$name] = $fcn( $schemaList[$name] );
			}
		}

		return $schemaList;
	}


	/**
	 * Creates all required tables from schema if they don't exist
	 *
	 * @param array $files Associative list of resource names as keys and file paths as values
	 * @param boolean $clean True to remove left over columns or indexes, false to keep them untouched
	 */
	protected function setupSchema( array $files, $clean = false )
	{
		foreach( $files as $rname => $relpath )
		{
			$this->msg( 'Using schema from ' . basename( $relpath ), 1 ); $this->status( '' );

			$dbal = $this->getConnection( $rname )->getRawObject();

			if( !( $dbal instanceof \Doctrine\DBAL\Connection ) ) {
				throw new \Aimeos\MW\Setup\Exception( 'Not a DBAL connection' );
			}

			$dbalManager = $dbal->getSchemaManager();
			$platform = $dbal->getDatabasePlatform();
			$schema = $this->getSchema( $rname );


			foreach( $this->getSchemaObjects( 'table', $relpath ) as $name => $dbalschema )
			{
				$this->msg( sprintf( 'Checking table "%1$s": ', $name ), 2 );

				$table = $dbalManager->listTableDetails( $name );
				$tables = ( $table->getColumns() !== [] ? array( $table ) : [] );

				$tableSchema = new \Doctrine\DBAL\Schema\Schema( $tables );
				$schemaDiff = \Doctrine\DBAL\Schema\Comparator::compareSchemas( $tableSchema, $dbalschema );
				$stmts = $this->remove( $this->exclude( $schemaDiff, $relpath ), $clean )->toSaveSql( $platform );

				$this->executeList( $stmts, $rname );
				$this->status( 'done' );
			}

			if( $schema->supports( $schema::HAS_SEQUENCES ) )
			{
				$sequences = $dbalManager->listSequences();

				foreach( $this->getSchemaObjects( 'sequence', $relpath ) as $name => $dbalschema )
				{
					$this->msg( sprintf( 'Checking sequence "%1$s": ', $name ), 2 );

					$seqSchema = new \Doctrine\DBAL\Schema\Schema( [], $sequences );
					$schemaDiff = \Doctrine\DBAL\Schema\Comparator::compareSchemas( $seqSchema, $dbalschema );
					$stmts = $this->remove( $schemaDiff, $clean )->toSaveSql( $platform );

					$this->executeList( $stmts, $rname );
					$this->status( 'done' );
				}
			}
		}
	}


	/**
	 * Creates all required tables from SQL statements if they don't exist
	 *
	 * @param array $files Associative list of resource names as keys and file paths as values
	 * @deprecated Use setupSchema() instead
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
	 * @param string $relpath Relative path to the scheme file
	 * @return \Doctrine\DBAL\Schema\SchemaDiff Modified DBAL schema diff object
	 */
	private function exclude( \Doctrine\DBAL\Schema\SchemaDiff $schemaDiff, $relpath )
	{
		foreach( $this->getSetupPaths() as $abspath )
		{
			$filepath = $abspath . DIRECTORY_SEPARATOR . $relpath;

			if( !file_exists( $filepath ) ) {
				continue;
			}

			if( ( $list = include( $filepath ) ) === false ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Unable to get list from file "%1$s"', $filepath ) );
			}

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
				$tableDiff->removedColumns = [];
				$tableDiff->removedIndexes = [];
				$tableDiff->renamedIndexes = [];
			}

			$schemaDiff->removedSequences = [];
		}

		return $schemaDiff;
	}
}
