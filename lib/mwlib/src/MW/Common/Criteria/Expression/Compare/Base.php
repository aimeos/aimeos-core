<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Common
 */


/**
 * Abstract class with common methods for comparing objects.
 *
 * @package MW
 * @subpackage Common
 */
abstract class MW_Common_Criteria_Expression_Compare_Base
	extends MW_Common_Criteria_Expression_Base
	implements MW_Common_Criteria_Expression_Compare_Interface
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
	public function __construct( $operator, $name, $value )
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
	public function getOperator()
	{
		return $this->operator;
	}


	/**
	 * Returns the left side of the compare expression.
	 *
	 * @return string Name of variable or column that should be compared
	 */
	public function getName()
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
	 * @param array $plugins Associative list of item names and plugins implementing MW_Common_Criteria_Plugin_Interface
	 * @return string Expression that evaluates to a boolean result
	 */
	public function toString( array $types, array $translations = array(), array $plugins = array() )
	{
		$this->setPlugins( $plugins );

		$name = $this->name;

		if( ( $transname = $this->translateName( $name, $translations ) ) === '' ) {
			$transname = $name;
		}

		if( !isset( $types[$name] ) ) {
			throw new MW_Common_Exception( sprintf( 'Invalid name "%1$s"', $name ) );
		}

		if( $this->value === null ) {
			return $this->createNullTerm( $transname );
		}

		if( is_array( $this->value ) ) {
			return $this->createListTerm( $transname, $types[$name] );
		}

		return $this->createTerm( $transname, $types[$name], $this->value );
	}


	/**
	 * Creates a term string from the given parameters.
	 *
	 * @param string $name Translated name of variable or column that should be compared
	 * @param integer $type Type constant
	 * @param string $value Value that the variable or column should be compared to
	 * @return string Created term string (name operator value)
	 */
	abstract protected function createTerm( $name, $type, $value );


	/**
	 * Creates a term which contains a null value.
	 *
	 * @param string $name Translated name of the variable or column
	 * @return string String that can be inserted into a SQL statement
	 */
	abstract protected function createNullTerm( $name );


	/**
	 * Creates a term from a list of values.
	 *
	 * @param string $name Translated name of the variable or column
	 * @param integer $type Type constant
	 * @return string String that can be inserted into a SQL statement
	 */
	abstract protected function createListTerm( $name, $type );
}
