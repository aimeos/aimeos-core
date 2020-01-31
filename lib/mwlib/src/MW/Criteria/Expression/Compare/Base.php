<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
 * @package MW
 * @subpackage Common
 */


namespace Aimeos\MW\Criteria\Expression\Compare;


/**
 * Abstract class with common methods for comparing objects.
 *
 * @package MW
 * @subpackage Common
 */
abstract class Base
	extends \Aimeos\MW\Criteria\Expression\Base
	implements \Aimeos\MW\Criteria\Expression\Compare\Iface
{
	private $operator;
	private $name;
	private $value;


	/**
	 * Initializes the object.
	 *
	 * @param string $operator Operator used for the expression
	 * @param string $name Name of variable or column that should be compared.
	 * @param string|array $value Value that the variable or column should be compared to
	 */
	public function __construct( string $operator, string $name, $value )
	{
		$this->operator = $operator;
		$this->name = $name;
		$this->value = $value;
	}


	/**
	 * Returns the operator used for the expression.
	 *
	 * @return string Operator used for the expression
	 */
	public function getOperator() : string
	{
		return $this->operator;
	}


	/**
	 * Returns the left side of the compare expression.
	 *
	 * @return string Name of variable or column that should be compared
	 */
	public function getName() : string
	{
		return $this->name;
	}


	/**
	 * Returns the right side of the compare expression.
	 *
	 * @return string|array Value that the variable or column should be compared to
	 */
	public function getValue()
	{
		return $this->value;
	}


	/**
	 * Generates a string from the expression objects.
	 *
	 * @param array $types Associative list of variable or column names as keys and their corresponding types
	 * @param array $translations Associative list of variable or column names that should be translated
	 * @param \Aimeos\MW\Criteria\Plugin\Iface[] $plugins Associative list of item names as keys and plugin objects as values
	 * @param array $funcs Associative list of item names and functions modifying the conditions
	 * @return mixed Expression that evaluates to a boolean result
	 */
	public function toSource( array $types, array $translations = [], array $plugins = [], array $funcs = [] )
	{
		$this->setPlugins( $plugins );

		$name = $this->name;

		if( ( $transname = $this->translateName( $name, $translations, $funcs ) ) === null ) {
			return;
		}

		if( $transname === '' ) {
			$transname = $name;
		}

		$transvalue = $this->translateValue( $name, $this->value );

		if( !isset( $types[$name] ) ) {
			throw new \Aimeos\MW\Common\Exception( sprintf( 'Invalid name "%1$s"', $name ) );
		}

		if( $transvalue === null && ( $this->operator === '==' || $this->operator === '!=' ) ) {
			return $this->createNullTerm( $transname );
		}

		if( is_array( $transname ) ) {
			return $transname;
		}

		if( is_array( $transvalue ) ) {
			return $this->createListTerm( $transname, $types[$name] );
		}

		return $this->createTerm( $transname, $types[$name], $this->value );
	}


	/**
	 * Creates a term string from the given parameters.
	 *
	 * @param string $name Translated name of variable or column that should be compared
	 * @param string $type Type constant
	 * @param mixed $value Value that the variable or column should be compared to
	 * @return string Created term string (name operator value)
	 */
	abstract protected function createTerm( string $name, string $type, $value ) : string;


	/**
	 * Creates a term which contains a null value.
	 *
	 * @param string $name Translated name of the variable or column
	 * @return string String that can be inserted into a SQL statement
	 */
	abstract protected function createNullTerm( string $name ) : string;


	/**
	 * Creates a term from a list of values.
	 *
	 * @param string $name Translated name of the variable or column
	 * @param string $type Type constant
	 * @return string String that can be inserted into a SQL statement
	 */
	abstract protected function createListTerm( string $name, string $type ) : string;
}
