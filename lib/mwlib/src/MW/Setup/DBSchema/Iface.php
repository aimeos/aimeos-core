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
 * Common interface for all classes implementing access to the database schema.
 *
 * @package MW
 * @subpackage Setup
 */
interface Iface
{
	const HAS_SEQUENCES = 'seqence';

	/**
	 * Initializes the database schema object.
	 *
	 * @param \Aimeos\MW\DB\Connection\Iface $conn Database connection
	 * @param string $dbname Database name
	 * @param string $name Adapter name
	 * @return null
	 */
	public function __construct( \Aimeos\MW\DB\Connection\Iface $conn, $dbname, $name );

	/**
	 * Checks if the given table exists for the specified table in the database.
	 *
	 * @param string $tablename Name of the database table
	 * @return boolean True if the table exists, false if not
	 */
	public function tableExists( $tablename );

	/**
	 * Checks if the given sequence exists in the database.
	 *
	 * @param string $seqname Name of the database sequence
	 * @return boolean True if the sequence exists, false if not
	 */
	public function sequenceExists( $seqname );

	/**
	 * Checks if the given index (not foreign keys, primary or unique constraints) exists in the database.
	 *
	 * @param string $tablename Name of the database table
	 * @param string $indexname Name of the database index
	 * @return boolean True if the index exists, false if not
	 */
	public function indexExists( $tablename, $indexname );

	/**
	 * Checks if the given constraint (foreign key, primary, unique) exists for the specified table in the database.
	 *
	 * @param string $tablename Name of the database table
	 * @param string $constraintname Name of the database table constraint
	 * @return boolean True if the constraint exists, false if not
	 */
	public function constraintExists( $tablename, $constraintname );

	/**
	 * Checks if the given column exists for the specified table in the database.
	 *
	 * @param string $tablename Name of the database table
	 * @param string $columnname Name of the table column
	 * @return boolean True if the column exists, false if not
	 */
	public function columnExists( $tablename, $columnname );

	/**
	 * Returns an object containing the details of the column.
	 *
	 * @param string $tablename Name of the database table
	 * @param string $columnname Name of the table column
	 * @return \Aimeos\MW\Setup\DBSchema\Column\Iface Object which contains the details
	 */
	public function getColumnDetails( $tablename, $columnname );

	/**
	 * Returns the database name.
	 *
	 * @return string Database name
	 */
	public function getDBName();

	/**
	 * Returns the name of the database adapter
	 *
	 * @return string Name of the adapter, e.g. 'mysql'
	 */
	public function getName();

	/**
	 * Tests if something is supported
	 *
	 * @param string $what Type of object
	 * @return boolean True if supported, false if not
	 */
	public function supports( $what );
}
