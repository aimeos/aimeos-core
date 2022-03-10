<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2022
 * @package MW
 * @subpackage DB
 */


namespace Aimeos\Base\DB\Statement\DBAL;


/**
 * Database statement class for simple DBAL statements
 *
 * @package MW
 * @subpackage DB
 */
class Simple extends \Aimeos\Base\DB\Statement\Base implements \Aimeos\Base\DB\Statement\Iface
{
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
		return $this->sql;
	}


	/**
	 * Binds a value to a parameter in the statement.
	 *
	 * @param int $position Position index of the placeholder
	 * @param mixed $value Value which should be bound to the placeholder
	 * @param int $type Type of given value defined in \Aimeos\Base\DB\Statement\Base as constant
	 * @return \Aimeos\Base\DB\Statement\Iface Statement instance for method chaining
	 * @throws \Aimeos\Base\DB\Exception If the parameter type is invalid
	 */
	public function bind( int $position, $value, int $type = \Aimeos\Base\DB\Statement\Base::PARAM_STR ) : \Aimeos\Base\DB\Statement\Iface
	{
		throw new \Aimeos\Base\DB\Exception( 'Binding parameters is not available for simple statements: ' . $this->sql );
	}


	/**
	 * Executes the statement.
	 *
	 * @return \Aimeos\Base\DB\Result\Iface Result object
	 * @throws \Aimeos\Base\DB\Exception If an error occured in the unterlying driver or if the number of binds doesn't match
	 */
	public function execute() : \Aimeos\Base\DB\Result\Iface
	{
		try {
			$result = $this->getConnection()->getRawObject()->getWrappedConnection()->query( $this->sql );
		} catch( \Doctrine\DBAL\Driver\Exception $e ) {
			throw new \Aimeos\Base\DB\Exception( $e->getMessage() . ': ' . $this->sql, $e->getCode() );
		}

		return new \Aimeos\Base\DB\Result\DBAL( $result );
	}
}
