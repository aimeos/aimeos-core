<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Translation
 * @version $Id: Zend.php 16606 2012-10-19 12:50:23Z nsendetzky $
 */


/**
 * Translation using Zend_Translate
 *
 * @package MW
 * @subpackage Translation
 */
class MW_Translation_Zend
	extends MW_Translation_Abstract
	implements MW_Translation_Interface
{
	private $_translate;
	private $_adapter;
	private $_locale;
	private $_options;
	private $_translations = array();
	private $_translationSources = array();


	/**
	 * Initializes the translation object using Zend_Translate.
	 * This implementation only accepts files as source for the Zend_Translate_Adapter.
	 *
	 * @param array $translationSources List of key/value pairs with the translation domain
	 * as key and the directory where the translation files are located as value.
	 * @param string $adapter Name of the Zend translation adapter
	 * @param string $locale ISO language name, like "en" or "en_US"
	 * @param string $options Associative array containing additional options for Zend_Translate
	 *
	 * @link http://framework.zend.com/manual/1.11/en/zend.translate.adapter.html
	 */
	public function __construct( array $translationSources, $adapter, $locale, array $options = array() )
	{
		$this->_translationSources = $translationSources;
		$this->_adapter = (string) $adapter;
		$this->_locale = (string) $locale;
		$this->_options = $options;
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
			return $this->_getTranslation( $domain )->translate( $string, $this->_locale );
		}
		catch( Exception $e )
		{
			; // do nothing at the moment
		}

		return (string) $string;
	}


	/**
	 * Returns the translated singular or plural form of the string depending on the given number.
	 *
	 * @param string $domain Translation domain
	 * @param string $singular String in singular form
	 * @param string $plural String in plural form
	 * @param integer $number Quantity to choose the correct plural form for languages with plural forms
	 * @return string Returns the translated singular or plural form of the string depending on the given number
	 * @throws MW_Translation_Exception Throws exception on initialization of the translation
	 *
	 * @link http://framework.zend.com/manual/en/zend.translate.plurals.html
	 */
	public function dn( $domain, $singular, $plural, $number )
	{
		try
		{
			return $this->_getTranslation( $domain )->plural( $singular, $plural, $number, $this->_locale );
		}
		catch( Exception $e )
		{
			; // do nothing at the moment
		}

		if( $number > 0 ) {
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
	 * Returns the initialized Zend translation object which contains the translations.
	 *
	 * @param string $domain Translation domain
	 * @return Zend_Translate Translation object
	 * @throws MW_Translation_Exception If initialization fails
	 */
	protected function _getTranslation( $domain )
	{
		if ( isset( $this->_translations[ $domain ] ) ) {
			return $this->_translations[ $domain ];
		}

		if ( !isset( $this->_translationSources[ $domain ] ) )
		{
			$msg = sprintf( 'No translation directory for domain "%1$s" available', $domain );
			throw new MW_Translation_Exception( $msg );
		}

		$location = $this->_getTranslationFileLocation( $this->_translationSources[$domain], $this->_locale );

		try
		{
			if ( $this->_translate === null ) {
				$this->_translate = new Zend_Translate( $this->_adapter, $location, $this->_locale, $this->_options );
			}

			/* @todo Set twice with all parameters because of a bug
			 * in ZF >= 1.11.11 (to be checked in future releases of ZF) */
			$options = array( 'adapter' => $this->_adapter, 'content' => $location, 'locale' => $this->_locale );
			$this->_translate->addTranslation( $options + $this->_options );
			$this->_translations[ $domain ] = $this->_translate;

			return $this->_translate;
		}
		catch( Exception $e )
		{
			throw new MW_Translation_Exception( $e->getMessage() );
		}
	}

}
