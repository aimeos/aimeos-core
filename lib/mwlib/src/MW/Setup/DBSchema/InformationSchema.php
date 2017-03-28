<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Setup
 */


namespace Aimeos\MW\Setup\DBSchema;


/**
 * Implements querying the information_schema tables.
 *
 * @package MW
 * @subpackage Setup
 */
abstract class InformationSchema implements \Aimeos\MW\Setup\DBSchema\Iface
{
	private $conn;
	private $dbname;
	private $name;


	/**
	 * Initializes the database schema object.
	 *
	 * @param \Aimeos\MW\DB\Connection\Iface $conn Database connection
	 * @param string $dbname Database name
	 * @param string $name Adapter name
	 */
	public function __construct( \Aimeos\MW\DB\Connection\Iface $conn, $dbname, $name )
	{
		$this->conn = $conn;
		$this->dbname = $dbname;
		$this->name = $name;
	}


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
				AND TABLE_SCHEMA = ?
				AND TABLE_NAME = ?
		";

		$stmt = $this->conn->create( $sql );
		$stmt->bind( 1, $this->dbname );
		$stmt->bind( 2, $tablename );
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
			WHERE SEQUENCE_SCHEMA = ?
				AND SEQUENCE_NAME = ?
		";

		$stmt = $this->conn->create( $sql );
		$stmt->bind( 1, $this->dbname );
		$stmt->bind( 2, $seqname );
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
			WHERE TABLE_SCHEMA = ?
				AND TABLE_NAME = ?
				AND CONSTRAINT_NAME = ?
		";

		$stmt = $this->conn->create( $sql );
		$stmt->bind( 1, $this->dbname );
		$stmt->bind( 2, $tablename );
		$stmt->bind( 3, $constraintname );
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
			WHERE TABLE_SCHEMA = ?
				AND TABLE_NAME = ?
				AND COLUMN_NAME = ?
		";

		$stmt = $this->conn->create( $sql );
		$stmt->bind( 1, $this->dbname );
		$stmt->bind( 2, $tablename );
		$stmt->bind( 3, $columnname );
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
			WHERE TABLE_SCHEMA = ?
				AND TABLE_NAME = ?
				AND COLUMN_NAME = ?
		";

		$stmt = $this->conn->create( $sql );
		$stmt->bind( 1, $this->dbname );
		$stmt->bind( 2, $tablename );
		$stmt->bind( 3, $columnname );
		$result = $stmt->execute();

		if( ( $record = $result->fetch() ) === false ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Unknown column "%1$s" in table "%2$s"', $columnname, $tablename ) );
		}

		return $this->createColumnItem( $record );
	}


	/**
	 * Returns the database connection.
	 *
	 * @return \Aimeos\MW\DB\Connection\Iface Database connection
	 */
	protected function getConnection()
	{
		return $this->conn;
	}


	/**
	 * Returns the database name.
	 *
	 * @return string Database name
	 */
	public function getDBName()
	{
		return $this->dbname;
	}


	/**
	 * Returns the name of the database adapter
	 *
	 * @return string Name of the adapter, e.g. 'mysql'
	 */
	public function getName()
	{
		return $this->name;
	}


	/**
	 * Tests if something is supported
	 *
	 * @param string $what Type of object
	 * @return boolean True if supported, false if not
	 */
	public function supports( $what )
	{
		return false;
	}


	/**
	 * Creates a new column item using the columns of the information_schema.columns.
	 *
	 * @param array $record Associative array with TABLE_NAME, COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH,
	 * 	NUMERIC_PRECISION, COLUMN_DEFAULT, IS_NULLABLE
	 * @return \Aimeos\MW\Setup\DBSchema\Column\Iface Column item
	 */
	protected function createColumnItem( array $record = [] )
	{
		$length = ( isset( $record['CHARACTER_MAXIMUM_LENGTH'] ) ? $record['CHARACTER_MAXIMUM_LENGTH'] : $record['NUMERIC_PRECISION'] );
		return new \Aimeos\MW\Setup\DBSchema\Column\Item( $record['TABLE_NAME'], $record['COLUMN_NAME'], $record['DATA_TYPE'], $length,
			$record['COLUMN_DEFAULT'], $record['IS_NULLABLE'], $record['COLLATION_NAME'] );
	}
}
