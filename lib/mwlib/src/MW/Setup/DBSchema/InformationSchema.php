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
 * Implements querying the information_schema tables.
 *
 * @package MW
 * @subpackage Setup
 */
abstract class InformationSchema implements \Aimeos\MW\Setup\DBSchema\Iface
{
	private $rname;
	private $dbname;
	private $name;
	private $dbm;


	/**
	 * Initializes the database schema object
	 *
	 * @param \Aimeos\MW\DB\Manager\Iface $dbm Database manager
	 * @param string $rname Resource name
	 * @param string $dbname Database name
	 * @param string $name Adapter name
	 */
	public function __construct( \Aimeos\MW\DB\Manager\Iface $dbm, string $rname, string $dbname, string $name )
	{
		$this->rname = $rname;
		$this->dbname = $dbname;
		$this->name = $name;
		$this->dbm = $dbm;
	}


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
				AND TABLE_CATALOG = ?
				AND TABLE_NAME = ?
		";

		$conn = $this->acquire();

		$stmt = $conn->create( $sql );
		$stmt->bind( 1, $this->dbname );
		$stmt->bind( 2, $tablename );
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
		$sql = "
			SELECT SEQUENCE_NAME
			FROM INFORMATION_SCHEMA.SEQUENCES
			WHERE SEQUENCE_CATALOG = ?
				AND SEQUENCE_NAME = ?
		";

		$conn = $this->acquire();

		$stmt = $conn->create( $sql );
		$stmt->bind( 1, $this->dbname );
		$stmt->bind( 2, $seqname );
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
			WHERE TABLE_CATALOG = ?
				AND TABLE_NAME = ?
				AND CONSTRAINT_NAME = ?
		";

		$conn = $this->acquire();

		$stmt = $conn->create( $sql );
		$stmt->bind( 1, $this->dbname );
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
			WHERE TABLE_CATALOG = ?
				AND TABLE_NAME = ?
				AND COLUMN_NAME = ?
		";

		$conn = $this->acquire();

		$stmt = $conn->create( $sql );
		$stmt->bind( 1, $this->dbname );
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
			WHERE TABLE_CATALOG = ?
				AND TABLE_NAME = ?
				AND COLUMN_NAME = ?
		";

		$conn = $this->acquire();

		$stmt = $conn->create( $sql );
		$stmt->bind( 1, $this->dbname );
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
	 * Returns the database name.
	 *
	 * @return string Database name
	 */
	public function getDBName() : string
	{
		return $this->dbname;
	}


	/**
	 * Returns the name of the database adapter
	 *
	 * @return string Name of the adapter, e.g. 'mysql'
	 */
	public function getName() : string
	{
		return $this->name;
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
	 * Returns the database connection
	 *
	 * @return \Aimeos\MW\DB\Connection\Iface Database connection
	 */
	protected function acquire() : \Aimeos\MW\DB\Connection\Iface
	{
		return $this->dbm->acquire( $this->rname );
	}


	/**
	 * Releases the database connection
	 *
	 * @param \Aimeos\MW\DB\Connection\Iface $conn Database connection
	 */
	protected function release( \Aimeos\MW\DB\Connection\Iface $conn )
	{
		$this->dbm->release( $conn, $this->rname );
	}


	/**
	 * Creates a new column item using the columns of the information_schema.columns.
	 *
	 * @param array $record Associative array with TABLE_NAME, COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH,
	 * 	NUMERIC_PRECISION, COLUMN_DEFAULT, IS_NULLABLE
	 * @return \Aimeos\MW\Setup\DBSchema\Column\Iface Column item
	 */
	protected function createColumnItem( array $record = [] ) : \Aimeos\MW\Setup\DBSchema\Column\Iface
	{
		$length = ( isset( $record['CHARACTER_MAXIMUM_LENGTH'] ) ? $record['CHARACTER_MAXIMUM_LENGTH'] : $record['NUMERIC_PRECISION'] );
		return new \Aimeos\MW\Setup\DBSchema\Column\Item( $record['TABLE_NAME'], $record['COLUMN_NAME'], $record['DATA_TYPE'], $length,
			$record['COLUMN_DEFAULT'], $record['IS_NULLABLE'], $record['CHARACTER_SET_NAME'], $record['COLLATION_NAME'] );
	}
}
