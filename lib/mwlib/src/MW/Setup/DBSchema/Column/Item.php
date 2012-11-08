<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @version $Id: Item.php 16606 2012-10-19 12:50:23Z nsendetzky $
 * @package MW
 * @subpackage Setup
 */


/**
 * Default class representing table columns and their details.
 *
 * @package MW
 * @subpackage Setup
 */
class MW_Setup_DBSchema_Column_Item implements MW_Setup_DBSchema_Column_Interface
{
	private $_tablename = '';
	private $_name = '';
	private $_type;
	private $_length = 0;
	private $_default;
	private $_nullable = false;
	private $_collation = '';


	/**
	 * Initializes the column object.
	 *
	 * @param string $tablename Name of the table the column belongs to
	 * @param string $name Name of the column
	 * @param string $type Type of the column
	 * @param integer $length Length of the column if the column type is of variable length
	 * @param string $default Default value if not specified
	 * @param boolean $nullable If null values are allowed
	 */
	public function __construct( $tablename, $name, $type, $length, $default, $nullable, $collation )
	{
		$this->_tablename = (string) $tablename;
		$this->_name = (string) $name;
		$this->_type = (string) $type;
		$this->_length = (int) $length;
		$this->_default = $default;
		$this->_collation = (string) $collation;

		switch( $nullable )
		{
			case 'YES':
				$this->_nullable = true; break;
			case 'NO':
				$this->_nullable = false; break;
			default:
				throw new MW_Setup_Exception( sprintf( 'Invalid value for allowing null: "%1$s', $nullable ) );
		}
	}


	/**
	 * Returns the collation type of the column.
	 *
	 * @return string collation type of the column
	 */
	public function getCollationType()
	{
		return $this->_collation;
	}


	/**
	 * Returns the data type of the column.
	 *
	 * @return string Data type of the column
	 */
	public function getDataType()
	{
		return $this->_type;
	}


	/**
	 * Returns the default of the column.
	 *
	 * @return mixed Default of the column
	 */
	public function getDefaultValue()
	{
		return $this->_default;
	}


	/**
	 * Returns the maximum length of the column.
	 *
	 * @return integer Maximum length of the column
	 */
	public function getMaxLength()
	{
		return $this->_length;
	}


	/**
	 * Returns the name of the column.
	 *
	 * @return string Name of the column
	 */
	public function getName()
	{
		return $this->_name;
	}


	/**
	 * Returns the table name of the column.
	 *
	 * @return string Table name of the column
	 */
	public function getTableName()
	{
		return $this->_tablename;
	}


	/**
	 * Checks if null values are allowed for this column.
	 *
	 * @return bool True if null is allowed, false if not
	 */
	public function isNullable()
	{
		return $this->_nullable;
	}
}
