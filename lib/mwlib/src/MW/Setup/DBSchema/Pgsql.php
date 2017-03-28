<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 * @package MW
 * @subpackage Setup
 */


namespace Aimeos\MW\Setup\DBSchema;


/**
 * Implements querying the PostgreSQL database
 *
 * @package MW
 * @subpackage Setup
 */
class Pgsql extends \Aimeos\MW\Setup\DBSchema\InformationSchema
{
	/**
	 * Checks if the given table exists in the database.
	 *
	 * @param string $tablename Name of the database table
	 * @return boolean True if the table exists, false if not
	 */
	public function tableExists( $tablename )
	{
		$sql = "
			SELECT TABLE_NAME
			FROM INFORMATION_SCHEMA.TABLES
			WHERE TABLE_TYPE = 'BASE TABLE'
				AND TABLE_SCHEMA = 'public'
				AND TABLE_NAME = ?
		";

		$stmt = $this->getConnection()->create( $sql );
		$stmt->bind( 1, $tablename );
		$result = $stmt->execute();

		if( $result->fetch() !== false ) {
			return true;
		}

		return false;
	}


	/**
	 * Checks if the given sequence exists in the database.
	 *
	 * @param string $seqname Name of the database sequence
	 * @return boolean True if the sequence exists, false if not
	 */
	public function sequenceExists( $seqname )
	{
		$sql = "
			SELECT SEQUENCE_NAME
			FROM INFORMATION_SCHEMA.SEQUENCES
			WHERE SEQUENCE_SCHEMA = 'public'
				AND SEQUENCE_NAME = ?
		";

		$stmt = $this->getConnection()->create( $sql );
		$stmt->bind( 1, $seqname );
		$result = $stmt->execute();

		if( $result->fetch() !== false ) {
			return true;
		}

		return false;
	}


	/**
	 * Checks if the given constraint exists for the specified table in the database.
	 *
	 * @param string $tablename Name of the database table
	 * @param string $constraintname Name of the database table constraint
	 * @return boolean True if the constraint exists, false if not
	 */
	public function constraintExists( $tablename, $constraintname )
	{
		$sql = "
			SELECT CONSTRAINT_NAME
			FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
			WHERE TABLE_SCHEMA = 'public'
				AND TABLE_NAME = ?
				AND CONSTRAINT_NAME = ?
		";

		$stmt = $this->getConnection()->create( $sql );
		$stmt->bind( 1, $tablename );
		$stmt->bind( 2, $constraintname );
		$result = $stmt->execute();

		if( $result->fetch() !== false ) {
			return true;
		}

		$sql = "
			SELECT indexname
			FROM pg_indexes
			WHERE schemaname = 'public'
				AND tablename = ?
				AND indexname = ?
		";

		$stmt = $this->getConnection()->create( $sql );
		$stmt->bind( 1, $tablename );
		$stmt->bind( 2, $constraintname );
		$result = $stmt->execute();

		if( $result->fetch() !== false ) {
			return true;
		}

		return false;
	}


	/**
	 * Checks if the given column exists for the specified table in the database.
	 *
	 * @param string $tablename Name of the database table
	 * @param string $columnname Name of the table column
	 * @return boolean True if the column exists, false if not
	 */
	public function columnExists( $tablename, $columnname )
	{
		$sql = "
			SELECT COLUMN_NAME
			FROM INFORMATION_SCHEMA.COLUMNS
			WHERE TABLE_SCHEMA = 'public'
				AND TABLE_NAME = ?
				AND COLUMN_NAME = ?
		";

		$stmt = $this->getConnection()->create( $sql );
		$stmt->bind( 1, $tablename );
		$stmt->bind( 2, $columnname );
		$result = $stmt->execute();

		if( $result->fetch() !== false ) {
			return true;
		}

		return false;
	}


	/**
	 * Checks if the given index (not foreign keys, primary or unique constraints) exists in the database.
	 *
	 * @param string $tablename Name of the database table
	 * @param string $indexname Name of the database index
	 * @return boolean True if the index exists, false if not
	 */
	public function indexExists( $tablename, $indexname )
	{
		$sql = "
			SELECT indexname
			FROM pg_indexes
			WHERE schemaname = 'public'
				AND tablename = ?
				AND indexname = ?
		";

		$stmt = $this->getConnection()->create( $sql );
		$stmt->bind( 1, $tablename );
		$stmt->bind( 2, $indexname );
		$result = $stmt->execute();

		if( $result->fetch() !== false ) {
			return true;
		}

		return false;
	}


	/**
	 * Returns an object containing the details of the column.
	 *
	 * @param string $tablename Name of the database table
	 * @param string $columnname Name of the table column
	 * @return \Aimeos\MW\Setup\DBSchema\Column\Iface Object which contains the details
	 */
	public function getColumnDetails( $tablename, $columnname )
	{
		$sql = "
			SELECT *
			FROM INFORMATION_SCHEMA.COLUMNS
			WHERE TABLE_SCHEMA = 'public'
				AND TABLE_NAME = ?
				AND COLUMN_NAME = ?
		";

		$stmt = $this->getConnection()->create( $sql );
		$stmt->bind( 1, $tablename );
		$stmt->bind( 2, $columnname );
		$result = $stmt->execute();

		if( ( $record = $result->fetch() ) === false ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Unknown column "%1$s" in table "%2$s"', $columnname, $tablename ) );
		}

		return $this->createColumnItem( $record );
	}


	/**
	 * Creates a new column item using the columns of the information_schema.columns.
	 *
	 * @param array $record Associative array with column details
	 * @return \Aimeos\MW\Setup\DBSchema\Column\Iface Column item
	 */
	protected function createColumnItem( array $record = [] )
	{
		switch( $record['data_type'] )
		{
			case 'character varying': $type = 'varchar'; break;
			default: $type = $record['data_type'];
		}

		$length = ( isset( $record['character_maximum_length'] ) ? $record['character_maximum_length'] : $record['numeric_precision'] );
		$default = ( preg_match( '/^\'(.*)\'::.+$/', $record['column_default'], $match ) === 1 ? $match[1] : $record['column_default'] );

		return new \Aimeos\MW\Setup\DBSchema\Column\Item( $record['table_name'], $record['column_name'],
			$type, $length, $default, $record['is_nullable'], $record['collation_name'] );
	}
}
