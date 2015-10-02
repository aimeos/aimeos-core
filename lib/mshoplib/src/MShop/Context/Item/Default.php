<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
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
	private $cache;
	private $config;
	private $dbm;
	private $locale;
	private $logger;
	private $session;
	private $mail;
	private $view;
	private $user;
	private $groups;
	private $editor = '';
	private $i18n = array();


	/**
	 * Cleans up the stored resources
	 */
	public function __destruct()
	{
		$this->cache = null;
		$this->config = null;
		$this->dbm = null;
		$this->locale = null;
		$this->logger = null;
		$this->session = null;
		$this->mail = null;
		$this->view = null;
		$this->i18n = array();
	}


	/**
	 * Clones internal objects of the context item.
	 */
	public function __clone()
	{
		$this->cache = ( isset( $this->cache ) ? clone $this->cache : null );
		$this->config = ( isset( $this->config ) ? clone $this->config : null );
		$this->locale = ( isset( $this->locale ) ? clone $this->locale : null );
		$this->logger = ( isset( $this->logger ) ? clone $this->logger : null );
		$this->session = ( isset( $this->session ) ? clone $this->session : null );
		$this->mail = ( isset( $this->mail ) ? clone $this->mail : null );
		// view is always cloned

		foreach( $this->i18n as $locale => $object ) {
			$this->i18n[$locale] = clone $this->i18n[$locale];
		}
	}


	/**
	 * Returns a hash identifying the context object.
	 *
	 * @return string Hash for identifying the context object
	 */
	public function __toString()
	{
		if( isset( $this->locale ) ) {
			return spl_object_hash( $this->locale );
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
		$this->cache = $cache;
	}


	/**
	 * Returns the cache object.
	 *
	 * @return MW_Cache_Interface Cache object
	 */
	public function getCache()
	{
		if( !isset( $this->cache ) ) {
			throw new MShop_Exception( sprintf( 'Cache object not available' ) );
		}

		return $this->cache;
	}


	/**
	 * Sets the configuration object.
	 *
	 * @param MW_Config_Interface $config Configuration object
	 */
	public function setConfig( MW_Config_Interface $config )
	{
		$this->config = $config;
	}


	/**
	 * Returns the configuration object.
	 *
	 * @return MShop_Config_Interface Configuration object
	 */
	public function getConfig()
	{
		if( !isset( $this->config ) ) {
			throw new MShop_Exception( sprintf( 'Configuration object not available' ) );
		}

		return $this->config;
	}


	/**
	 * Sets the database connection manager object.
	 *
	 * @param MW_DB_Manager_Interface $manager Database manager object
	 */
	public function setDatabaseManager( MW_DB_Manager_Interface $manager )
	{
		$this->dbm = $manager;
	}


	/**
	 * Returns the database manager object.
	 *
	 * @return MW_DB_Manager_Interface Database manager object
	 */
	public function getDatabaseManager()
	{
		if( !isset( $this->dbm ) ) {
			throw new MShop_Exception( sprintf( 'Database manager object not available' ) );
		}

		return $this->dbm;
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

		if( !isset( $this->i18n[$locale] ) ) {
			/// Locale ID %1$s
			throw new MShop_Exception( sprintf( 'Internationalization object not available for "%1$s"', $locale ) );
		}

		return $this->i18n[$locale];
	}


	/**
	 * Sets the translation/internationalization objects.
	 *
	 * @param array $translations Associative list of internationalization objects implementing
	 * 	MW_Translation_Interface with locale as key
	 */
	public function setI18n( array $translations )
	{
		$this->i18n = $translations;
	}


	/**
	 * Sets the localization object.
	 *
	 * @param MShop_Locale_Item_Interface $locale Localization object
	 */
	public function setLocale( MShop_Locale_Item_Interface $locale )
	{
		$this->locale = $locale;
	}


	/**
	 * Returns the localization object.
	 *
	 * @return MShop_Locale_Item_Interface Localization object
	 */
	public function getLocale()
	{
		if( !isset( $this->locale ) ) {
			throw new MShop_Exception( sprintf( 'Locale object not available' ) );
		}

		return $this->locale;
	}


	/**
	 * Sets the logger object.
	 *
	 * @param MW_Logger_Interface $logger Logger object
	 */
	public function setLogger( MW_Logger_Interface $logger )
	{
		$this->logger = $logger;
	}


	/**
	 * Returns the logger object.
	 *
	 * @return MW_Logger_Interface Logger object
	 */
	public function getLogger()
	{
		if( !isset( $this->logger ) ) {
			throw new MShop_Exception( sprintf( 'Log manager object not available' ) );
		}

		return $this->logger;
	}


	/**
	 * Sets the session object.
	 *
	 * @param MW_Session_Interface $session Session object
	 */
	public function setSession( MW_Session_Interface $session )
	{
		$this->session = $session;
	}


	/**
	 * Returns the session object.
	 *
	 * @return MW_Session_Interface Session object
	 */
	public function getSession()
	{
		if( !isset( $this->session ) ) {
			throw new MShop_Exception( sprintf( 'Session object not available' ) );
		}

		return $this->session;
	}


	/**
	 * Sets the mail object.
	 *
	 * @param MW_Mail_Interface $mail Mail object
	 */
	public function setMail( MW_Mail_Interface $mail )
	{
		$this->mail = $mail;
	}


	/**
	 * Returns the mail object.
	 *
	 * @return MW_Mail_Interface Mail object
	 */
	public function getMail()
	{
		if( !isset( $this->mail ) ) {
			throw new MShop_Exception( sprintf( 'Mail object not available' ) );
		}

		return $this->mail;
	}


	/**
	 * Sets the view object.
	 *
	 * @param MW_View_Interface $view View object
	 */
	public function setView( MW_View_Interface $view )
	{
		$this->view = $view;
	}


	/**
	 * Returns the view object.
	 *
	 * @return MW_View_Interface View object
	 */
	public function getView()
	{
		if( !isset( $this->view ) ) {
			throw new MShop_Exception( sprintf( 'View object not available' ) );
		}

		return clone $this->view;
	}


	/**
	 * Sets the account name of the user/editor.
	 *
	 * @param string $name Account name of the user/editor
	 */
	public function setEditor( $name )
	{
		$this->editor = (string) $name;
	}


	/**
	 * Returns the account name of the user/editor.
	 *
	 * @return string Account name of the user/editor
	 */
	public function getEditor()
	{
		return $this->editor;
	}


	/**
	 * Sets the user ID of the logged in user.
	 *
	 * @param closure|string|null $user User ID of the logged in user or closure to retrieve them
	 */
	public function setUserId( $user )
	{
		$this->user = $user;
	}


	/**
	 * Returns the user ID of the logged in user.
	 *
	 * @return string User ID of the logged in user
	 */
	public function getUserId()
	{
		if( $this->user instanceof Closure )
		{
			$fcn = $this->user;
			$this->user = $fcn();
		}

		return $this->user;
	}


	/**
	 * Sets the group IDs of the logged in user.
	 *
	 * @param closure|array $groupIds Group IDs of the logged in user or closure to retrieve them
	 */
	public function setGroupIds( $groupIds )
	{
		$this->groups = $groupIds;
	}


	/**
	 * Returns the group IDs of the logged in user.
	 *
	 * @return array Group IDs of the logged in user
	 */
	public function getGroupIds()
	{
		if( $this->groups instanceof Closure )
		{
			$fcn = $this->groups;
			$this->groups = $fcn();
		}

		return (array) $this->groups;
	}
}
