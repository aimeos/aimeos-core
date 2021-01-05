<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Setup
 */


namespace Aimeos\MW\Setup\DBSchema;


/**
 * Implements querying the MySQL database.
 *
 * @package MW
 * @subpackage Setup
 */
class Mysql extends \Aimeos\MW\Setup\DBSchema\InformationSchema
{
	/**
	 * Checks if the given table exists in the database.
	 *
	 * @param string $tablename Name of the database table
	 * @return bool True if the table exists, false if not
	 */
	public function tableExists( string $tablename ) : bool
	{
		$sql = "
			SELECT TABLE_NAME
			FROM INFORMATION_SCHEMA.TABLES
			WHERE TABLE_TYPE = 'BASE TABLE'
				AND TABLE_SCHEMA = ?
				AND TABLE_NAME = ?
		";

		$conn = $this->acquire();

		$stmt = $conn->create( $sql );
		$stmt->bind( 1, $this->getDBName() );
		$stmt->bind( 2, $tablename );
		$result = $stmt->execute()->fetch();

		$this->release( $conn );

		return $result ? true : false;
	}


	/**
	 * Checks if the given constraint exists for the specified table in the database.
	 *
	 * @param string $tablename Name of the database table
	 * @param string $constraintname Name of the database table constraint
	 * @return bool True if the constraint exists, false if not
	 */
	public function constraintExists( string $tablename, string $constraintname ) : bool
	{
		$sql = "
			SELECT CONSTRAINT_NAME
			FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
			WHERE TABLE_SCHEMA = ?
				AND TABLE_NAME = ?
				AND CONSTRAINT_NAME = ?
		";

		$conn = $this->acquire();

		$stmt = $conn->create( $sql );
		$stmt->bind( 1, $this->getDBName() );
		$stmt->bind( 2, $tablename );
		$stmt->bind( 3, $constraintname );
		$result = $stmt->execute()->fetch();

		$this->release( $conn );

		return $result ? true : false;
	}


	/**
	 * Checks if the given column exists for the specified table in the database.
	 *
	 * @param string $tablename Name of the database table
	 * @param string $columnname Name of the table column
	 * @return bool True if the column exists, false if not
	 */
	public function columnExists( string $tablename, string $columnname ) : bool
	{
		$sql = "
			SELECT COLUMN_NAME
			FROM INFORMATION_SCHEMA.COLUMNS
			WHERE TABLE_SCHEMA = ?
				AND TABLE_NAME = ?
				AND COLUMN_NAME = ?
		";

		$conn = $this->acquire();

		$stmt = $conn->create( $sql );
		$stmt->bind( 1, $this->getDBName() );
		$stmt->bind( 2, $tablename );
		$stmt->bind( 3, $columnname );
		$result = $stmt->execute()->fetch();

		$this->release( $conn );

		return $result ? true : false;
	}


	/**
	 * Returns an object containing the details of the column.
	 *
	 * @param string $tablename Name of the database table
	 * @param string $columnname Name of the table column
	 * @return \Aimeos\MW\Setup\DBSchema\Column\Iface Object which contains the details
	 */
	public function getColumnDetails( string $tablename, string $columnname ) : \Aimeos\MW\Setup\DBSchema\Column\Iface
	{
		$sql = "
			SELECT *
			FROM INFORMATION_SCHEMA.COLUMNS
			WHERE TABLE_SCHEMA = ?
				AND TABLE_NAME = ?
				AND COLUMN_NAME = ?
		";

		$conn = $this->acquire();

		$stmt = $conn->create( $sql );
		$stmt->bind( 1, $this->getDBName() );
		$stmt->bind( 2, $tablename );
		$stmt->bind( 3, $columnname );
		$result = $stmt->execute()->fetch();

		$this->release( $conn );

		if( $result === null ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Unknown column "%1$s" in table "%2$s"', $columnname, $tablename ) );
		}

		return $this->createColumnItem( $result );
	}


	/**
	 * Checks if the given index (not foreign keys, primary or unique constraints) exists in the database.
	 *
	 * @param string $tablename Name of the database table
	 * @param string $indexname Name of the database index
	 * @return bool True if the index exists, false if not
	 */
	public function indexExists( string $tablename, string $indexname ) : bool
	{
		$sql = "
			SELECT INDEX_NAME
			FROM INFORMATION_SCHEMA.STATISTICS
			WHERE TABLE_SCHEMA = ?
				AND TABLE_NAME = ?
				AND INDEX_NAME = ?
		";

		$conn = $this->acquire();

		$stmt = $conn->create( $sql );
		$stmt->bind( 1, $this->getDBName() );
		$stmt->bind( 2, $tablename );
		$stmt->bind( 3, $indexname );
		$result = $stmt->execute()->fetch();

		$this->release( $conn );

		return $result ? true : false;
	}


	/**
	 * Checks if the given sequence exists in the database.
	 *
	 * @param string $seqname Name of the database sequence
	 * @return bool True if the sequence exists, false if not
	 */
	public function sequenceExists( string $seqname ) : bool
	{
		return false;
	}


	/**
	 * Tests if something is supported
	 *
	 * @param string $what Type of object
	 * @return bool True if supported, false if not
	 */
	public function supports( string $what ) : bool
	{
		return false;
	}


	/**
	 * Creates a new column item using the columns of the information_schema.columns.
	 *
	 * @param array $record Associative array with column details
	 * @return \Aimeos\MW\Setup\DBSchema\Column\Iface Column item
	 */
	protected function createColumnItem( array $record = [] ) : \Aimeos\MW\Setup\DBSchema\Column\Iface
	{
		switch( $record['DATA_TYPE'] )
		{
			case 'int': $type = 'integer'; break;
			default: $type = $record['DATA_TYPE'];
		}

		$length = ( isset( $record['CHARACTER_MAXIMUM_LENGTH'] ) ? $record['CHARACTER_MAXIMUM_LENGTH'] : $record['NUMERIC_PRECISION'] );
		$default = is_string( $record['COLUMN_DEFAULT'] ) ? trim( $record['COLUMN_DEFAULT'], '\'' ) : $record['COLUMN_DEFAULT'];
		$default = $default !== 'NULL' ? $default : null; // MariaDB workarounds for default values

		return new \Aimeos\MW\Setup\DBSchema\Column\Item( $record['TABLE_NAME'], $record['COLUMN_NAME'], $type, $length,
			$default, $record['IS_NULLABLE'], $record['CHARACTER_SET_NAME'], $record['COLLATION_NAME'] );
	}
}
