<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
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
class Standard
	extends \Aimeos\MW\View\Helper\Base
	implements \Aimeos\MW\View\Helper\Encoder\Iface
{
	const TAINT = 0;
	const TRUST = 1;


	/**
	 * Returns the encoder.
	 *
	 * @return \Aimeos\MW\View\Helper\Iface Encoder object
	 */
	public function transform()
	{
		return $this;
	}


	/**
	 * Escapes strings for HTML/XML attributes.
	 * All attribute values must be surrounded by " (double quote)
	 *
	 * @param string $value Attribute string
	 * @param integer $trust Zero to distrust the input, one (1) if you trust in it
	 * @param string $replace Replace whitespace characters by given string
	 * @return string Escaped attribute string
	 */
	public function attr( $value, $trust = self::TAINT, $replace = '' )
	{
		if( $replace != '' ) {
			$value = str_replace( " \v\t\r\n\f", $replace, $value );
		}

		return htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' );
	}


	/**
	 * Escapes strings for HTML.
	 *
	 * @param string $value HTML string
	 * @param integer $trust Zero to distrust the input, one (1) if you trust in it
	 * @return string Escaped HTML string
	 */
	public function html( $value, $trust = self::TAINT )
	{
		if( $trust === self::TRUST ) {
			return $value;
		}

		return htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' );
	}


	/**
	 * Escapes strings for XML.
	 * All node values must be surrounded by <![CDATA[...]]>
	 *
	 * @param string $value XML string
	 * @param integer $trust Zero to distrust the input, one (1) if you trust in it
	 * @return string Escaped XML string
	 */
	public function xml( $value, $trust = self::TAINT )
	{
		if( $trust === self::TRUST ) {
			return $value;
		}

		return htmlspecialchars( $value, ENT_NOQUOTES, 'UTF-8' );
	}


	/**
	 * Escapes strings for URLs.
	 *
	 * @param string $value URI/URL string
	 * @param integer $trust Zero to distrust the input, one (1) if you trust in it
	 * @param boolean $strip Stip HTML tags if they are part of the input
	 * @param array $replace Associative list of characters or strings that should be replaced
	 * @return string Escaped URI/URL string
	 */
	public function url( $value, $trust = self::TAINT, $strip = true, $replace = array( ' ' => '_' ) )
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