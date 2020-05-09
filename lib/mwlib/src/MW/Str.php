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
	 * @param mixed $str Stringable value
	 * @param mixed $needle String or character to search for
	 * @return string|null Sub-string after the needle or NULL if needle is not part of the string
	 */
	public static function after( $str, $needle ) : ?string
	{
		$str = (string) $str;
		$needle = (string) $needle;

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
	 * @param mixed $str Stringable value
	 * @param mixed $needle String or character to search for
	 * @return string|null Sub-string before the needle or NULL if needle is not part of the string
	 */
	public static function before( $str, $needle ) : ?string
	{
		$str = (string) $str;
		$needle = (string) $needle;

		if( $needle !== '' && ( $pos = strpos( $str, $needle ) ) !== false
			&& ( $result = substr( $str, 0, $pos ) ) !== false
		) {
			return $result;
		}

		return null;
	}


	/**
	 * Tests if the strings ends with the needle.
	 *
	 * @param mixed $str Stringable value
	 * @param array|string|null $needles String/character or list thereof to compare with
	 * @return bool TRUE if string ends with needle, FALSE if not
	 */
	public static function ends( $str, $needles ) : bool
	{
		$str = (string) $str;

		foreach( (array) $needles as $needle )
		{
			$needle = (string) $needle;

			if( $needle !== '' && substr_compare( $str, $needle, -strlen( $needle ) ) === 0 ) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Replaces special HTML characters by their entities.
	 *
	 * @param mixed $str Stringable value
	 * @param int $flags Which characters to encode
	 * @return string String which isn't interpreted as HTML and save to include in HTML documents
	 */
	public static function html( $str, int $flags = ENT_COMPAT | ENT_HTML401 ) : string
	{
		return htmlspecialchars( (string) $str, $flags, 'UTF-8' );
	}


	/**
	 * Tests if the strings contains all of the needles.
	 *
	 * @param mixed $str Stringable value
	 * @param string|array|null $needles String/character or list thereof to search for
	 * @return bool TRUE if string contains all needles, FALSE if not
	 */
	public static function in( $str, $needles ) : bool
	{
		$str = (string) $str;

		if( $needles == null ) {
			return false;
		}

		foreach( (array) $needles as $needle )
		{
			$needle = (string) $needle;

			if( $needle === '' || strpos( $str, $needle ) === false ) {
				return false;
			}
		}

		return true;
	}


	/**
	 * Transforms the string into a suitable URL segment.
	 *
	 * @param mixed $str Stringable value
	 * @param string $lang Two letter ISO language code
	 * @param string $sep Separator between the words
	 * @return string String suitable for an URL segment
	 */
	public static function slug( $str, string $lang = 'en', string $sep = '-' ) : string
	{
		$str = \voku\helper\ASCII::to_ascii( (string) $str, $lang );
		return trim( preg_replace( '/[^A-Za-z0-9]+/', $sep, $str ), $sep );
	}


	/**
	 * Tests if the strings contains at least one of the needles.
	 *
	 * @param mixed $str Stringable value
	 * @param array $needles Strings or characters to search for
	 * @return bool TRUE if string contains at least one needle, FALSE if it contains none
	 */
	public static function some( $str, array $needles ) : bool
	{
		$str = (string) $str;

		foreach( $needles as $needle )
		{
			$needle = (string) $needle;

			if( $needle !== '' && strpos( $str, $needle ) !== false ) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Tests if the strings starts with the needle.
	 *
	 * @param mixed $str Stringable value
	 * @param array|string|null $needles String/character or list thereof to compare with
	 * @return bool TRUE if string starts with needle, FALSE if not
	 */
	public static function starts( $str, $needles ) : bool
	{
		$str = (string) $str;

		foreach( (array) $needles as $needle )
		{
			$needle = (string) $needle;

			if( $needle !== '' && strncmp( $str, $needle, strlen( $needle ) ) === 0 ) {
				return true;
			}
		}

		return false;
	}
}
