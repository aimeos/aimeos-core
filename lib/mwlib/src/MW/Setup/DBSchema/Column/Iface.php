<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Setup
 */


namespace Aimeos\MW\Setup\DBSchema\Column;


/**
 * Interface for classes representing table columns and their details.
 *
 * @package MW
 * @subpackage Setup
 */
interface Iface
{
	/**
	 * Initializes the column object.
	 *
	 * @param string $tablename Name of the table the column belongs to
	 * @param string $name Name of the column
	 * @param string $type Type of the column
	 * @param int $length Length of the column if the column type is of variable length
	 * @param mixed $default Default value if not specified
	 * @param string $nullable "YES" if null values are allowed, "NO" if not
	 * @param string|null $charset Charset of the column
	 * @param string|null $collation Collation type of the column
	 */
	public function __construct( string $tablename, string $name, string $type, int $length, $default,
		string $nullable, string $charset = null, string $collation = null );

	/**
	 * Returns the charset of the column.
	 *
	 * @return string|null Charset of the column
	 */
	public function getCharset() : ?string;

	/**
	 * Returns the collation type of the column.
	 *
	 * @return string|null Collation type of the column
	 */
	public function getCollationType() : ?string;

	/**
	 * Returns the data type of the column.
	 *
	 * @return string Data type of the column
	 */
	public function getDataType() : string;

	/**
	 * Returns the default of the column.
	 *
	 * @return mixed Default of the column
	 */
	public function getDefaultValue();

	/**
	 * Returns the maximum length of the column.
	 *
	 * @return int Maximum length of the column
	 */
	public function getMaxLength() : int;

	/**
	 * Returns the name of the column.
	 *
	 * @return string Name of the column
	 */
	public function getName() : string;

	/**
	 * Returns the table name of the column.
	 *
	 * @return string Table name of the column
	 */
	public function getTableName() : string;

	/**
	 * Checks if NULL values are allowed for this column.
	 *
	 * @return bool True if NULL is allowed, false if not
	 */
	public function isNullable() : bool;
}
