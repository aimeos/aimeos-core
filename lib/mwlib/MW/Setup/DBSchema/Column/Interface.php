<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @version $Id: Interface.php 16606 2012-10-19 12:50:23Z nsendetzky $
 * @package MW
 * @subpackage Setup
 */


/**
 * Interface for classes representing table columns and their details.
 *
 * @package MW
 * @subpackage Setup
 */
interface MW_Setup_DBSchema_Column_Interface
{
	/**
	 * Initializes the column object.
	 *
	 * @param string $tablename Name of the table the column belongs to
	 * @param string $name Name of the column
	 * @param string $type Type of the column
	 * @param integer $length Length of the column if the column type is of variable length
	 * @param string $default Default value if not specified
	 * @param boolean $nullable If NULL values are allowed
	 * @param string $collation collation type of the column
	 */
	public function __construct( $tablename, $name, $type, $length, $default, $nullable, $collation );

	/**
	 * Returns the collation type of the column.
	 *
	 * @return string collation type of the column
	 */
	public function getCollationType();

	/**
	 * Returns the data type of the column.
	 *
	 * @return string Data type of the column
	 */
	public function getDataType();

	/**
	 * Returns the default of the column.
	 *
	 * @return mixed Default of the column
	 */
	public function getDefaultValue();

	/**
	 * Returns the maximum length of the column.
	 *
	 * @return integer Maximum length of the column
	 */
	public function getMaxLength();

	/**
	 * Returns the name of the column.
	 *
	 * @return string Name of the column
	 */
	public function getName();

	/**
	 * Returns the table name of the column.
	 *
	 * @return string Table name of the column
	 */
	public function getTableName();

	/**
	 * Checks if NULL values are allowed for this column.
	 *
	 * @return bool True if NULL is allowed, false if not
	 */
	public function isNullable();
}
