<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
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
	private $stmt = null;


	/**
	 * Initializes the statement object
	 *
	 * @param \Doctrine\DBAL\Driver\Statement $stmt DBAL database statement object
	 */
	public function __construct( \Doctrine\DBAL\Driver\Statement $stmt )
	{
		$this->stmt = $stmt;
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
		try {
			$this->stmt->bindValue( $position, $value, $this->getPdoType( $type, $value ) );
		} catch ( \Doctrine\DBAL\DBALException $e ) {
			throw new \Aimeos\MW\DB\Exception( $e->getMessage(), $e->getCode() );
		}
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
			$this->stmt->execute();
		} catch ( \Doctrine\DBAL\DBALException $e ) {
			throw new \Aimeos\MW\DB\Exception( $e->getMessage(), $e->getCode() );
		}

		return new \Aimeos\MW\DB\Result\DBAL( $this->stmt );
	}
}
