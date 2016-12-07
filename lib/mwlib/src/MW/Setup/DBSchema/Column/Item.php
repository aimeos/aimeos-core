<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Setup
 */


namespace Aimeos\MW\Setup\DBSchema\Column;


/**
 * Default class representing table columns and their details.
 *
 * @package MW
 * @subpackage Setup
 */
class Item implements \Aimeos\MW\Setup\DBSchema\Column\Iface
{
	private $tablename = '';
	private $name = '';
	private $type;
	private $length = 0;
	private $default;
	private $nullable = false;
	private $collation = '';


	/**
	 * Initializes the column object.
	 *
	 * @param string $tablename Name of the table the column belongs to
	 * @param string $name Name of the column
	 * @param string $type Type of the column
	 * @param integer $length Length of the column if the column type is of variable length
	 * @param string $default Default value if not specified
	 * @param string $nullable "YES" if null values are allowed, "NO" if not
	 * @param string $collation Used collation for text type columns
	 */
	public function __construct( $tablename, $name, $type, $length, $default, $nullable, $collation )
	{
		$this->tablename = (string) $tablename;
		$this->name = (string) $name;
		$this->type = (string) $type;
		$this->length = (int) $length;
		$this->default = $default;
		$this->collation = (string) $collation;

		switch( strtoupper( $nullable ) )
		{
			case 'YES':
			case 'Y':
			case 'T':
			case '1':
				$this->nullable = true; break;
			case 'NO':
			case 'N':
			case 'F':
			case '0':
				$this->nullable = false; break;
			default:
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Invalid value for allowing null: "%1$s', $nullable ) );
		}
	}


	/**
	 * Returns the collation type of the column.
	 *
	 * @return string collation type of the column
	 */
	public function getCollationType()
	{
		return $this->collation;
	}


	/**
	 * Returns the data type of the column.
	 *
	 * @return string Data type of the column
	 */
	public function getDataType()
	{
		return $this->type;
	}


	/**
	 * Returns the default of the column.
	 *
	 * @return string Default of the column
	 */
	public function getDefaultValue()
	{
		return $this->default;
	}


	/**
	 * Returns the maximum length of the column.
	 *
	 * @return integer Maximum length of the column
	 */
	public function getMaxLength()
	{
		return $this->length;
	}


	/**
	 * Returns the name of the column.
	 *
	 * @return string Name of the column
	 */
	public function getName()
	{
		return $this->name;
	}


	/**
	 * Returns the table name of the column.
	 *
	 * @return string Table name of the column
	 */
	public function getTableName()
	{
		return $this->tablename;
	}


	/**
	 * Checks if null values are allowed for this column.
	 *
	 * @return bool True if null is allowed, false if not
	 */
	public function isNullable()
	{
		return $this->nullable;
	}
}
