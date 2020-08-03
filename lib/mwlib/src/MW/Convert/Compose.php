<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2020
 * @package MW
 * @subpackage Convert
 */


namespace Aimeos\MW\Convert;


/**
 * Combines several objects into a converter chain
 *
 * @package MW
 * @subpackage Convert
 */
class Compose implements \Aimeos\MW\Convert\Iface
{
	private $converter;


	/**
	 * Initializes the compose object.
	 *
	 * @param \Aimeos\MW\Convert\Iface[] $converter Instances of converter classes
	 */
	public function __construct( array $converter )
	{
		$this->converter = \Aimeos\MW\Common\Base::checkClassList( \Aimeos\MW\Convert\Iface::class, $converter );
	}


	/**
	 * Translates a value to another one.
	 *
	 * @param mixed $value Value to translate
	 * @return mixed Translated value
	 */
	public function translate( $value )
	{
		foreach( $this->converter as $object ) {
			$value = $object->translate( $value );
		}

		return $value;
	}


	/**
	 * Reverses the translation of the value.
	 *
	 * @param mixed $value Value to reverse
	 * @return mixed Reversed translation
	 */
	public function reverse( $value )
	{
		foreach( $this->converter as $object ) {
			$value = $object->reverse( $value );
		}

		return $value;
	}
}
