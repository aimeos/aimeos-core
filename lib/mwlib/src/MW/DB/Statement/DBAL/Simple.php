<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 * @package MW
 * @subpackage DB
 */


namespace Aimeos\MW\DB\Statement\DBAL;


/**
 * Database statement class for simple DBAL statements
 *
 * @package MW
 * @subpackage DB
 */
class Simple extends \Aimeos\MW\DB\Statement\Base implements \Aimeos\MW\DB\Statement\Iface
{
	private $binds = [];
	private $conn;
	private $parts;
	private $sql;


	/**
	 * Initializes the statement object.
	 *
	 * @param \Doctrine\DBAL\Connection $conn DBAL database connection object
	 * @param string $sql SQL statement string
	 */
	public function __construct( \Doctrine\DBAL\Connection $conn, $sql )
	{
		$this->parts = $this->getSqlParts( $sql );
		$this->conn = $conn;
		$this->sql = $sql;
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
				$this->binds[$position] = (int) (bool) $value; break;
			case \Aimeos\MW\DB\Statement\Base::PARAM_INT:
				$this->binds[$position] = (int) $value; break;
			case \Aimeos\MW\DB\Statement\Base::PARAM_FLOAT:
				$this->binds[$position] = (float) $value; break;
			default:
				$this->binds[$position] = $this->conn->quote( $value ); break;
		}
	}


	/**
	 * Executes the statement.
	 *
	 * @return \Aimeos\MW\DB\Result\Iface Result object
	 * @throws \Aimeos\MW\DB\Exception If an error occured in the unterlying driver or if the number of binds doesn't match
	 */
	public function execute()
	{
		if( count( $this->binds ) !== count( $this->parts ) - 1 ) {
			throw new \Aimeos\MW\DB\Exception( sprintf( 'Number of binds (%1$d) doesn\'t match the number of markers in "%2$s"', count( $this->binds ), $this->sql ) );
		}

		$sql = $this->buildSQL( $this->parts, $this->binds );

		try {
			return new \Aimeos\MW\DB\Result\DBAL( $this->conn->query( $sql ) );
		} catch ( \Doctrine\DBAL\DBALException $p ) {
			throw new \Aimeos\MW\DB\Exception( sprintf( 'Executing statement "%1$s" failed: ', $sql ) . $p->getMessage(), $p->getCode() );
		}
	}


	/**
	 * Returns the SQL string as sent to the database (magic PHP method)
	 *
	 * @return string SQL statement
	 */
	public function __toString()
	{
		return $this->buildSQL( $this->parts, $this->binds );
	}
}
