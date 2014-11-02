<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Context
 */


/**
 * Common objects which must to be available for all manager objects.
 *
 * @package MShop
 * @subpackage Context
 */
class MShop_Context_Item_Default implements MShop_Context_Item_Interface
{
	private $_cache;
	private $_config;
	private $_dbm;
	private $_locale;
	private $_logger;
	private $_session;
	private $_mail;
	private $_view;
	private $_userid;
	private $_editor = '';
	private $_i18n = array();


	/**
	 * Cleans up the stored resources
	 */
	public function __destruct()
	{
		$this->_cache = null;
		$this->_config = null;
		$this->_dbm = null;
		$this->_locale = null;
		$this->_logger = null;
		$this->_session = null;
		$this->_mail = null;
		$this->_view = null;
		$this->_i18n = array();
	}


	/**
	 * Clones internal objects of the context item.
	 */
	public function __clone()
	{
		$this->_cache = ( isset( $this->_cache ) ? clone $this->_cache : null );
		$this->_config = ( isset( $this->_config ) ? clone $this->_config : null );
		$this->_locale = ( isset( $this->_locale ) ? clone $this->_locale : null );
		$this->_logger = ( isset( $this->_logger ) ? clone $this->_logger : null );
		$this->_session = ( isset( $this->_session ) ? clone $this->_session : null );
		$this->_mail = ( isset( $this->_mail ) ? clone $this->_mail : null );
		$this->_view = ( isset( $this->_view ) ? clone $this->_view : null );

		foreach( $this->_i18n as $locale => $object ) {
			$this->_i18n[$locale] = clone $this->_i18n[$locale];
		}
	}


	/**
	 * Returns a hash identifying the context object.
	 *
	 * @return string Hash for identifying the context object
	 */
	public function __toString()
	{
		if( isset( $this->_locale ) ) {
			return spl_object_hash( $this->_locale );
		}

		return '0';
	}


	/**
	 * Sets the cache object.
	 *
	 * @param MW_Cache_Interface $cache Cache object
	 */
	public function setCache( MW_Cache_Interface $cache )
	{
		$this->_cache = $cache;
	}


	/**
	 * Returns the cache object.
	 *
	 * @return MW_Cache_Interface Cache object
	 */
	public function getCache()
	{
		if( !isset( $this->_cache ) ) {
			throw new MShop_Exception( sprintf( 'Cache object not available' ) );
		}

		return $this->_cache;
	}


	/**
	 * Sets the configuration object.
	 *
	 * @param MW_Config_Interface $config Configuration object
	 */
	public function setConfig( MW_Config_Interface $config )
	{
		$this->_config = $config;
	}


	/**
	 * Returns the configuration object.
	 *
	 * @return MShop_Config_Interface Configuration object
	 */
	public function getConfig()
	{
		if( !isset( $this->_config ) ) {
			throw new MShop_Exception( sprintf( 'Configuration object not available' ) );
		}

		return $this->_config;
	}


	/**
	 * Sets the database connection manager object.
	 *
	 * @param MW_DB_Manager_Interface $manager Database manager object
	 */
	public function setDatabaseManager( MW_DB_Manager_Interface $manager )
	{
		$this->_dbm = $manager;
	}


	/**
	 * Returns the database manager object.
	 *
	 * @return MW_DB_Manager_Interface Database manager object
	 */
	public function getDatabaseManager()
	{
		if( !isset( $this->_dbm ) ) {
			throw new MShop_Exception( sprintf( 'Database manager object not available' ) );
		}

		return $this->_dbm;
	}


	/**
	 * Returns the translation/internationalization object for the given locale (null for default one).
	 *
	 * @param string $locale Two letter language ISO code for specific language instead of default one
	 * @return MW_Translation_Interface Internationalization object
	 */
	public function getI18n( $locale = null )
	{
		$locale = ( $locale !== null ? $locale : $this->getLocale()->getLanguageId() );

		if( !isset( $this->_i18n[$locale] ) ) {
			/// Locale ID %1$s
			throw new MShop_Exception( sprintf( 'Internationalization object not available for "%1$s"', $locale ) );
		}

		return $this->_i18n[$locale];
	}


	/**
	 * Sets the translation/internationalization objects.
	 *
	 * @param array $translations Associative list of internationalization objects implementing
	 * 	MW_Translation_Interface with locale as key
	 */
	public function setI18n( array $translations )
	{
		$this->_i18n = $translations;
	}


	/**
	 * Sets the localization object.
	 *
	 * @param MShop_Locale_Item_Interface $locale Localization object
	 */
	public function setLocale( MShop_Locale_Item_Interface $locale )
	{
		$this->_locale = $locale;
	}


	/**
	 * Returns the localization object.
	 *
	 * @return MShop_Locale_Item_Interface Localization object
	 */
	public function getLocale()
	{
		if( !isset( $this->_locale ) ) {
			throw new MShop_Exception( sprintf( 'Locale object not available' ) );
		}

		return $this->_locale;
	}


	/**
	 * Sets the logger object.
	 *
	 * @param MW_Logger_Interface $logger Logger object
	 */
	public function setLogger( MW_Logger_Interface $logger )
	{
		$this->_logger = $logger;
	}


	/**
	 * Returns the logger object.
	 *
	 * @return MW_Logger_Interface Logger object
	 */
	public function getLogger()
	{
		if( !isset( $this->_logger ) ) {
			throw new MShop_Exception( sprintf( 'Log manager object not available' ) );
		}

		return $this->_logger;
	}


	/**
	 * Sets the session object.
	 *
	 * @param MW_Session_Interface $session Session object
	 */
	public function setSession( MW_Session_Interface $session )
	{
		$this->_session = $session;
	}


	/**
	 * Returns the session object.
	 *
	 * @return MW_Session_Interface Session object
	 */
	public function getSession()
	{
		if( !isset( $this->_session ) ) {
			throw new MShop_Exception( sprintf( 'Session object not available' ) );
		}

		return $this->_session;
	}


	/**
	 * Sets the mail object.
	 *
	 * @param MW_Mail_Interface $mail Mail object
	 */
	public function setMail( MW_Mail_Interface $mail )
	{
		$this->_mail = $mail;
	}


	/**
	 * Returns the mail object.
	 *
	 * @return MW_Mail_Interface Mail object
	 */
	public function getMail()
	{
		if( !isset( $this->_mail ) ) {
			throw new MShop_Exception( sprintf( 'Mail object not available' ) );
		}

		return $this->_mail;
	}


	/**
	 * Sets the view object.
	 *
	 * @param MW_View_Interface $view View object
	 */
	public function setView( MW_View_Interface $view )
	{
		$this->_view = $view;
	}


	/**
	 * Returns the view object.
	 *
	 * @return MW_View_Interface View object
	 */
	public function getView()
	{
		if( !isset( $this->_view ) ) {
			throw new MShop_Exception( sprintf( 'View object not available' ) );
		}

		return clone $this->_view;
	}


	/**
	 * Sets the account name of the user/editor.
	 *
	 * @param string $name Account name of the user/editor
	 */
	public function setEditor( $name )
	{
		$this->_editor = (string) $name;
	}


	/**
	 * Returns the account name of the user/editor.
	 *
	 * @return string Account name of the user/editor
	 */
	public function getEditor()
	{
		return $this->_editor;
	}


	/**
	 * Sets the user ID of the logged in user.
	 *
	 * @param string|null $userid User ID of the logged in user
	 */
	public function setUserId( $userid )
	{
		if( $userid === null ) {
			$this->_userid = null;
		}

		$this->_userid = (string) $userid;
	}


	/**
	 * Returns the user ID of the logged in user.
	 *
	 * @return string User ID of the logged in user
	 */
	public function getUserId()
	{
		return $this->_userid;
	}
}
