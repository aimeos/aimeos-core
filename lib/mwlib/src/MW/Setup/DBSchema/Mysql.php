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
 * Implements querying the MySQL database.
 *
 * @package MW
 * @subpackage Setup
 */
class Mysql extends \Aimeos\MW\Setup\DBSchema\InformationSchema
{
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
			FROM INFORMATION_SCHEMA.STATISTICS
			WHERE TABLE_SCHEMA = ?
				AND TABLE_NAME = ?
				AND INDEX_NAME = ?
		";

		$stmt = $this->getConnection()->create( $sql );
		$stmt->bind( 1, $this->getDBName() );
		$stmt->bind( 2, $tablename );
		$stmt->bind( 3, $indexname );
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
	 * @param array $record Associative array with column details
	 * @return \Aimeos\MW\Setup\DBSchema\Column\Iface Column item
	 */
	protected function createColumnItem( array $record = [] )
	{
		switch( $record['DATA_TYPE'] )
		{
			case 'int': $type = 'integer'; break;
			default: $type = $record['DATA_TYPE'];
		}

		$length = ( isset( $record['CHARACTER_MAXIMUM_LENGTH'] ) ? $record['CHARACTER_MAXIMUM_LENGTH'] : $record['NUMERIC_PRECISION'] );

		return new \Aimeos\MW\Setup\DBSchema\Column\Item( $record['TABLE_NAME'], $record['COLUMN_NAME'],
			$type, $length, $record['COLUMN_DEFAULT'], $record['IS_NULLABLE'], $record['COLLATION_NAME'] );
	}
}
