<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Encoder;


/**
 * View helper class for encoding data into the output.
 *
 * @package MW
 * @subpackage View
 */
interface Iface extends \Aimeos\MW\View\Helper\Iface
{
	/**
	 * Returns the encoder.
	 *
	 * @return \Aimeos\MW\View\Helper\Iface Encoder object
	 */
	public function transform();

	/**
	 * Escapes strings for HTML/XML attributes.
	 * All attribute values must be surrounded by " (double quote)
	 *
	 * @param string $value Attribute string
	 * @param integer $trust Zero to distrust the input, one (1) if you trust in it
	 * @param string $replace Replace whitespace characters by given string
	 * @return string Escaped attribute string
	 */
	public function attr( $value, $trust = self::TAINT, $replace = '' );

	/**
	 * Escapes strings for HTML.
	 *
	 * @param string $value HTML string
	 * @param integer $trust Zero to distrust the input, one (1) if you trust in it
	 * @return string Escaped HTML string
	 */
	public function html( $value, $trust = self::TAINT );

	/**
	 * Escapes strings for XML.
	 * All node values must be surrounded by <![CDATA[...]]>
	 *
	 * @param string $value XML string
	 * @param integer $trust Zero to distrust the input, one (1) if you trust in it
	 * @return string Escaped XML string
	 */
	public function xml( $value, $trust = self::TAINT );

	/**
	 * Escapes strings for URLs.
	 *
	 * @param string $value URI/URL string
	 * @param integer $trust Zero to distrust the input, one (1) if you trust in it
	 * @param boolean $strip Stip HTML tags if they are part of the input
	 * @param array $replace Associative list of characters or strings that should be replaced
	 * @return string Escaped URI/URL string
	 */
	public function url( $value, $trust = self::TAINT, $strip = true, $replace = array( ' ' => '_' ) );
}