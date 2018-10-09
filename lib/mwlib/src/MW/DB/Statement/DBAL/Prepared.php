<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2018-2018
 * @package MW
 * @subpackage DB
 */


namespace Aimeos\MW\DB\Statement\DBAL;


/**
 * Database statement class for prepared DBAL statements
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
	 * @param \Aimeos\MW\DB\Connection\DBAL $conn Database connection object
	 * @param string $sql SQL statement
	 */
	public function __construct( \Aimeos\MW\DB\Connection\DBAL $conn, $sql )
	{
		parent::__construct( $conn );
		$this->sql = $sql;
	}


	/**
	 * Binds a value to a parameter in the statement
	 *
	 * @param integer $position Position index of the placeholder
	 * @param mixed $value Value which should be bound to the placeholder
	 * @param integer $type Type of given value defined in \Aimeos\MW\DB\Statement\Base as constant
	 * @throws \Aimeos\MW\DB\Exception If an error occured in the unterlying driver
	 */
	public function bind( $position, $value, $type = \Aimeos\MW\DB\Statement\Base::PARAM_STR )
	{
		$this->binds[$position] = [$value, $type];
	}


	/**
	 * Executes the statement
	 *
	 * @return \Aimeos\MW\DB\Result\Iface Result object
	 * @throws \Aimeos\MW\DB\Exception If an error occured in the unterlying driver
	 */
	public function execute()
	{
		try {
			$stmt = $this->exec();
		} catch( \PDOException $e ) {
			throw new \Aimeos\MW\DB\Exception( $e->getMessage(), $e->getCode() );
		}

		return new \Aimeos\MW\DB\Result\PDO( $stmt );
	}


	/**
	 * Binds the parameters and executes the SQL statment
	 *
	 * @return \Doctrine\DBAL\Driver\Statement Executed DBAL statement
	 */
	protected function exec()
	{
		$conn = $this->getConnection();
		$stmt = $conn->getRawObject()->getWrappedConnection()->prepare( $this->sql );

		foreach( $this->binds as $position => $list ) {
			$stmt->bindValue( $position, $list[0], $this->getPdoType( $list[1], $list[0] ) );
		}

		try
		{
			$stmt->execute();
		}
		catch( \Exception $e )
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
