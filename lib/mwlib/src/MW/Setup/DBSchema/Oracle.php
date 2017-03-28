<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 * @package MW
 * @subpackage Setup
 */


namespace Aimeos\MW\Setup\DBSchema;


/**
 * Implements querying the Oracle database
 *
 * @package MW
 * @subpackage Setup
 */
class Oracle extends \Aimeos\MW\Setup\DBSchema\InformationSchema
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
			FROM ALL_TABLES
			WHERE TABLE_NAME = ?
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
			FROM ALL_SEQUENCES
			WHERE SEQUENCE_NAME = ?
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
			FROM ALL_CONSTRAINTS
			WHERE TABLE_NAME = ?
				AND CONSTRAINT_NAME = ?
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
			FROM ALL_TAB_COLUMNS
			WHERE TABLE_NAME = ?
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
			SELECT INDEX_NAME
			FROM ALL_INDEXES
			WHERE TABLE_NAME = ?
				AND INDEX_NAME = ?
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
			FROM ALL_TAB_COLUMNS
			WHERE TABLE_NAME = ?
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
		$length = ( isset( $record['CHAR_COL_DECL_LENGTH'] ) ? $record['CHAR_COL_DECL_LENGTH'] : $record['DATA_PRECISION'] );

		return new \Aimeos\MW\Setup\DBSchema\Column\Item( $record['TABLE_NAME'], $record['COLUMN_NAME'],
			$record['DATA_TYPE'], $length, $record['DATA_DEFAULT'], $record['NULLABLE'], null );
	}
}
