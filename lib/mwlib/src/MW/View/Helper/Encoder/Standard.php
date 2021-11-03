<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Encoder;

use Aimeos\MW\Str;


/**
 * View helper class for encoding data into the output.
 *
 * @package MW
 * @subpackage View
 */
class Standard
	extends \Aimeos\MW\View\Helper\Base
	implements \Aimeos\MW\View\Helper\Encoder\Iface
{
	/**
	 * Returns the encoder.
	 *
	 * @return \Aimeos\MW\View\Helper\Encoder\Iface Encoder object
	 */
	public function transform() : Iface
	{
		return $this;
	}


	/**
	 * Escapes strings for HTML/XML attributes.
	 * All attribute values must be surrounded by " (double quote)
	 *
	 * @param mixed $value Attribute string
	 * @param int $trust Zero to distrust the input, one (1) if you trust in it
	 * @param string $replace Replace whitespace characters by given string
	 * @return string Escaped attribute string
	 */
	public function attr( $value, int $trust = self::TAINT, string $replace = '' ) : string
	{
		if( $value !== null && !is_scalar( $value ) ) {
			$value = json_encode( $value, JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG );
		} elseif( $replace != '' ) {
			$value = str_replace( [" ", "\v", "\t", "\r", "\n", "\f"], $replace, $value );
		}

		if( $trust === self::TRUST ) {
			return (string) $value;
		}

		return str_replace( ['"', '\'', '`'], ['&quot;', '&apos;', '&#96;'], (string) $value );
	}


	/**
	 * Escapes strings for HTML.
	 *
	 * @param mixed $value HTML string
	 * @param int $trust Zero to distrust the input, one (1) if you trust in it
	 * @return string Escaped HTML string
	 */
	public function html( $value, int $trust = self::TAINT ) : string
	{
		if( $value !== null && !is_scalar( $value ) ) {
			$value = json_encode( $value, JSON_HEX_AMP );
		}

		if( $trust === self::TRUST ) {
			return (string) $value;
		}

		// Avoid client side template injection
		return str_replace( ['{', '}'], '', Str::html( (string) $value, ENT_QUOTES ) );
	}


	/**
	 * Escapes strings for JS strings.
	 * All strings values must be surrounded by ' (single quote)
	 *
	 * @param mixed $value Unescaped string
	 * @return string Escaped string for JS
	 */
	public function js( $value ) : string
	{
		if( $value !== null && !is_scalar( $value ) ) {
			$value = json_encode( $value, JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG );
		}

		return str_replace( ['"', '\'', '`'], ['&quot;', '&apos;', '\&#96;'], (string) $value );
	}


	/**
	 * Escapes strings for XML.
	 * All node values must be surrounded by <![CDATA[...]]>
	 *
	 * @param mixed $value XML string
	 * @param int $trust Zero to distrust the input, one (1) if you trust in it
	 * @return string Escaped XML string
	 */
	public function xml( $value, int $trust = self::TAINT ) : string
	{
		if( $value !== null && !is_scalar( $value ) ) {
			$value = json_encode( $value, JSON_HEX_AMP );
		}

		if( $trust === self::TRUST ) {
			return (string) $value;
		}

		return Str::html( (string) $value, ENT_NOQUOTES );
	}


	/**
	 * Escapes strings for URLs.
	 *
	 * @param string $value URI/URL string
	 * @param int $trust Zero to distrust the input, one (1) if you trust in it
	 * @param bool $strip Stip HTML tags if they are part of the input
	 * @param string[] $replace Associative list of characters or strings that should be replaced
	 * @return string Escaped URI/URL string
	 */
	public function url( string $value, int $trust = self::TAINT, bool $strip = true, array $replace = [' ' => '_'] ) : string
	{
		if( $strip !== false ) {
			$value = strip_tags( $value );
		}

		foreach( $replace as $key => $val ) {
			$value = str_replace( $key, $val, $value );
		}

		return urlencode( $value );
	}
}
