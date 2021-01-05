<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
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
	 * Checks if the given constraint exists for the specified table in the database.
	 *
	 * @param string $tablename Name of the database table
	 * @param string $constraintname Name of the database table constraint
	 * @return bool True if the constraint exists, false if not
	 */
	public function constraintExists( string $tablename, string $constraintname ) : bool
	{
		if( ( $result = parent::constraintExists( $tablename, $constraintname ) ) === false )
		{
			$sql = "
				SELECT indexname
				FROM pg_indexes
				WHERE schemaname = 'public'
					AND tablename = ?
					AND indexname = ?
			";

			$conn = $this->acquire();

			$stmt = $conn->create( $sql );
			$stmt->bind( 1, $tablename );
			$stmt->bind( 2, $constraintname );
			$result = $stmt->execute()->fetch();

			$this->release( $conn );
		}

		return $result ? true : false;
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
			SELECT indexname
			FROM pg_indexes
			WHERE schemaname = 'public'
				AND tablename = ?
				AND indexname = ?
		";

		$conn = $this->acquire();

		$stmt = $conn->create( $sql );
		$stmt->bind( 1, $tablename );
		$stmt->bind( 2, $indexname );
		$result = $stmt->execute()->fetch();

		$this->release( $conn );

		return $result ? true : false;
	}


	/**
	 * Creates a new column item using the columns of the information_schema.columns.
	 *
	 * @param array $record Associative array with column details
	 * @return \Aimeos\MW\Setup\DBSchema\Column\Iface Column item
	 */
	protected function createColumnItem( array $record = [] ) : \Aimeos\MW\Setup\DBSchema\Column\Iface
	{
		switch( $record['data_type'] )
		{
			case 'character varying': $type = 'varchar'; break;
			default: $type = $record['data_type'];
		}

		$match = [];
		$length = ( isset( $record['character_maximum_length'] ) ? $record['character_maximum_length'] : $record['numeric_precision'] );
		$default = ( preg_match( '/^\'(.*)\'::.+$/', $record['column_default'], $match ) === 1 ? $match[1] : $record['column_default'] );

		return new \Aimeos\MW\Setup\DBSchema\Column\Item( $record['table_name'], $record['column_name'],
			$type, $length, $default, $record['is_nullable'], $record['character_set_name'], $record['collation_name'] );
	}
}
