<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	const TAINT = 0;
	const TRUST = 1;


	/**
	 * Returns the encoder.
	 *
	 * @return \Aimeos\MW\View\Helper\Encoder\Iface Encoder object
	 */
	public function transform() : Iface;

	/**
	 * Escapes strings for HTML/XML attributes.
	 * All attribute values must be surrounded by " (double quote)
	 *
	 * @param mixed $value Attribute string
	 * @param int $trust Zero to distrust the input, one (1) if you trust in it
	 * @param string $replace Replace whitespace characters by given string
	 * @return string Escaped attribute string
	 */
	public function attr( $value, int $trust = self::TAINT, string $replace = '' ) : string;

	/**
	 * Escapes strings for HTML.
	 *
	 * @param mixed $value HTML string
	 * @param int $trust Zero to distrust the input, one (1) if you trust in it
	 * @return string Escaped HTML string
	 */
	public function html( $value, int $trust = self::TAINT ) : string;

	/**
	 * Escapes strings for JS strings.
	 * All strings values must be surrounded by ' (single quote)
	 *
	 * @param mixed $value Unescaped string
	 * @return string Escaped string for JS
	 */
	public function js( $value ) : string;

	/**
	 * Escapes strings for XML.
	 * All node values must be surrounded by <![CDATA[...]]>
	 *
	 * @param mixed $value XML string
	 * @param int $trust Zero to distrust the input, one (1) if you trust in it
	 * @return string Escaped XML string
	 */
	public function xml( $value, int $trust = self::TAINT ) : string;

	/**
	 * Escapes strings for URLs.
	 *
	 * @param string $value URI/URL string
	 * @param int $trust Zero to distrust the input, one (1) if you trust in it
	 * @param bool $strip Stip HTML tags if they are part of the input
	 * @param string[] $replace Associative list of characters or strings that should be replaced
	 * @return string Escaped URI/URL string
	 */
	public function url( string $value, int $trust = self::TAINT, bool $strip = true, array $replace = [' ' => '_'] ) : string;
}
