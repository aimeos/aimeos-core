<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 * @package MW
 * @subpackage Setup
 */


namespace Aimeos\MW\Setup\DBSchema;


/**
 * Implements querying the SQL Anywhere database
 *
 * @package MW
 * @subpackage Setup
 */
class Sqlanywhere extends \Aimeos\MW\Setup\DBSchema\InformationSchema
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
			SELECT table_name
			FROM SYSTAB
			WHERE table_type = 1
				AND table_name = ?
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
			SELECT index_name
			FROM SYSIDX
			WHERE index_name = ?
		";

		$stmt = $this->getConnection()->create( $sql );
		$stmt->bind( 1, $indexname );
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
			SELECT constraint_name
			FROM SYSCONSTRAINT c
			WHERE constraint_name = ?
		";

		$stmt = $this->getConnection()->create( $sql );
		$stmt->bind( 1, $constraintname );
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
			SELECT column_name
			FROM SYSTABCOL c
			INNER JOIN SYS.ISYSTAB t ON c.table_id = t.table_id
			WHERE t.table_name = ?
				AND column_name = ?
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
	 * Returns an object containing the details of the column.
	 *
	 * @param string $tablename Name of the database table
	 * @param string $columnname Name of the table column
	 * @return \Aimeos\MW\Setup\DBSchema\Column\Iface Object which contains the details
	 */
	public function getColumnDetails( $tablename, $columnname )
	{
		$sql = "
			SELECT t.table_name, c.*
			FROM SYSTABCOL c
			INNER JOIN SYS.ISYSTAB t ON c.table_id = t.table_id
			WHERE t.table_name = ?
				AND column_name = ?
		";

		$stmt = $this->getConnection()->create( $sql );
		$stmt->bind( 1, $this->getDBName() );
		$stmt->bind( 2, $tablename );
		$stmt->bind( 3, $columnname );
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
		return new \Aimeos\MW\Setup\DBSchema\Column\Item( $record['table_name'], $record['column_name'],
			$record['base_type_str'], $record['width'], $record['default'], $record['nulls'], null );
	}
}
