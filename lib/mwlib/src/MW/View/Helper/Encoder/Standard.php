<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2020
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
class Standard
	extends \Aimeos\MW\View\Helper\Base
	implements \Aimeos\MW\View\Helper\Encoder\Iface
{
	/**
	 * Returns the encoder.
	 *
	 * @return \Aimeos\MW\View\Helper\Iface Encoder object
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
			$value = json_encode( $value, JSON_HEX_AMP );
		} elseif( $replace != '' ) {
			$value = str_replace( [" ", "\v", "\t", "\r", "\n", "\f"], $replace, $value );
		}

		if( $trust === self::TRUST ) {
			return (string) $value;
		}

		return str_replace( '"', '&quot;', (string) $value );
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

		return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' );
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

		return htmlspecialchars( (string) $value, ENT_NOQUOTES, 'UTF-8' );
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
