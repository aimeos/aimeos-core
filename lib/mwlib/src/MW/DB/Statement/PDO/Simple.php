<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage DB
 */


/**
 * Database statement class for simple PDO statements.
 *
 * @package MW
 * @subpackage DB
 */
class MW_DB_Statement_PDO_Simple extends MW_DB_Statement_Abstract implements MW_DB_Statement_Interface
{
	private $_conn = null;
	private $_binds = array();
	private $_sql = '';
	private $_stmt = null;
	private $_parts = array();


	/**
	 * Initializes the statement object.
	 *
	 * @param PDO $conn PDO database connection object
	 * @param string $sql SQL statement string
	 */
	public function __construct( PDO $conn, $sql )
	{
		$this->_conn = $conn;
		$this->_sql = $sql;

		$parts = explode( '?', $sql );

		if( ( $part = reset( $parts ) ) !== false )
		{
			do
			{
				$count = 0; $temp = $part;
				while( ( $count += substr_count( $part, '\'' ) ) % 2 !== 0 )
				{
					if( ( $part = next( $parts ) ) === false ) {
						throw new MW_DB_Exception( 'Number of apostrophes don\'t match' );
					}
					$temp .= '?' . $part;
				}
				$this->_parts[] = $temp;
			}
			while( ( $part = next( $parts ) ) !== false );
		}
	}


	/**
	 * Binds a value to a parameter in the statement.
	 *
	 * @param integer $position Position index of the placeholder
	 * @param mixed $value Value which should be bound to the placeholder
	 * @param integer $type Type of given value defined in MW_DB_Statement_Abstract as constant
	 */
	public function bind( $position, $value, $type = MW_DB_Statement_Abstract::PARAM_STR )
	{
		if( is_null( $value ) ) {
			$this->_binds[$position] = 'NULL'; return;
		}

		switch( $type )
		{
			case MW_DB_Statement_Abstract::PARAM_NULL:
				$this->_binds[$position] = 'NULL'; break;
			case MW_DB_Statement_Abstract::PARAM_BOOL:
				$this->_binds[$position] = (int) (bool) $value; break;
			case MW_DB_Statement_Abstract::PARAM_INT:
				$this->_binds[$position] = (int) $value; break;
			case MW_DB_Statement_Abstract::PARAM_FLOAT:
				$this->_binds[$position] = (float) $value; break;
			case MW_DB_Statement_Abstract::PARAM_STR:
				// PDO quote isn't available for ODBC driver
				$value = str_replace( '\'', '\'\'', str_replace( '\\', '\\\\', $value ) );
				$this->_binds[$position] = '\'' . $value . '\''; break;
			default:
				$this->_binds[$position] = $value; break;
		}

		$this->_stmt = null;
	}


	/**
	 * Executes the statement.
	 *
	 * @return MW_DB_Result_Interface Result object
	 * @throws MW_DB_Exception If an error occured in the unterlying driver or if the number of binds doesn't match
	 */
	public function execute()
	{
		if( count( $this->_binds ) !== count( $this->_parts ) - 1 ) {
			throw new MW_DB_Exception( sprintf( 'Number of binds (%1$d) doesn\'t match the number of markers in "%2$s"', count( $this->_binds ), $this->_sql ) );
		}

		$sql = $this->_buildSQL();

		try {
			return new MW_DB_Result_PDO( $this->_conn->query( $sql ) );
		} catch ( PDOException $pe ) {
			throw new MW_DB_Exception( sprintf( 'Executing statement "%1$s" failed: ', $sql ) . $pe->getMessage(), $pe->getCode(), $pe->errorInfo );
		}
	}


	/**
	 * Returns the SQL string as sent to the database (magic PHP method)
	 *
	 * @return string SQL statement
	 */
	public function __toString()
	{
		return $this->_buildSQL();
	}


	/**
	 * Creates the SQL string with bound parameters.
	 *
	 * @return string SQL statement
	 */
	protected function _buildSQL()
	{
		if( $this->_stmt !== null ) {
			return $this->_stmt;
		}

		$i = 1;
		foreach( $this->_parts as $part )
		{
			$this->_stmt .= $part;
			if( isset( $this->_binds[$i] ) ) {
				$this->_stmt .= $this->_binds[$i];
			}
			$i++;
		}

		return $this->_stmt;
	}
}
