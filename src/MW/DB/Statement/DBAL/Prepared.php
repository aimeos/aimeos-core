<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2022
 * @package MW
 * @subpackage DB
 */


namespace Aimeos\Base\DB\Statement\DBAL;


/**
 * Database statement class for prepared DBAL statements
 *
 * @package MW
 * @subpackage DB
 */
class Prepared extends \Aimeos\Base\DB\Statement\Base implements \Aimeos\Base\DB\Statement\Iface
{
	private $binds = [];
	private $sql;


	/**
	 * Initializes the statement object
	 *
	 * @param \Aimeos\Base\DB\Connection\DBAL $conn Database connection object
	 * @param string $sql SQL statement
	 */
	public function __construct( \Aimeos\Base\DB\Connection\DBAL $conn, string $sql )
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
	 * @param int $type Type of given value defined in \Aimeos\Base\DB\Statement\Base as constant
	 * @return \Aimeos\Base\DB\Statement\Iface Statement instance for method chaining
	 * @throws \Aimeos\Base\DB\Exception If an error occured in the unterlying driver
	 */
	public function bind( int $position, $value, int $type = \Aimeos\Base\DB\Statement\Base::PARAM_STR ) : \Aimeos\Base\DB\Statement\Iface
	{
		$this->binds[$position] = [$value, $type];
		return $this;
	}


	/**
	 * Executes the statement
	 *
	 * @return \Aimeos\Base\DB\Result\Iface Result object
	 * @throws \Aimeos\Base\DB\Exception If an error occured in the unterlying driver
	 */
	public function execute() : \Aimeos\Base\DB\Result\Iface
	{
		try {
			$result = $this->exec();
		} catch( \Doctrine\DBAL\Driver\Exception $e ) {
			throw new \Aimeos\Base\DB\Exception( $e->getMessage() . ': ' . $this->sql . map( $this->binds )->col( 0 )->toJson(), $e->getCode() );
		}

		return new \Aimeos\Base\DB\Result\DBAL( $result );
	}


	/**
	 * Binds the parameters and executes the SQL statment
	 *
	 * @return \Doctrine\DBAL\Driver\Statement|\Doctrine\DBAL\Driver\Result DBAL statement or result object
	 */
	protected function exec()
	{
		$stmt = $this->getConnection()->getRawObject()->getWrappedConnection()->prepare( $this->sql );

		foreach( $this->binds as $position => $list ) {
			$stmt->bindValue( $position, $list[0], $this->getDbalType( $list[1], $list[0] ) );
		}

		$result = $stmt->execute();

		if( $result instanceof \Doctrine\DBAL\Driver\Result ) {
			return $result;
		}

		return $stmt;
	}


	/**
	 * Returns the PDO type mapped to the Aimeos type
	 *
	 * @param integer $type Type of given value defined in \Aimeos\Base\DB\Statement\Base as constant
	 * @param mixed $value Value which should be bound to the placeholder
	 * @return integer PDO parameter type constant
	 * @throws \Aimeos\Base\DB\Exception If the type is unknown
	 */
	protected function getDbalType( int $type, $value ) : int
	{
		switch( $type )
		{
			case \Aimeos\Base\DB\Statement\Base::PARAM_NULL:
				$dbaltype = \Doctrine\DBAL\ParameterType::NULL; break;
			case \Aimeos\Base\DB\Statement\Base::PARAM_BOOL:
				$dbaltype = \Doctrine\DBAL\ParameterType::BOOLEAN; break;
			case \Aimeos\Base\DB\Statement\Base::PARAM_INT:
				$dbaltype = \Doctrine\DBAL\ParameterType::INTEGER; break;
			case \Aimeos\Base\DB\Statement\Base::PARAM_FLOAT:
				$dbaltype = \Doctrine\DBAL\ParameterType::STRING; break;
			case \Aimeos\Base\DB\Statement\Base::PARAM_STR:
				$dbaltype = \Doctrine\DBAL\ParameterType::STRING; break;
			case \Aimeos\Base\DB\Statement\Base::PARAM_LOB:
				$dbaltype = \Doctrine\DBAL\ParameterType::LARGE_OBJECT; break;
			default:
				throw new \Aimeos\Base\DB\Exception( sprintf( 'Invalid parameter type "%1$s"', $type ) );
		}

		if( is_null( $value ) ) {
			$dbaltype = \Doctrine\DBAL\ParameterType::NULL;
		}

		return $dbaltype;
	}
}
