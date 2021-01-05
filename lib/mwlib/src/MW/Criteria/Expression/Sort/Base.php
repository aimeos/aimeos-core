<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 * @package MW
 * @subpackage Common
 */


namespace Aimeos\MW\Criteria\Expression\Sort;


/**
 * Base class for combining objects.
 *
 * @package MW
 * @subpackage Common
 */
abstract class Base implements Iface
{
	use \Aimeos\MW\Criteria\Expression\Traits;

	private $operator;
	private $name;


	/**
	 * Initializes the object.
	 *
	 * @param string $operator Sorting operator ("+": ascending, "-": descending)
	 * @param string $name Name of the variable to sort
	 */
	public function __construct( string $operator, string $name )
	{
		$this->operator = $operator;
		$this->name = $name;
	}


	/**
	 * Returns an array representation of the expression that can be parsed again
	 *
	 * @return array Multi-dimensional expression structure
	 */
	public function __toArray() : array
	{
		return [$this->name => $this->operator];
	}


	/**
	 * Returns the name of the variable to sort.
	 *
	 * @return string Name of the variable or column to sort
	 */
	public function getName() : string
	{
		return $this->name;
	}


	/**
	 * Returns the sorting direction operator.
	 *
	 * @return string Sorting direction ("+": ascending, "-": descending)
	 */
	public function getOperator() : string
	{
		return $this->operator;
	}
}
