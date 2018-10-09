<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MW
 * @subpackage DB
 */


namespace Aimeos\MW\DB\Statement\PDO;


/**
 * Database statement class for simple PDO statements
 *
 * @package MW
 * @subpackage DB
 */
class Simple extends \Aimeos\MW\DB\Statement\Base implements \Aimeos\MW\DB\Statement\Iface
{
	private $binds = [];
	private $parts;
	private $sql;


	/**
	 * Initializes the statement object
	 *
	 * @param \Aimeos\MW\DB\Connection\PDO $conn Database connection object
	 * @param string $sql SQL statement
	 */
	public function __construct( \Aimeos\MW\DB\Connection\PDO $conn, $sql )
	{
		parent::__construct( $conn );

		$this->parts = $this->getSqlParts( $sql );
	}


	/**
	 * Binds a value to a parameter in the statement.
	 *
	 * @param integer $position Position index of the placeholder
	 * @param mixed $value Value which should be bound to the placeholder
	 * @param integer $type Type of given value defined in \Aimeos\MW\DB\Statement\Base as constant
	 */
	public function bind( $position, $value, $type = \Aimeos\MW\DB\Statement\Base::PARAM_STR )
	{
		if( is_null( $value ) ) {
			$this->binds[$position] = 'NULL'; return;
		}

		switch( $type )
		{
			case \Aimeos\MW\DB\Statement\Base::PARAM_NULL:
				$this->binds[$position] = 'NULL'; break;
			case \Aimeos\MW\DB\Statement\Base::PARAM_BOOL:
				$this->binds[$position] = ((bool) $value ? 'TRUE' : 'FALSE'); break;
			case \Aimeos\MW\DB\Statement\Base::PARAM_INT:
				$this->binds[$position] = (int) $value; break;
			case \Aimeos\MW\DB\Statement\Base::PARAM_FLOAT:
				$this->binds[$position] = (float) $value; break;
			case \Aimeos\MW\DB\Statement\Base::PARAM_STR:
				$this->binds[$position] = $this->getConnection()->getRawObject()->quote( $value ); break;
			default:
				throw new \Aimeos\MW\DB\Exception( sprintf( 'Invalid parameter type "%1$s"', $type ) );
		}

		$this->sql = null;
	}


	/**
	 * Executes the statement.
	 *
	 * @return \Aimeos\MW\DB\Result\Iface Result object
	 * @throws \Aimeos\MW\DB\Exception If an error occured in the unterlying driver or if the number of binds doesn't match
	 */
	public function execute()
	{
		if( count( $this->binds ) !== count( $this->parts ) - 1 )
		{
			$msg = 'Number of binds (%1$d) doesn\'t match the number of markers in "%2$s"';
			throw new \Aimeos\MW\DB\Exception( sprintf( $msg, count( $this->binds ), implode( '?', $this->parts ) ) );
		}

		try {
			$result = $this->exec();
		} catch( \PDOException $e ) {
			throw new \Aimeos\MW\DB\Exception( $e->getMessage(), $e->getCode() );
		}

		return new \Aimeos\MW\DB\Result\PDO( $result );
	}


	/**
	 * Returns the SQL string as sent to the database (magic PHP method)
	 *
	 * @return string SQL statement
	 */
	public function __toString()
	{
		if( $this->sql === null ) {
			$this->sql = $this->buildSQL( $this->parts, $this->binds );
		}

		return $this->sql;
	}


	/**
	 * Binds the parameters and executes the SQL statment
	 *
	 * @return \PDOStatement Executed PDO statement
	 */
	protected function exec()
	{
		if( $this->sql === null ) {
			$this->sql = $this->buildSQL( $this->parts, $this->binds );
		}

		$level = error_reporting(); // Workaround for PDO warnings

		try
		{
			error_reporting( $level & ~E_WARNING );
			$result = $this->getConnection()->getRawObject()->query( $this->sql );
		}
		catch( \PDOException $e )
		{
			error_reporting( $level );

			// recover from lost connection (MySQL)
			if( !isset( $e->errorInfo[1] ) || $e->errorInfo[1] != 2006 || $conn->inTransaction() === true ) {
				throw $e;
			}

			$conn->connect();
			return $this->exec();
		}

		error_reporting( $level );

		return $result;
	}
}
