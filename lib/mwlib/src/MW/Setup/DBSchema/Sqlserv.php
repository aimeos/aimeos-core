<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 * @package MW
 * @subpackage Setup
 */


namespace Aimeos\MW\Setup\DBSchema;


/**
 * Implements querying the SQL server database
 *
 * @package MW
 * @subpackage Setup
 */
class Sqlserv extends \Aimeos\MW\Setup\DBSchema\InformationSchema
{
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
			SELECT name
			FROM sys.indexes
			WHERE object_id = OBJECT_ID( ? )
				AND name = ?
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
	 * Creates a new column item using the columns of the information_schema.columns.
	 *
	 * @param array $record Associative array with column details
	 * @return \Aimeos\MW\Setup\DBSchema\Column\Iface Column item
	 */
	protected function createColumnItem( array $record = [] )
	{
		$length = ( isset( $record['CHARACTER_MAXIMUM_LENGTH'] ) ? $record['CHARACTER_MAXIMUM_LENGTH'] : $record['NUMERIC_PRECISION'] );

		return new \Aimeos\MW\Setup\DBSchema\Column\Item( $record['TABLE_NAME'], $record['COLUMN_NAME'],
			$record['DATA_TYPE'], $length, $record['COLUMN_DEFAULT'], $record['IS_NULLABLE'], null );
	}
}
