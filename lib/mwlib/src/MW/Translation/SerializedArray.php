<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Translation
 */


/**
 * Translation using a serialized array
 *
 * @package MW
 * @subpackage Translation
 */
class MW_Translation_SerializedArray
	extends MW_Translation_Abstract
	implements MW_Translation_Interface
{
	private $_locale;
	private $_translations = array();
	private $_translationSources = array();


	/**
	 * Initializes the translation object.
	 *
	 * The serialized array must contain a list of key => value pairs or the value
	 * contains an array also for plural translations:
	 *
	 * singular => array(
	 *     singular translation, [plural translation 1 [, plural translation 2] ...]
	 * )
	 *
	 * Example for russia:
	 * a:1:{s:4:"file";a:4:{i:0;s:8:"Файл";i:1;s:8:"Файл";i:2;s:10:"Файла";i:3;s:12:"Файлов";}}
	 *
	 * @param array $translationSources Associative list of translation domains and lists of translation directories.
	 * 	Translations from the first file aren't overwritten by the later ones
	 * domain as key and the directory where the translation files are located as value.
	 * @param string $locale Locale string, e.g. en or en_GB
	 */
	public function __construct( array $translationSources, $locale )
	{
		$this->_translationSources = $translationSources;
		$this->_locale = (string) $locale;
	}


	/**
	 * Returns the translated string for the given domain.
	 *
	 * @param string $domain Translation domain
	 * @param string $string String to be translated
	 * @return string The translated string
	 * @throws MW_Translation_Exception Throws exception on initialization of the translation
	 */
	public function dt( $domain, $string )
	{
		try
		{
			$content = $this->_getTranslation( $domain );

			if ( isset( $content[$string][0] ) ) {
				return $content[$string][0];
			}
		}
		catch( Exception $e )
		{
			;
		}

		return (string) $string;
	}


	/**
	 * Returns the translated plural by given domain.
	 *
	 * @param string $domain Translation domain
	 * @param string $singular String in singular form
	 * @param string $plural String in plural form
	 * @param integer $number Quantity to choose the correct plural form for languages with plural forms
	 * @return string Returns the translated singular or plural form of the string depending on the given number
	 * @throws MW_Translation_Exception If the initialization of the translation
	 */
	public function dn( $domain, $singular, $plural, $number )
	{
		$index = $this->_getPluralIndex( $number, $this->_locale );

		try
		{
			$content = $this->_getTranslation( $domain );

			if ( isset( $content[ $singular ][ $index ] ) ) {
				return $content[ $singular ][ $index ];
			}
		}
		catch( Exception $e )
		{
			;
		}

		if( $index > 0 ) {
			return (string) $plural;
		}

		return (string) $singular;
	}


	/**
	 * Returns the current locale string.
	 *
	 * @return string ISO locale string
	 */
	public function getLocale()
	{
		return $this->_locale;
	}


	/**
	 * Gets, adds and loads necessary translation data if it was not set befor.
	 *
	 * @param string $domain Translation domain
	 * @return array Returns a list with key value pairs for each domain.
	 * @throws MW_Translation_Exception Throws exception on initialization of the translation
	 */
	private function _getTranslation( $domain )
	{
		if( !isset( $this->_translations[$domain] ) )
		{
			if( !isset( $this->_translationSources[$domain] ) )
			{
				$msg = sprintf( 'No translation directory for domain "%1$s" available', $domain );
				throw new MW_Translation_Exception( $msg );
			}

			$locations = $this->_getTranslationFileLocations( $this->_translationSources[$domain], $this->_locale );
			$translations = array();

			foreach( $locations as $location )
			{
				if( ( $content = file_get_contents( $location ) ) === false ) {
					throw new MW_Translation_Exception( 'No translation file "%1$s" available', $location );
				}

				if( ( $content = unserialize( $content ) ) === false ) {
					throw new MW_Translation_Exception( 'Invalid content in translation file "%1$s"', $location );
				}

				$translations = $translations + $content;
			}

			$this->_translations[$domain] = $translations;
		}

		return $this->_translations[$domain];
	}

}
