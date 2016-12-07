<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage DB
 */


namespace Aimeos\MW\DB\Statement\PDO;


/**
 * Database statement class for prepared \PDO statements.
 *
 * @package MW
 * @subpackage DB
 */
class Prepared extends \Aimeos\MW\DB\Statement\Base implements \Aimeos\MW\DB\Statement\Iface
{
	private $stmt = null;


	/**
	 * Initializes the statement object.
	 *
	 * @param \PDOStatement $stmt \PDO database statement object
	 */
	public function __construct( \PDOStatement $stmt )
	{
		$this->stmt = $stmt;
	}


	/**
	 * Binds a value to a parameter in the statement.
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
		} catch ( \PDOException $pe ) {
			throw new \Aimeos\MW\DB\Exception( $pe->getMessage(), $pe->getCode(), $pe->errorInfo );
		}
	}


	/**
	 * Executes the statement.
	 *
	 * @return \Aimeos\MW\DB\Result\Iface Result object
	 * @throws \Aimeos\MW\DB\Exception If an error occured in the unterlying driver
	 */
	public function execute()
	{
		try {
			$this->stmt->execute();
		} catch ( \PDOException $pe ) {
			throw new \Aimeos\MW\DB\Exception( $pe->getMessage(), $pe->getCode(), $pe->errorInfo );
		}

		return new \Aimeos\MW\DB\Result\PDO( $this->stmt );
	}
}
