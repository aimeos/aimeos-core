<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020
 * @package MW
 */


namespace Aimeos\MW;


/**
 * String utility class
 *
 * @package MW
 */
class Str
{
	/**
	 * Returns the sub-string after the given needle.
	 *
	 * @param string $str Original string
	 * @param string $needle String or character to search for
	 * @return string| Sub-string after the needle or NULL if needle is not part of the string
	 */
	public static function after( string $str, string $needle ) : ?string
	{
		if( ( $len = strlen( $needle ) ) > 0 && ( $pos = strpos( $str, $needle ) ) !== false
			&& ( $result = substr( $str, $pos + $len ) ) !== false
		) {
			return $result;
		}

		return null;
	}


	/**
	 * Returns the sub-string before the given needle.
	 *
	 * @param string $str Original string
	 * @param string $needle String or character to search for
	 * @return string| Sub-string before the needle or NULL if needle is not part of the string
	 */
	public static function before( string $str, string $needle ) : ?string
	{
		if( $needle !== '' && ( $pos = strpos( $str, $needle ) ) !== false
			&& ( $result = substr( $str, 0, $pos ) ) !== false
		) {
			return $result;
		}

		return null;
	}


	/**
	 * Returns the number of characters (not bytes).
	 *
	 * @param string $str String with variable width UTF-8 characters
	 * @return int Number of characters
	 */
	public static function chars( string $str ) : int
	{
		return mb_strlen( $str );
	}


	/**
	 * Tests if the strings ends with the needle.
	 *
	 * @param string $str String to test
	 * @param string $needle String or character to compare with
	 * @return bool TRUE if string ends with needle, FALSE if not
	 */
	public static function ends( string $str, string $needle ) : bool
	{
		return $needle !== '' && substr_compare( $str, $needle, -strlen( $needle ) ) === 0;
	}


	/**
	 * Replaces special HTML characters by their entities.
	 *
	 * @param string $str Original string
	 * @param int $flags Which characters to encode
	 * @return string String which isn't interpreted as HTML and save to include in HTML documents
	 */
	public static function html( string $str, int $flags = ENT_COMPAT | ENT_HTML401 ) : string
	{
		return htmlspecialchars( $str, $flags );
	}


	/**
	 * Tests if the strings contains all of the needles.
	 *
	 * @param string $str String to test
	 * @param string|array $needles String/character or multiple thereof to search for
	 * @return bool TRUE if string contains all needles, FALSE if not
	 */
	public static function in( string $str, $needles ) : bool
	{
		foreach( (array) $needles as $needle )
		{
			if( $needle === '' || strpos( $str, $needle ) === false ) {
				return false;
			}
		}

		return true;
	}


	/**
	 * Tests if the value is a string.
	 *
	 * @param mixed $value Value to test
	 * @return bool TRUE if value is a string, FALSE if not
	 */
	public static function is( $value ) : bool
	{
		return is_string( $value );
	}


	/**
	 * Returns a string with the number of characters (not bytes) limited.
	 *
	 * @param string $str Original string
	 * @param int $chars Number of characters
	 * @return string Shortend string
	 */
	public static function limit( string $str, int $chars ) : string
	{
		return mb_substr( $str, 0, $chars );
	}


	/**
	 * Tests if the string matches the pattern.
	 *
	 * @param string $str String to test
	 * @param string $pattern Regular expression pattern
	 * @param bool TRUE if the pattern matches, FALSE if not
	 */
	public static function match( string $str, string $pattern ) : bool
	{
		return (bool) preg_match( $pattern, $str );
	}


	/**
	 * Transforms the string into a suitable URL segment.
	 *
	 * @param string $str Original string
	 * @param string $sep Separator between the words
	 * @return string String suitable for an URL segment
	 */
	public static function slug( string $str, string $sep = '-' ) : string
	{
		$pattern = '/(\ |\!|\"|\#|\$|\%|\&|\'|\(|\)|\*|\+|\,|\.|\/|\:|\;|\<|\=|\>|\?|\@|\[|\\|\]|\^|\`|\%)+/';
		return trim( preg_replace( $pattern, $sep, $str ), $sep );
	}


	/**
	 * Tests if the strings contains at least one of the needles.
	 *
	 * @param string $str String to test
	 * @param array $needles Strings or characters to search for
	 * @return bool TRUE if string contains at least one needle, FALSE if it contains none
	 */
	public static function some( string $str, array $needles ) : bool
	{
		foreach( $needles as $needle )
		{
			if( $needle !== '' && strpos( $str, $needle ) !== false ) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Tests if the strings starts with the needle.
	 *
	 * @param string $str String to test
	 * @param string $needle String or character to compare with
	 * @return bool TRUE if string starts with needle, FALSE if not
	 */
	public static function starts( string $str, string $needle ) : bool
	{
		return $needle !== '' && strncmp( $str, $needle, strlen( $needle ) ) === 0;
	}


	/**
	 * Removes all HTML tags besides the allowed ones
	 *
	 * @param string $str Original string
	 * @param array $allowed Which HTML tags are allowed
	 * @return string String HTML tags removed
	 */
	public static function strip( string $str, array $allowed = [] ) : string
	{
		return strip_tags( $str, join( '', $allowed ) );
	}
}