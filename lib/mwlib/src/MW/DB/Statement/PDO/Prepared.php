<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage DB
 */


/**
 * Database statement class for prepared PDO statements.
 *
 * @package MW
 * @subpackage DB
 */
class MW_DB_Statement_PDO_Prepared extends MW_DB_Statement_Abstract implements MW_DB_Statement_Interface
{
	private $stmt = null;


	/**
	 * Initializes the statement object.
	 *
	 * @param PDOStatement $stmt PDO database statement object
	 */
	public function __construct( PDOStatement $stmt )
	{
		$this->stmt = $stmt;
	}


	/**
	 * Binds a value to a parameter in the statement.
	 *
	 * @param integer $position Position index of the placeholder
	 * @param mixed $value Value which should be bound to the placeholder
	 * @param integer $type Type of given value defined in MW_DB_Statement_Abstract as constant
	 * @throws MW_DB_Exception If an error occured in the unterlying driver
	 */
	public function bind( $position, $value, $type = MW_DB_Statement_Abstract::PARAM_STR )
	{
		switch( $type )
		{
			case MW_DB_Statement_Abstract::PARAM_NULL:
				$pdotype = PDO::PARAM_NULL; break;
			case MW_DB_Statement_Abstract::PARAM_BOOL:
				$pdotype = PDO::PARAM_BOOL; break;
			case MW_DB_Statement_Abstract::PARAM_INT:
				$pdotype = PDO::PARAM_INT; break;
			case MW_DB_Statement_Abstract::PARAM_FLOAT:
				$pdotype = PDO::PARAM_STR; break;
			case MW_DB_Statement_Abstract::PARAM_STR:
				$pdotype = PDO::PARAM_STR; break;
			case MW_DB_Statement_Abstract::PARAM_LOB:
				$pdotype = PDO::PARAM_LOB; break;
			default:
				throw new MW_DB_Exception( sprintf( 'Invalid parameter type "%1$s"', $type ) );
		}

		if( is_null( $value ) ) {
			$pdotype = PDO::PARAM_NULL;
		}

		try {
			$this->stmt->bindValue( $position, $value, $pdotype );
		} catch ( PDOException $pe ) {
			throw new MW_DB_Exception( $pe->getMessage(), $pe->getCode(), $pe->errorInfo );
		}
	}


	/**
	 * Executes the statement.
	 *
	 * @return MW_DB_Result_Interface Result object
	 * @throws MW_DB_Exception If an error occured in the unterlying driver
	 */
	public function execute()
	{
		try {
			$this->stmt->execute();
		} catch ( PDOException $pe ) {
			throw new MW_DB_Exception( $pe->getMessage(), $pe->getCode(), $pe->errorInfo );
		}

		return new MW_DB_Result_PDO( $this->stmt );
	}
}
