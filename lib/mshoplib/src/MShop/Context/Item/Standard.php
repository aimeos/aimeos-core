<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Context
 */


namespace Aimeos\MShop\Context\Item;


/**
 * Common objects which must to be available for all manager objects.
 *
 * @package MShop
 * @subpackage Context
 */
class Standard implements \Aimeos\MShop\Context\Item\Iface
{
	private $cache;
	private $config;
	private $dbm;
	private $fsm;
	private $locale;
	private $logger;
	private $mail;
	private $mqueue;
	private $session;
	private $view;
	private $user;
	private $groups;
	private $editor = '';
	private $i18n = [];


	/**
	 * Cleans up the stored resources
	 */
	public function __destruct()
	{
		$this->cache = null;
		$this->config = null;
		$this->dbm = null;
		$this->fsm = null;
		$this->locale = null;
		$this->logger = null;
		$this->mail = null;
		$this->mqueue = null;
		$this->session = null;
		$this->view = null;
		$this->i18n = [];
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
		$this->mail = ( isset( $this->mail ) ? clone $this->mail : null );
		$this->mqueue = ( isset( $this->mqueue ) ? clone $this->mqueue : null );
		$this->session = ( isset( $this->session ) ? clone $this->session : null );
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
		$objects = array(
			$this, $this->cache, $this->config, $this->dbm, $this->fsm, $this->locale,
			$this->logger, $this->mail, $this->mqueue, $this->session, $this->view
		);

		return md5( $this->hash( $objects ) );
	}


	/**
	 * Sets the cache object.
	 *
	 * @param \Aimeos\MW\Cache\Iface $cache Cache object
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setCache( \Aimeos\MW\Cache\Iface $cache )
	{
		$this->cache = $cache;

		return $this;
	}


	/**
	 * Returns the cache object.
	 *
	 * @return \Aimeos\MW\Cache\Iface Cache object
	 */
	public function getCache()
	{
		if( !isset( $this->cache ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Cache object not available' ) );
		}

		return $this->cache;
	}


	/**
	 * Sets the configuration object.
	 *
	 * @param \Aimeos\MW\Config\Iface $config Configuration object
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setConfig( \Aimeos\MW\Config\Iface $config )
	{
		$this->config = $config;

		return $this;
	}


	/**
	 * Returns the configuration object.
	 *
	 * @return \Aimeos\MShop\Config\Iface Configuration object
	 */
	public function getConfig()
	{
		if( !isset( $this->config ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Configuration object not available' ) );
		}

		return $this->config;
	}


	/**
	 * Sets the database connection manager object.
	 *
	 * @param \Aimeos\MW\DB\Manager\Iface $manager Database manager object
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setDatabaseManager( \Aimeos\MW\DB\Manager\Iface $manager )
	{
		$this->dbm = $manager;

		return $this;
	}


	/**
	 * Returns the database manager object.
	 *
	 * @return \Aimeos\MW\DB\Manager\Iface Database manager object
	 */
	public function getDatabaseManager()
	{
		if( !isset( $this->dbm ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Database manager object not available' ) );
		}

		return $this->dbm;
	}


	/**
	 * Sets the file system manager object.
	 *
	 * @param \Aimeos\MW\Filesystem\Manager\Iface $manager File system object
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setFilesystemManager( \Aimeos\MW\Filesystem\Manager\Iface $manager )
	{
		$this->fsm = $manager;

		return $this;
	}


	/**
	 * Returns the file system manager object.
	 *
	 * @return \Aimeos\MW\Filesystem\Manager\Iface File system manager object
	 */
	public function getFilesystemManager()
	{
		if( !isset( $this->fsm ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'File system manager object not available' ) );
		}

		return $this->fsm;
	}


	/**
	 * Returns the file system object for the given resource name.
	 *
	 * @param string $resource Resource name, e.g. "fs-admin"
	 * @return \Aimeos\MW\Filesystem\Iface File system object
	 */
	public function getFilesystem( $resource )
	{
		if( !isset( $this->fsm ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'File system manager object not available' ) );
		}

		return $this->fsm->get( $resource );
	}


	/**
	 * Sets the translation/internationalization objects.
	 *
	 * @param array $translations Associative list of internationalization objects implementing
	 * 	\Aimeos\MW\Translation\Iface with locale as key
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setI18n( array $translations )
	{
		$this->i18n = $translations;

		return $this;
	}


	/**
	 * Returns the translation/internationalization object for the given locale (null for default one).
	 *
	 * @param string|null $locale Two letter language ISO code for specific language instead of default one
	 * @return \Aimeos\MW\Translation\Iface Internationalization object
	 */
	public function getI18n( $locale = null )
	{
		if( $locale === null ) {
			$locale = $this->getLocale()->getLanguageId();
		}

		if( $locale === null && reset( $this->i18n ) !== false ) {
			$locale = key( $this->i18n );
		}

		if( isset( $this->i18n[$locale] ) ) {
			return $this->i18n[$locale];
		}

		if( isset( $this->i18n['en'] ) ) {
			return $this->i18n['en'];
		}

		/// Locale ID %1$s
		throw new \Aimeos\MShop\Exception( sprintf( 'Internationalization object not available for "%1$s"', $locale ) );
	}


	/**
	 * Sets the localization object.
	 *
	 * @param \Aimeos\MShop\Locale\Item\Iface $locale Localization object
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setLocale( \Aimeos\MShop\Locale\Item\Iface $locale )
	{
		$this->locale = $locale;

		return $this;
	}


	/**
	 * Returns the localization object.
	 *
	 * @return \Aimeos\MShop\Locale\Item\Iface Localization object
	 */
	public function getLocale()
	{
		if( !isset( $this->locale ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Locale object not available' ) );
		}

		return $this->locale;
	}


	/**
	 * Sets the logger object.
	 *
	 * @param \Aimeos\MW\Logger\Iface $logger Logger object
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setLogger( \Aimeos\MW\Logger\Iface $logger )
	{
		$this->logger = $logger;

		return $this;
	}


	/**
	 * Returns the logger object.
	 *
	 * @return \Aimeos\MW\Logger\Iface Logger object
	 */
	public function getLogger()
	{
		if( !isset( $this->logger ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Log manager object not available' ) );
		}

		return $this->logger;
	}


	/**
	 * Sets the mail object.
	 *
	 * @param \Aimeos\MW\Mail\Iface $mail Mail object
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setMail( \Aimeos\MW\Mail\Iface $mail )
	{
		$this->mail = $mail;

		return $this;
	}


	/**
	 * Returns the mail object.
	 *
	 * @return \Aimeos\MW\Mail\Iface Mail object
	 */
	public function getMail()
	{
		if( !isset( $this->mail ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Mail object not available' ) );
		}

		return $this->mail;
	}


	/**
	 * Sets the message queue manager object.
	 *
	 * @param \Aimeos\MW\MQueue\Manager\Iface $mqManager Message queue manager object
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setMessageQueueManager( \Aimeos\MW\MQueue\Manager\Iface $mqManager )
	{
		$this->mqueue = $mqManager;

		return $this;
	}


	/**
	 * Returns the message queue manager object.
	 *
	 * @return \Aimeos\MW\MQueue\Manager\Iface Message queue manager object
	*/
	public function getMessageQueueManager()
	{
		if( !isset( $this->mqueue ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Message queue object not available' ) );
		}

		return $this->mqueue;
	}


	/**
	 * Returns the message queue object.
	 *
	 * @param string $resource Resource name, e.g. "mq-email"
	 * @apram string $queue Message queue name, e.g. "order/email/payment"
	 * @return \Aimeos\MW\Queue\Manager\Iface Message queue object
	*/
	public function getMessageQueue( $resource, $queue )
	{
		if( !isset( $this->mqueue ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Message queue object not available' ) );
		}

		return $this->mqueue->get( $resource )->getQueue( $queue );
	}


	/**
	 * Sets the session object.
	 *
	 * @param \Aimeos\MW\Session\Iface $session Session object
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setSession( \Aimeos\MW\Session\Iface $session )
	{
		$this->session = $session;

		return $this;
	}


	/**
	 * Returns the session object.
	 *
	 * @return \Aimeos\MW\Session\Iface Session object
	 */
	public function getSession()
	{
		if( !isset( $this->session ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Session object not available' ) );
		}

		return $this->session;
	}


	/**
	 * Sets the view object.
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setView( \Aimeos\MW\View\Iface $view )
	{
		$this->view = $view;

		return $this;
	}


	/**
	 * Returns the view object.
	 *
	 * @return \Aimeos\MW\View\Iface View object
	 */
	public function getView()
	{
		if( !isset( $this->view ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'View object not available' ) );
		}

		return clone $this->view;
	}


	/**
	 * Sets the account name of the user/editor.
	 *
	 * @param string $name Account name of the user/editor
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setEditor( $name )
	{
		$this->editor = (string) $name;

		return $this;
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
	 * @param \Closure|string|null $user User ID of the logged in user or closure to retrieve them
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setUserId( $user )
	{
		$this->user = $user;

		return $this;
	}


	/**
	 * Returns the user ID of the logged in user.
	 *
	 * @return string User ID of the logged in user
	 */
	public function getUserId()
	{
		if( $this->user instanceof \Closure )
		{
			$fcn = $this->user;
			$this->user = $fcn();
		}

		return $this->user;
	}


	/**
	 * Sets the group IDs of the logged in user.
	 *
	 * @param \Closure|array $groupIds Group IDs of the logged in user or closure to retrieve them
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setGroupIds( $groupIds )
	{
		$this->groups = $groupIds;

		return $this;
	}


	/**
	 * Returns the group IDs of the logged in user.
	 *
	 * @return array Group IDs of the logged in user
	 */
	public function getGroupIds()
	{
		if( $this->groups instanceof \Closure )
		{
			$fcn = $this->groups;
			$this->groups = $fcn();
		}

		return (array) $this->groups;
	}


	/**
	 * Returns a hash for the given objects
	 *
	 * @param array $list List of objects
	 * @return string Hash for the objects
	 */
	private function hash( array $list )
	{
		$hash = '';

		foreach( $list as $item )
		{
			if( is_object( $item ) ) {
				$hash .= spl_object_hash( $item );
			}
		}

		return $hash;
	}
}
