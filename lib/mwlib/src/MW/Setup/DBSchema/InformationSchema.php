<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Setup
 */


/**
 * Implements querying the information_schema tables.
 *
 * @package MW
 * @subpackage Setup
 */
abstract class MW_Setup_DBSchema_InformationSchema implements MW_Setup_DBSchema_Interface
{
	private $conn;
	private $dbname = '';


	/**
	 * Initializes the database schema object.
	 *
	 * @param MW_DB_Connection_Interface $conn Database connection
	 * @param string $dbname Database name
	 */
	public function __construct( MW_DB_Connection_Interface $conn, $dbname )
	{
		$this->conn = $conn;
		$this->dbname = $dbname;
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
	 * @return MW_Setup_DBSchema_Column_Interface Object which contains the details
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
			throw new MW_Setup_Exception( sprintf( 'Unknown column "%1$s" in table "%2$s"', $tablename, $columnname ) );
		}

		return $this->createColumnItem( $record );
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
	 * Creates a new column item using the columns of the information_schema.columns.
	 *
	 * @param array $record Associative array with TABLE_NAME, COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH,
	 * 	NUMERIC_PRECISION, COLUMN_DEFAULT, IS_NULLABLE
	 * @return MW_Setup_DBSchema_Column_Interface Column item
	 */
	protected function createColumnItem( array $record = array() )
	{
		$length = ( isset( $record['CHARACTER_MAXIMUM_LENGTH'] ) ? $record['CHARACTER_MAXIMUM_LENGTH'] : $record['NUMERIC_PRECISION'] );
		return new MW_Setup_DBSchema_Column_Item( $record['TABLE_NAME'], $record['COLUMN_NAME'], $record['DATA_TYPE'], $length,
			$record['COLUMN_DEFAULT'], $record['IS_NULLABLE'], $record['COLLATION_NAME'] );
	}


	/**
	 * Returns the database connection.
	 *
	 * @return MW_DB_Connection_Interface Database connection
	 */
	protected function getConnection()
	{
		return $this->conn;
	}

}
