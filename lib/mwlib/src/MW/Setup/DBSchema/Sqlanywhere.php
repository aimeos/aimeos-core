<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
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
	 * @return bool True if the table exists, false if not
	 */
	public function tableExists( string $tablename ) : bool
	{
		$sql = "
			SELECT table_name
			FROM SYSTAB
			WHERE table_type = 1
				AND table_name = ?
		";

		$conn = $this->acquire();

		$stmt = $conn->create( $sql );
		$stmt->bind( 1, $tablename );
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
	 * Checks if the given index (not foreign keys, primary or unique constraints) exists in the database.
	 *
	 * @param string $tablename Name of the database table
	 * @param string $indexname Name of the database index
	 * @return bool True if the index exists, false if not
	 */
	public function indexExists( string $tablename, string $indexname ) : bool
	{
		$sql = "
			SELECT index_name
			FROM SYSIDX
			WHERE index_name = ?
		";

		$conn = $this->acquire();

		$stmt = $conn->create( $sql );
		$stmt->bind( 1, $indexname );
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
			SELECT constraint_name
			FROM SYSCONSTRAINT c
			WHERE constraint_name = ?
		";

		$conn = $this->acquire();

		$stmt = $conn->create( $sql );
		$stmt->bind( 1, $constraintname );
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
			SELECT column_name
			FROM SYSTABCOL c
			INNER JOIN SYS.ISYSTAB t ON c.table_id = t.table_id
			WHERE t.table_name = ?
				AND column_name = ?
		";

		$conn = $this->acquire();

		$stmt = $conn->create( $sql );
		$stmt->bind( 1, $tablename );
		$stmt->bind( 2, $columnname );
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
			SELECT t.table_name, c.*
			FROM SYSTABCOL c
			INNER JOIN SYS.ISYSTAB t ON c.table_id = t.table_id
			WHERE t.table_name = ?
				AND column_name = ?
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
	 * Creates a new column item using the columns of the information_schema.columns.
	 *
	 * @param array $record Associative array with column details
	 * @return \Aimeos\MW\Setup\DBSchema\Column\Iface Column item
	 */
	protected function createColumnItem( array $record = [] ) : \Aimeos\MW\Setup\DBSchema\Column\Iface
	{
		return new \Aimeos\MW\Setup\DBSchema\Column\Item( $record['table_name'], $record['column_name'],
			$record['base_type_str'], $record['width'], $record['default'], $record['nulls'], null, null );
	}
}
