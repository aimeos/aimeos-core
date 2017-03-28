<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Translation
 */


namespace Aimeos\MW\Translation;


/**
 * Abstract class for the translation interface
 *
 * @package MW
 * @subpackage Translation
 */
abstract class Base
{
	private $locale;


	/**
	 * Initializes the translation object.
	 *
	 * @param string $locale Locale string, e.g. en or en_GB
	 */
	public function __construct( $locale )
	{
		if( preg_match( '/^[a-z]{2,3}(_[A-Z]{2})?$/', $locale ) !== 1 ) {
			throw new \Aimeos\MW\Translation\Exception( sprintf( 'Invalid locale "%1$s"', $locale ) );
		}

		$this->locale = (string) $locale;
	}


	/**
	 * Returns the current locale string.
	 *
	 * @return string ISO locale string
	 */
	public function getLocale()
	{
		return $this->locale;
	}


	/**
	 * Returns the location of the translation file.
	 * If the requested file does exists (eg: de_DE) the implementation
	 * will check for "de" and will return that location as fallback.
	 *
	 * @param array $paths Paths of the translation files.
	 * @param string $locale Locale to be used
	 * @return array List of locations to the translation files
	 * @throws \Aimeos\MW\Translation\Exception If translation file doesn't exist
	 */
	protected function getTranslationFileLocations( array $paths, $locale )
	{
		$locations = [];

		foreach( $paths as $path )
		{
			$location = $path . DIRECTORY_SEPARATOR . $locale;

			if( file_exists( $location ) )
			{
				$locations[] = $location;
				continue;
			}

			if( strlen( $locale ) > 3 )
			{
				$location = $path . DIRECTORY_SEPARATOR . substr( $locale, 0, -strlen( strrchr( $locale, '_' ) ) );

				if( file_exists( $location ) ) {
					$locations[] = $location;
				}
			}
		}

		return $locations;
	}


	/**
	 * Returns the plural index number to be used for the plural translation.
	 *
	 * @param  integer $number Quantity to find the plural index
	 * @param  string  $locale Locale to be used
	 * @return integer Number of the plural index
	 *
	 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
	 * @license    http://framework.zend.com/license/new-bsd     New BSD License
	 */
	protected function getPluralIndex( $number, $locale )
	{
		$number = abs( (int) $number );

		if( $locale == 'pt_BR' ) {
			$locale = 'xbr'; // temporary set a locale for brasilian
		}

		if( strlen( $locale ) > 3 ) {
			$locale = substr( $locale, 0, -strlen( strrchr( $locale, '_' ) ) );
		}

		switch ( $locale )
		{
			case 'af':
			case 'az':
			case 'bn':
			case 'bg':
			case 'ca':
			case 'da':
			case 'de':
			case 'el':
			case 'en':
			case 'eo':
			case 'es':
			case 'et':
			case 'eu':
			case 'fa':
			case 'fi':
			case 'fo':
			case 'fur':
			case 'fy':
			case 'gl':
			case 'gu':
			case 'ha':
			case 'he':
			case 'hu':
			case 'is':
			case 'it':
			case 'ku':
			case 'lb':
			case 'ml':
			case 'mn':
			case 'mr':
			case 'nah':
			case 'nb':
			case 'ne':
			case 'nl':
			case 'nn':
			case 'no':
			case 'om':
			case 'or':
			case 'pa':
			case 'pap':
			case 'ps':
			case 'pt':
			case 'so':
			case 'sq':
			case 'sv':
			case 'sw':
			case 'ta':
			case 'te':
			case 'tk':
			case 'ur':
			case 'zu':
				return ($number == 1) ? 0 : 1;

			case 'am':
			case 'bh':
			case 'fil':
			case 'fr':
			case 'gun':
			case 'hi':
			case 'ln':
			case 'mg':
			case 'nso':
			case 'xbr':
			case 'ti':
			case 'wa':
				return (($number == 0) || ($number == 1)) ? 0 : 1;

			case 'be':
			case 'bs':
			case 'hr':
			case 'ru':
			case 'sr':
			case 'uk':
				return (($number % 10 == 1) && ($number % 100 != 11)) ? 0 : ((($number % 10 >= 2) && ($number % 10 <= 4) && (($number % 100 < 10) || ($number % 100 >= 20))) ? 1 : 2);

			case 'cs':
			case 'sk':
				return ($number == 1) ? 0 : ((($number >= 2) && ($number <= 4)) ? 1 : 2);

			case 'ar':
				return ($number == 0) ? 0 : (($number == 1) ? 1 : (($number == 2) ? 2 : ((($number >= 3) && ($number <= 10)) ? 3 : ((($number >= 11) && ($number <= 99)) ? 4 : 5))));

			case 'cy':
				return ($number == 1) ? 0 : (($number == 2) ? 1 : ((($number == 8) || ($number == 11)) ? 2 : 3));

			case 'ga':
				return ($number == 1) ? 0 : (($number == 2) ? 1 : 2);

			case 'lt':
				return (($number % 10 == 1) && ($number % 100 != 11)) ? 0 : ((($number % 10 >= 2) && (($number % 100 < 10) || ($number % 100 >= 20))) ? 1 : 2);

			case 'lv':
				return ($number == 0) ? 0 : ((($number % 10 == 1) && ($number % 100 != 11)) ? 1 : 2);

			case 'mk':
				return ($number % 10 == 1) ? 0 : 1;

			case 'mt':
				return ($number == 1) ? 0 : ((($number == 0) || (($number % 100 > 1) && ($number % 100 < 11))) ? 1 : ((($number % 100 > 10) && ($number % 100 < 20)) ? 2 : 3));

			case 'pl':
				return ($number == 1) ? 0 : ((($number % 10 >= 2) && ($number % 10 <= 4) && (($number % 100 < 12) || ($number % 100 > 14))) ? 1 : 2);

			case 'ro':
				return ($number == 1) ? 0 : ((($number == 0) || (($number % 100 > 0) && ($number % 100 < 20))) ? 1 : 2);

			case 'sl':
				return ($number % 100 == 1) ? 0 : (($number % 100 == 2) ? 1 : ((($number % 100 == 3) || ($number % 100 == 4)) ? 2 : 3));

			default:
				return 0;
		}
	}

}
