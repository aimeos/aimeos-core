<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage DB
 */


namespace Aimeos\MW\DB\Statement\PDO;


/**
 * Database statement class for prepared PDO statements.
 *
 * @package MW
 * @subpackage DB
 */
class Prepared extends \Aimeos\MW\DB\Statement\Base implements \Aimeos\MW\DB\Statement\Iface
{
	private $binds = [];
	private $sql;


	/**
	 * Initializes the statement object
	 *
	 * @param \Aimeos\MW\DB\Connection\PDO $conn Database connection object
	 * @param string $sql SQL statement
	 */
	public function __construct( \Aimeos\MW\DB\Connection\PDO $conn, string $sql )
	{
		parent::__construct( $conn );
		$this->sql = $sql;
	}


	/**
	 * Returns the SQL string as sent to the database (magic PHP method)
	 *
	 * @return string SQL statement
	 */
	public function __toString()
	{
		return $this->sql . ":\n" . print_r( array_column( $this->binds, 0 ), true );
	}


	/**
	 * Binds a value to a parameter in the statement
	 *
	 * @param int $position Position index of the placeholder
	 * @param mixed $value Value which should be bound to the placeholder
	 * @param int $type Type of given value defined in \Aimeos\MW\DB\Statement\Base as constant
	 * @return \Aimeos\MW\DB\Statement\Iface Statement instance for method chaining
	 * @throws \Aimeos\MW\DB\Exception If an error occured in the unterlying driver
	 */
	public function bind( int $position, $value, int $type = \Aimeos\MW\DB\Statement\Base::PARAM_STR ) : \Aimeos\MW\DB\Statement\Iface
	{
		$this->binds[$position] = [$value, $type];
		return $this;
	}


	/**
	 * Executes the statement
	 *
	 * @return \Aimeos\MW\DB\Result\Iface Result object
	 * @throws \Aimeos\MW\DB\Exception If an error occured in the unterlying driver
	 */
	public function execute() : \Aimeos\MW\DB\Result\Iface
	{
		try {
			$stmt = $this->exec();
		} catch( \PDOException $e ) {
			throw new \Aimeos\MW\DB\Exception( $e->getMessage() . ': ' . $this->sql . map( $this->binds )->col( 0 )->toJson(), $e->getCode(), $e->errorInfo );
		}

		return new \Aimeos\MW\DB\Result\PDO( $stmt );
	}


	/**
	 * Binds the parameters and executes the SQL statment
	 *
	 * @return \PDOStatement Executed PDO statement
	 */
	protected function exec() : \PDOStatement
	{
		$conn = $this->getConnection();
		$stmt = $conn->getRawObject()->prepare( $this->sql );

		foreach( $this->binds as $position => $list ) {
			$stmt->bindValue( $position, $list[0], $this->getPdoType( $list[1], $list[0] ) );
		}

		try
		{
			$stmt->execute();
		}
		catch( \PDOException $e )
		{
			// recover from lost connection (MySQL)
			if( !isset( $e->errorInfo[1] ) || $e->errorInfo[1] != 2006 || $conn->inTransaction() === true ) {
				throw $e;
			}

			$conn->connect();
			return $this->exec();
		}

		return $stmt;
	}
}
