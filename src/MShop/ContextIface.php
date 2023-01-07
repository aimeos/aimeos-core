<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package MShop
 */


namespace Aimeos\MShop;


/**
 * Common objects which have to be available for all manager objects.
 *
 * @package MShop
 */
interface ContextIface
{
	/**
	 * Returns the cache object.
	 *
	 * @return \Aimeos\Base\Cache\Iface Cache object
	 */
	public function cache() : \Aimeos\Base\Cache\Iface;

	/**
	 * Sets the cache object.
	 *
	 * @param \Aimeos\Base\Cache\Iface $cache Cache object
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setCache( \Aimeos\Base\Cache\Iface $cache ) : \Aimeos\MShop\ContextIface;

	/**
	 * Returns the configuration object.
	 *
	 * @return \Aimeos\Base\Config\Iface Configuration object
	 */
	public function config() : \Aimeos\Base\Config\Iface;

	/**
	 * Sets the configuration object.
	 *
	 * @param \Aimeos\Base\Config\Iface $config Configuration object
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setConfig( \Aimeos\Base\Config\Iface $config ) : \Aimeos\MShop\ContextIface;

	/**
	 * Returns the database connection object.
	 *
	 * @param string $resource Database resource name
	 * @param bool $new Create a new database connection
	 * @return \Aimeos\Base\DB\Manager\Iface Database manager object
	 */
	public function db( string $resource = 'db', bool $new = false ) : \Aimeos\Base\DB\Connection\Iface;

	/**
	 * Sets the database connection manager object.
	 *
	 * @param \Aimeos\Base\DB\Manager\Iface $dbManager Database manager object
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setDatabaseManager( \Aimeos\Base\DB\Manager\Iface $dbManager ) : \Aimeos\MShop\ContextIface;

	/**
	 * Returns the current date and time
	 * This is especially useful to share the same request time or if applications
	 * allow to travel in time.
	 *
	 * @return string Current date and time as ISO string (YYYY-MM-DD HH:mm:ss)
	 */
	public function datetime() : string;

	/**
	 * Sets the current date and time
	 *
	 * @param string $datetime Date and time as ISO string (YYYY-MM-DD HH:mm:ss)
	 */
	public function setDateTime( string $datetime ) : \Aimeos\MShop\ContextIface;

	/**
	 * Returns the file system object for the given resource name.
	 *
	 * @param string $resource Resource name, e.g. "fs-admin"
	 * @return \Aimeos\Base\Filesystem\Iface File system object
	 */
	public function fs( string $resource = 'fs' ) : \Aimeos\Base\Filesystem\Iface;

	/**
	 * Sets the file system manager object.
	 *
	 * @param \Aimeos\Base\Filesystem\Manager\Iface $fsManager File system manager object
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setFilesystemManager( \Aimeos\Base\Filesystem\Manager\Iface $fsManager ) : \Aimeos\MShop\ContextIface;

	/**
	 * Returns the translation/internationalization object for the given locale (null for default one).
	 *
	 * @param string|null $locale Two letter language ISO code for specific language instead of default one
	 * @return \Aimeos\Base\Translation\Iface Internationalization object
	 */
	public function i18n( string $locale = null ) : \Aimeos\Base\Translation\Iface;

	/**
	 * Sets the translation/internationalization objects.
	 *
	 * @param \Aimeos\Base\Translation\Iface[] $translations Associative list locale as key as items as values
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setI18n( array $translations ) : \Aimeos\MShop\ContextIface;

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
	public function translate( string $domain, string $singular, string $plural = null, int $number = 1, string $locale = null ) : string;

	/**
	 * Returns the localization object.
	 *
	 * @return \Aimeos\MShop\Locale\Item\Iface Localization object
	 */
	public function locale() : \Aimeos\MShop\Locale\Item\Iface;

	/**
	 * Sets the localization object.
	 *
	 * @param \Aimeos\MShop\Locale\Item\Iface $locale Localization object
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setLocale( \Aimeos\MShop\Locale\Item\Iface $locale ) : \Aimeos\MShop\ContextIface;

	/**
	 * Returns the logger object.
	 *
	 * @return \Aimeos\Base\Logger\Iface Logger object
	 */
	public function logger() : \Aimeos\Base\Logger\Iface;

	/**
	 * Sets the logger object.
	 *
	 * @param \Aimeos\Base\Logger\Iface $logger Logger object
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setLogger( \Aimeos\Base\Logger\Iface $logger ) : \Aimeos\MShop\ContextIface;

	/**
	 * Returns the mail object.
	 *
	 * @return \Aimeos\Base\Mail\Iface Mail object
	 */
	public function mail() : \Aimeos\Base\Mail\Iface;

	/**
	 * Sets the mail object.
	 *
	 * @param \Aimeos\Base\Mail\Iface $mail Mail object
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setMail( \Aimeos\Base\Mail\Iface $mail ) : \Aimeos\MShop\ContextIface;

	/**
	 * Returns the password adapter object.
	 *
	 * @return \Aimeos\Base\Password\Iface Password adapter
	 */
	public function password() : \Aimeos\Base\Password\Iface;

	/**
	 * Sets the password adapter object.
	 *
	 * @param \Aimeos\Base\Password\Iface $password Password adapter
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setPassword( \Aimeos\Base\Password\Iface $password ) : \Aimeos\MShop\ContextIface;

	/**
	 * Returns the message queue object.
	 *
	 * @param string $resource Resource name, e.g. "mq-email"
	 * @param string $queue Message queue name, e.g. "order/email/payment"
	 * @return \Aimeos\Base\MQueue\Queue\Iface Message queue object
	 */
	public function queue( string $resource, string $queue ) : \Aimeos\Base\MQueue\Queue\Iface;

	/**
	 * Sets the message queue manager object.
	 *
	 * @param \Aimeos\Base\MQueue\Manager\Iface $mqManager Message queue manager object
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setMessageQueueManager( \Aimeos\Base\MQueue\Manager\Iface $mqManager ) : \Aimeos\MShop\ContextIface;

	/**
	 * Returns the nonce value for inline Javascript
	 *
	 * @return string|null Nonce value
	 */
	public function nonce() : ?string;

	/**
	 * Sets the nonce value for inline Javascript
	 *
	 * @param string|null $value Nonce value
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setNonce( ?string $value ) : \Aimeos\MShop\ContextIface;

	/**
	 * Returns the process object.
	 *
	 * @return \Aimeos\Base\Process\Iface Process object
	 */
	public function process() : \Aimeos\Base\Process\Iface;

	/**
	 * Sets the process object.
	 *
	 * @param \Aimeos\Base\Process\Iface $process Process object
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setProcess( \Aimeos\Base\Process\Iface $process ) : \Aimeos\MShop\ContextIface;

	/**
	 * Returns the session object.
	 *
	 * @return \Aimeos\Base\Session\Iface Session object
	 */
	public function session() : \Aimeos\Base\Session\Iface;

	/**
	 * Sets the session object.
	 *
	 * @param \Aimeos\Base\Session\Iface $session Session object
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setSession( \Aimeos\Base\Session\Iface $session ) : \Aimeos\MShop\ContextIface;

	/**
	 * Returns the session token.
	 *
	 * @return string|null Session token
	 */
	public function token() : ?string;

	/**
	 * Sets the ion token.
	 *
	 * @param string $token Session token
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setToken( string $token ) : \Aimeos\MShop\ContextIface;

	/**
	 * Returns the view object.
	 *
	 * @return \Aimeos\Base\View\Iface View object
	 */
	public function view() : \Aimeos\Base\View\Iface;

	/**
	 * Sets the view object.
	 *
	 * @param \Aimeos\Base\View\Iface $view View object
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setView( \Aimeos\Base\View\Iface $view ) : \Aimeos\MShop\ContextIface;

	/**
	 * Returns the account name of the user/editor.
	 *
	 * @return string Account name of the user/editor
	 */
	public function editor() : string;

	/**
	 * Sets the account name of the user/editor.
	 *
	 * @param string $name Account name or IP address of the user/editor
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setEditor( string $name ) : \Aimeos\MShop\ContextIface;

	/**
	 * Returns the user ID of the logged in user.
	 *
	 * @return string|null User ID of the logged in user
	 */
	public function user() : ?string;

	/**
	 * Sets the user ID of the logged in user.
	 *
	 * @param \Closure|string|null $user User ID of the logged in user or closure to retrieve them
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setUserId( $user ) : \Aimeos\MShop\ContextIface;

	/**
	 * Returns the group IDs of the logged in user.
	 *
	 * @return array Group IDs of the logged in user
	 */
	public function groups() : array;

	/**
	 * Sets the group IDs of the logged in user.
	 *
	 * @param \Closure|array $groupIds Group IDs of the logged in user or closure to retrieve them
	 * @return \Aimeos\MShop\ContextIface Context item for chaining method calls
	 */
	public function setGroupIds( $groupIds ) : \Aimeos\MShop\ContextIface;
}
