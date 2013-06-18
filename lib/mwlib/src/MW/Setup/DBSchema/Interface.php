<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Setup
 */


/**
 * Common interface for all classes implementing access to the database schema.
 *
 * @package MW
 * @subpackage Setup
 */
interface MW_Setup_DBSchema_Interface
{
	/**
	 * Initializes the database schema object.
	 *
	 * @param MW_DB_Connection_Interface $conn Database connection
	 * @param string $dbname Database name
	 */
	public function __construct( MW_DB_Connection_Interface $conn, $dbname );

	/**
	 * Checks if the given table exists for the specified table in the database.
	 *
	 * @param string $tablename Name of the database table
	 * @return boolean True if the table exists, false if not
	 */
	public function tableExists( $tablename );

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
	 * @return MW_Setup_DBSchema_Column_Interface Object which contains the details
	 */
	public function getColumnDetails( $tablename, $columnname );
	
	/**
	 * Returns the database name.
	 *
	 * @return string Database name
	 */
	public function getDBName( );
}
