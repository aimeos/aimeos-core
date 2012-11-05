<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @version $Id: Mysql.php 16606 2012-10-19 12:50:23Z nsendetzky $
 * @package MW
 * @subpackage Setup
 */


/**
 * Implements querying the MySQL database.
 *
 * @package MW
 * @subpackage Setup
 */
class MW_Setup_DBSchema_Mysql extends MW_Setup_DBSchema_InformationSchema
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

		$stmt = $this->_getConnection()->create( $sql );
		$stmt->bind( 1, $this->getDBName() );
		$stmt->bind( 2, $tablename );
		$stmt->bind( 3, $indexname );
		$result = $stmt->execute();

		if( $result->fetch() !== false ) {
			return true;
		}

		return false;
	}
}
