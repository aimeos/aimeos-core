<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package MShop
 */


namespace Aimeos\MShop;


/**
 * Common objects which must to be available for all manager objects.
 *
 * @package MShop
 */
class Context implements \Aimeos\MShop\ContextIface
{
	private ?\Aimeos\Base\Cache\Iface $cache = null;
	private ?\Aimeos\Base\Config\Iface $config = null;
	private ?\Aimeos\Base\DB\Manager\Iface $db = null;
	private ?\Aimeos\Base\Filesystem\Manager\Iface $fs = null;
	private ?\Aimeos\MShop\Locale\Item\Iface $locale = null;
	private ?\Aimeos\Base\Logger\Iface $logger = null;
	private ?\Aimeos\Base\Mail\Iface $mail = null;
	private ?\Aimeos\Base\MQueue\Manager\Iface $queue = null;
	private ?\Aimeos\Base\Password\Iface $password = null;
	private ?\Aimeos\Base\Process\Iface $process = null;
	private ?\Aimeos\Base\Session\Iface $session = null;
	private ?\Aimeos\Base\View\Iface $view = null;
	private ?string $datetime = null;
	private ?string $nonce = null;
	private ?string $token = null;
	private string $editor = '';
	private array $i18n = [];
	private $groups = null;
	private $user = null;


	/**
	 * Cleans up the stored resources
	 */
	public function __destruct()
	{
		$this->cache = null;
		$this->config = null;
		$this->db = null;
		$this->fs = null;
		$this->locale = null;
		$this->logger = null;
		$this->mail = null;
		$this->queue = null;
		$this->password = null;
		$this->process = null;
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
		$this->fs = ( isset( $this->fs ) ? clone $this->fs : null );
		$this->locale = ( isset( $this->locale ) ? clone $this->locale : null );
		$this->logger = ( isset( $this->logger ) ? clone $this->logger : null );
		$this->mail = ( isset( $this->mail ) ? clone $this->mail : null );
		$this->queue = ( isset( $this->queue ) ? clone $this->queue : null );
		$this->password = ( isset( $this->password ) ? clone $this->password : null );
		$this->process = ( isset( $this->process ) ? clone $this->process : null );
		$this->session = ( isset( $this->session ) ? clone $this->session : null );
		// view is always cloned

		foreach( $this->i18n as $locale => $object ) {
			$this->i18n[$locale] = clone $this->i18n[$locale];
		}
	}


	/**
	 * Cleans up internal objects of the context item
	 */
	public function __sleep() : array
	{
		$objects = array(
			$this->cache, $this->config, $this->db, $this->fs, $this->locale, $this->logger,
			$this->mail, $this->queue, $this->password, $this->process, $this->session, $this->view
		);

		foreach( $objects as $object )
		{
			if( is_object( $object ) && method_exists( $object, '__sleep' ) ) {
				$object->__sleep();
			}
		}

		return get_object_vars( $this );
	}


	/**
	 * Returns a hash identifying the context object.
	 *
	 * @return string Hash for identifying the context object
	 */
	public function __toString() : string
	{
		$objects = array(
			$this, $this->cache, $this->config, $this->db, $this->fs, $this->locale,
			$this->logger, $this->mail, $this->queue, $this->password, $this->process,
			$this->session, $this->view
		);

		return md5( $this->hash( $objects ) );
	}


	/**
	 * Sets the cache object.
	 *
	 * @param \Aimeos\Base\Cache\Iface $cache Cache object
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setCache( \Aimeos\Base\Cache\Iface $cache ) : \Aimeos\MShop\ContextIface
	{
		$this->cache = $cache;

		return $this;
	}


	/**
	 * Returns the cache object.
	 *
	 * @return \Aimeos\Base\Cache\Iface Cache object
	 */
	public function cache() : \Aimeos\Base\Cache\Iface
	{
		if( !isset( $this->cache ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Cache object not available' ) );
		}

		return $this->cache;
	}


	/**
	 * Sets the configuration object.
	 *
	 * @param \Aimeos\Base\Config\Iface $config Configuration object
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setConfig( \Aimeos\Base\Config\Iface $config ) : \Aimeos\MShop\ContextIface
	{
		$this->config = $config;

		return $this;
	}


	/**
	 * Returns the configuration object.
	 *
	 * @return \Aimeos\Base\Config\Iface Configuration object
	 */
	public function config() : \Aimeos\Base\Config\Iface
	{
		if( !isset( $this->config ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Configuration object not available' ) );
		}

		return $this->config;
	}


	/**
	 * Sets the database connection manager object.
	 *
	 * @param \Aimeos\Base\DB\Manager\Iface $manager Database manager object
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setDatabaseManager( \Aimeos\Base\DB\Manager\Iface $manager ) : \Aimeos\MShop\ContextIface
	{
		$this->db = $manager;

		return $this;
	}


	/**
	 * Returns the database connection object.
	 *
	 * @param string $resource Database resource name
	 * @param bool $new Create a new database connection
	 * @return \Aimeos\Base\DB\Manager\Iface Database manager object
	 */
	public function db( string $resource = 'db', bool $new = false ) : \Aimeos\Base\DB\Connection\Iface
	{
		if( !isset( $this->db ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Database manager object not available' ) );
		}

		return $this->db->get( $resource, $new );
	}


	/**
	 * Sets the current date and time
	 *
	 * @param string $datetime Date and time as ISO string (YYYY-MM-DD HH:mm:ss)
	 */
	public function setDateTime( string $datetime ) : \Aimeos\MShop\ContextIface
	{
		$regex = '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/';

		if( preg_match( $regex, (string) $datetime ) !== 1 ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Invalid characters in date "%1$s". ISO format "YYYY-MM-DD hh:mm:ss" expected.', $datetime ) );
		}

		$this->datetime = $datetime;

		return $this;
	}


	/**
	 * Returns the current date and time
	 * This is especially useful to share the same request time or if applications
	 * allow to travel in time.
	 *
	 * @return string Current date and time as ISO string (YYYY-MM-DD HH:mm:ss)
	 */
	public function datetime() : string
	{
		if( $this->datetime === null ) {
			$this->datetime = date( 'Y-m-d H:i:00' );
		}

		return $this->datetime;
	}


	/**
	 * Sets the file system manager object.
	 *
	 * @param \Aimeos\Base\Filesystem\Manager\Iface $manager File system object
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setFilesystemManager( \Aimeos\Base\Filesystem\Manager\Iface $manager ) : \Aimeos\MShop\ContextIface
	{
		$this->fs = $manager;
		return $this;
	}


	/**
	 * Returns the file system object for the given resource name.
	 *
	 * @param string $resource Resource name, e.g. "fs-admin"
	 * @return \Aimeos\Base\Filesystem\Iface File system object
	 */
	public function fs( string $resource = 'fs' ) : \Aimeos\Base\Filesystem\Iface
	{
		if( !isset( $this->fs ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'File system manager object not available' ) );
		}

		return $this->fs->get( $resource );
	}


	/**
	 * Sets the translation/internationalization objects.
	 *
	 * @param array $translations Associative list of internationalization objects implementing
	 * 	\Aimeos\Base\Translation\Iface with locale as key
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setI18n( array $translations ) : \Aimeos\MShop\ContextIface
	{
		$this->i18n = $translations;

		return $this;
	}


	/**
	 * Returns the translation/internationalization object for the given locale (null for default one).
	 *
	 * @param string|null $locale Two letter language ISO code for specific language instead of default one
	 * @return \Aimeos\Base\Translation\Iface Internationalization object
	 */
	public function i18n( string $locale = null ) : \Aimeos\Base\Translation\Iface
	{
		if( isset( $this->locale ) && $locale === null ) {
			$locale = $this->locale()->getLanguageId();
		}

		if( isset( $this->locale ) && $locale === null && reset( $this->i18n ) !== false ) {
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
	 * Translates a string if possible
	 *
	 * @param string $name Name of the translation domain
	 * @param string $singular Singular string to translate
	 * @param string $plural Plural string to translate if count is not one
	 * @param int $number Number for plural translations
	 * @param string|null $locale Locale (e.g. en, en_US, de, etc.) or NULL for current locale
	 * @return string Translated string if possible
	 */
	public function translate( string $domain, string $singular, string $plural = null, int $number = 1, string $locale = null ) : string
	{
		if( empty( $this->i18n ) ) {
			return $number === 1 ? $singular : $plural;
		}

		if( $plural ) {
			return $this->i18n( $locale )->dn( $domain, $singular, $plural, $number );
		}

		return $this->i18n( $locale )->dt( $domain, $singular );
	}


	/**
	 * Sets the localization object.
	 *
	 * @param \Aimeos\MShop\Locale\Item\Iface $locale Localization object
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setLocale( \Aimeos\MShop\Locale\Item\Iface $locale ) : \Aimeos\MShop\ContextIface
	{
		$this->locale = $locale;

		return $this;
	}


	/**
	 * Returns the localization object.
	 *
	 * @return \Aimeos\MShop\Locale\Item\Iface Localization object
	 */
	public function locale() : \Aimeos\MShop\Locale\Item\Iface
	{
		if( !isset( $this->locale ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Locale object not available' ) );
		}

		return $this->locale;
	}


	/**
	 * Sets the logger object.
	 *
	 * @param \Aimeos\Base\Logger\Iface $logger Logger object
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setLogger( \Aimeos\Base\Logger\Iface $logger ) : \Aimeos\MShop\ContextIface
	{
		$this->logger = $logger;

		return $this;
	}


	/**
	 * Returns the logger object.
	 *
	 * @return \Aimeos\Base\Logger\Iface Logger object
	 */
	public function logger() : \Aimeos\Base\Logger\Iface
	{
		if( !isset( $this->logger ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Log manager object not available' ) );
		}

		return $this->logger;
	}


	/**
	 * Sets the mail object.
	 *
	 * @param \Aimeos\Base\Mail\Iface $mail Mail object
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setMail( \Aimeos\Base\Mail\Iface $mail ) : \Aimeos\MShop\ContextIface
	{
		$this->mail = $mail;

		return $this;
	}


	/**
	 * Returns the mail object.
	 *
	 * @return \Aimeos\Base\Mail\Iface Mail object
	 */
	public function mail() : \Aimeos\Base\Mail\Iface
	{
		if( !isset( $this->mail ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Mail object not available' ) );
		}

		return $this->mail;
	}


	/**
	 * Sets the message queue manager object.
	 *
	 * @param \Aimeos\Base\MQueue\Manager\Iface $mqManager Message queue manager object
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setMessageQueueManager( \Aimeos\Base\MQueue\Manager\Iface $mqManager ) : \Aimeos\MShop\ContextIface
	{
		$this->queue = $mqManager;

		return $this;
	}


	/**
	 * Returns the message queue object.
	 *
	 * @param string $resource Resource name, e.g. "mq-email"
	 * @param string $queue Message queue name, e.g. "order/email/payment"
	 * @return \Aimeos\Base\MQueue\Queue\Iface Message queue object
	 */
	public function queue( string $resource, string $queue ) : \Aimeos\Base\MQueue\Queue\Iface
	{
		if( !isset( $this->queue ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Message queue object not available' ) );
		}

		return $this->queue->get( $resource )->getQueue( $queue );
	}


	/**
	 * Returns the nonce value for inline Javascript
	 *
	 * @return string|null Nonce value
	 */
	public function nonce() : ?string
	{
		return $this->nonce;
	}


	/**
	 * Sets the nonce value for inline Javascript
	 *
	 * @param string $value Nonce value
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setNonce( ?string $value ) : \Aimeos\MShop\ContextIface
	{
		$this->nonce = $value;
		return $this;
	}


	/**
	 * Returns the password adapter object.
	 *
	 * @return \Aimeos\Base\Password\Iface Password adapter
	 */
	public function password() : \Aimeos\Base\Password\Iface
	{
		if( !isset( $this->password ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Password object not available' ) );
		}

		return $this->password;
	}


	/**
	 * Sets the password adapter object.
	 *
	 * @param \Aimeos\Base\Password\Iface $password Password adapter
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setPassword( \Aimeos\Base\Password\Iface $password ) : \Aimeos\MShop\ContextIface
	{
		$this->password = $password;
		return $this;
	}


	/**
	 * Sets the process object.
	 *
	 * @param \Aimeos\Base\Process\Iface $process Process object
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setProcess( \Aimeos\Base\Process\Iface $process ) : \Aimeos\MShop\ContextIface
	{
		$this->process = $process;

		return $this;
	}


	/**
	 * Returns the process object.
	 *
	 * @return \Aimeos\Base\Process\Iface Process object
	 */
	public function process() : \Aimeos\Base\Process\Iface
	{
		if( !isset( $this->process ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Process object not available' ) );
		}

		return $this->process;
	}


	/**
	 * Sets the session object.
	 *
	 * @param \Aimeos\Base\Session\Iface $session Session object
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setSession( \Aimeos\Base\Session\Iface $session ) : \Aimeos\MShop\ContextIface
	{
		$this->session = $session;

		return $this;
	}


	/**
	 * Returns the session object.
	 *
	 * @return \Aimeos\Base\Session\Iface Session object
	 */
	public function session() : \Aimeos\Base\Session\Iface
	{
		if( !isset( $this->session ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Session object not available' ) );
		}

		return $this->session;
	}

	/**
	 * Returns the session token.
	 *
	 * @return string|null Session token
	 */
	public function token() : ?string
	{
		return $this->token;
	}


	/**
	 * Sets the ion token.
	 *
	 * @param string $token Session token
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setToken( string $token ) : \Aimeos\MShop\ContextIface
	{
		$this->token = $token;
		return $this;
	}


	/**
	 * Sets the view object.
	 *
	 * @param \Aimeos\Base\View\Iface $view View object
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setView( \Aimeos\Base\View\Iface $view ) : \Aimeos\MShop\ContextIface
	{
		$this->view = $view;

		return $this;
	}


	/**
	 * Returns the view object.
	 *
	 * @return \Aimeos\Base\View\Iface View object
	 */
	public function view() : \Aimeos\Base\View\Iface
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
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setEditor( string $name ) : \Aimeos\MShop\ContextIface
	{
		$this->editor = $name;

		return $this;
	}


	/**
	 * Returns the account name of the user/editor.
	 *
	 * @return string Account name of the user/editor
	 */
	public function editor() : string
	{
		return $this->editor;
	}


	/**
	 * Sets the user ID of the logged in user.
	 *
	 * @param \Closure|string|null $user User ID of the logged in user or closure to retrieve them
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setUserId( $user ) : \Aimeos\MShop\ContextIface
	{
		$this->user = $user;

		return $this;
	}


	/**
	 * Returns the user ID of the logged in user.
	 *
	 * @return string|null User ID of the logged in user
	 */
	public function user() : ?string
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
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setGroupIds( $groupIds ) : \Aimeos\MShop\ContextIface
	{
		$this->groups = $groupIds;

		return $this;
	}


	/**
	 * Returns the group IDs of the logged in user.
	 *
	 * @return array Group IDs of the logged in user
	 */
	public function groups() : array
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
	private function hash( array $list ) : string
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
