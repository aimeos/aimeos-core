<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
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
	private static $methods = [];
	private static $node;
	private static $seq = 0;


	/**
	 * Calls custom methods
	 *
	 * @param string $method Name of the method
	 * @param array $args Method parameters
	 * @return mixed Return value of the called method
	 */
	public static function __callStatic( string $method, array $args )
	{
		if( $fcn = static::method( $method ) ) {
			return $fcn( ...$args );
		}

		throw new \InvalidArgumentException( sprintf( 'Unknown method "%1$s" in "%2$s"', $method, __CLASS__ ) );
	}


	/**
	 * Returns the sub-string after the given needle.
	 *
	 * @param mixed $str Stringable value
	 * @param mixed $needles String or strings to search for
	 * @return string|null Sub-string after the needle or NULL if needle is not part of the string
	 */
	public static function after( $str, $needles ) : ?string
	{
		$str = (string) $str;

		foreach( (array) $needles as $needle )
		{
			$needle = (string) $needle;

			if( ( $len = strlen( $needle ) ) > 0 && ( $pos = strpos( $str, $needle ) ) !== false
				&& ( $result = substr( $str, $pos + $len ) ) !== false
			) {
				return $result;
			}
		}

		return null;
	}


	/**
	 * Returns the sub-string before the given needle.
	 *
	 * @param mixed $str Stringable value
	 * @param mixed $needles String or strings to search for
	 * @return string|null Sub-string before the needle or NULL if needle is not part of the string
	 */
	public static function before( $str, $needles ) : ?string
	{
		$str = (string) $str;

		foreach( (array) $needles as $needle )
		{
			$needle = (string) $needle;

			if( $needle !== '' && ( $pos = strpos( $str, $needle ) ) !== false
				&& ( $result = substr( $str, 0, $pos ) ) !== false
			) {
				return $result;
			}
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

		if( $str === '' || $needles == null ) {
			return false;
		}

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
	 * Registers a custom method
	 *
	 * @param string $method Name of the method
	 * @param \Closure|null $fcn Anonymous function which receives the same parameters as the original method
	 * @return \Closure|null Registered anonymous function or NULL if none has been registered
	 */
	public static function method( string $method, \Closure $fcn = null ) : ?\Closure
	{
		if( $fcn ) {
			self::$methods[$method] = $fcn;
		}

		return self::$methods[$method] ?? null;
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
		if( $fcn = static::method( 'slug' ) ) {
			return $fcn( $str, $lang, $sep );
		}

		$str = \voku\helper\ASCII::to_ascii( (string) $str, $lang );
		return trim( preg_replace( '/[^A-Za-z0-9~_.]+/', $sep, $str ), $sep );
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

		if( $str === '' || $needles == null ) {
			return false;
		}

		foreach( (array) $needles as $needle )
		{
			$needle = (string) $needle;

			if( $needle !== '' && strncmp( $str, $needle, strlen( $needle ) ) === 0 ) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Generates a unique ID string suitable as global identifier
	 *
	 * The ID is similar to an UUID and is as unique as an UUID but it's human
	 * readable string is only 20 bytes long. Additionally, the unique ID is
	 * optimized for being used as primary key in databases.
	 *
	 * @return string Global unique ID of 20 bytes length
	 */
	public static function uid() : string
	{
		if( self::$node === null )
		{
			try {
				self::$node = random_bytes( 6 );
			} catch( \Throwable $t ) {
				if( function_exists( 'openssl_random_pseudo_bytes' ) ) {
					self::$node = openssl_random_pseudo_bytes( 6 );
				} else {
					self::$node = pack( 'n*', rand( 0, 0xffff ), rand( 0, 0xffff ), rand( 0, 0xffff ) );
				}
			}
		}

		$t = gettimeofday();
		$sec = $t['sec'];
		$usec = $t['usec'];

		self::$seq = self::$seq + 1 & 0xfff; // 20 bits for sequence (1 to 4,095), wraps around

		$hsec = ( $sec & 0xff00000000 ) >> 32; // 5th byte from seconds till 1970-01-01T00:00:00 (on 64 bit platforms)
		$lsec = $sec & 0xffffffff; // Lowest 32 bits from seconds till 1970-01-01T00:00:00
		$husec = ( $usec & 0xffff0 ) >> 4; // Highest 16 bits from micro seconds (total 20 bits)
		$mix = ( $usec & 0xf ) << 4 | ( self::$seq & 0xf00 ) >> 8; // Lowest 4 bits (usec) and highest 4 bits (seq)
		$lseq = self::$seq & 0xff; // Lowest 16 bits from sequence

		// 5 bytes seconds, 2 byte usec, 1 byte usec+seq, 1 byte seq, 6 bytes node
		$uid = base64_encode( pack( 'CNnCC', $hsec, $lsec, $husec, $mix, $lseq ) . self::$node );

		return str_replace( ['+', '/'], ['-', '_'], $uid ); // URL safety
	}
}
