<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MW
 * @subpackage View
 */


/**
 * View helper class for encoding data into the output.
 *
 * @package MW
 * @subpackage View
 */
class MW_View_Helper_Encoder_Default
	extends MW_View_Helper_Abstract
	implements MW_View_Helper_Interface
{
	const TAINT = 0;
	const TRUST = 1;


	/**
	 * Returns the encoder.
	 *
	 * @return MW_View_Helper_Interface Encoder object
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
	 * @return string Escaped attribute string
	 */
	public function attr( $value, $trust = self::TAINT )
	{
		return htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' );
	}


	/**
	 * Escapes strings for HTML.
	 *
	 * @param string $value HTML string
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
	 * @return string Escaped URI/URL string
	 */
	public function url( $value, $trust = self::TAINT )
	{
		return urlencode( htmlspecialchars( $value, ENT_NOQUOTES, 'UTF-8' ) );
	}
}