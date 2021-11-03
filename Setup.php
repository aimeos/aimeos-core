<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos;


/**
 * Setup Aimeos data structures and storeages
 */
class Setup
{
	private $bootstrap;
	private $context;
	private $options;
	private $verbose;


	/**
	 * Initializes the Aimeos setup object
	 *
	 * @param Bootstrap $bootstrap Aimeos bootstrap object
	 * @param array $options Associative list of config keys and values
	 */
	public function __construct( Bootstrap $bootstrap, array $options = [] )
	{
		$this->bootstrap = $bootstrap;
		$this->options = $options;

		$this->macros();
	}


	/**
	 * Creates a new initialized setup object
	 *
	 * @param Bootstrap $bootstrap Aimeos bootstrap object
	 * @param array $options Associative list of config keys and values
	 * @return self Aimeos setup object
	 */
	public static function use( Bootstrap $bootstrap, array $options = [] ) : self
	{
		return new static( $bootstrap, $options );
	}


	/**
	 * Sets a custom context object
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 * @return self Same object for fluid method calls
	 */
	public function context( \Aimeos\MShop\Context\Item\Iface $context ) : self
	{
		$this->context = $context;
		return $this;
	}


	/**
	 * Performs the migrations
	 *
	 * @param string $site Site code to execute the migrations for
	 * @param string $template Name of the migration template
	 */
	public function up( string $site = 'default', string $template = 'default' )
	{
		$ctx = $this->context ?? $this->createContext();

		\Aimeos\Upscheme\Task\Base::macro( 'context', function() use ( $ctx ) {
			return $ctx;
		} );


		$config = $ctx->config();
		$config->set( 'setup/site', $site );
		$dbconf = $this->getDBConfig( $config );
		$taskPaths = $this->bootstrap->getSetupPaths( $template );

		\Aimeos\Upscheme\Up::use( $dbconf, $taskPaths )->verbose( $this->verbose )->up();
	}


	/**
	 * Sets the verbosity level
	 *
	 * @param mixed $level Verbosity level (empty: none, v: notice: vv: info, vvv: debug)
	 * @return self Same object for fluid method calls
	 */
	public function verbose( $level = 'vv' ) : self
	{
		$this->verbose = $level;
		return $this;
	}


	/**
	 * Returns a new configuration object
	 *
	 * @param array $confPaths List of configuration paths from the bootstrap object
	 * @param array $options Associative list of configuration options as key/value pairs
	 * @return \Aimeos\MW\Config\Iface Configuration object
	 */
	protected function createConfig( array $confPaths, array $options ) : \Aimeos\MW\Config\Iface
	{
		$config = [];

		foreach( (array) ( $options['config'] ?? [] ) as $path )
		{
			if( is_file( $path ) ) {
				$config = array_replace_recursive( $config, require $path );
			} else {
				$confPaths[] = $path;
			}
		}

		$conf = new \Aimeos\MW\Config\PHPArray( $config, $confPaths );
		$conf = new \Aimeos\MW\Config\Decorator\Memory( $conf );

		foreach( (array) ( $options['option'] ?? [] ) as $option )
		{
			$parts = explode( ':', $option );

			if( count( $parts ) !== 2 ) {
				throw new \RuntimeException( "Invalid config option \"%1\$s\"\n", $option );
			}

			$conf->set( str_replace( '\\', '/', $parts[0] ), $parts[1] );
		}

		return $conf;
	}


	/**
	 * Returns a new context object
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface New context object
	 */
	protected function createContext() : \Aimeos\MShop\Context\Item\Iface
	{
		$conf = $this->createConfig( $this->bootstrap->getConfigPaths(), $this->options );

		$ctx = new \Aimeos\MShop\Context\Item\Standard();
		$ctx->setConfig( $conf );

		$dbm = new \Aimeos\MW\DB\Manager\DBAL( $conf );
		$ctx->setDatabaseManager( $dbm );

		$logger = new \Aimeos\MW\Logger\Errorlog( \Aimeos\MW\Logger\Base::INFO );
		$ctx->setLogger( $logger );

		$password = new \Aimeos\MW\Password\Standard();
		$ctx->setPassword( $password );

		$session = new \Aimeos\MW\Session\None();
		$ctx->setSession( $session );

		$cache = new \Aimeos\MW\Cache\None();
		$ctx->setCache( $cache );

		$process = new \Aimeos\MW\Process\Pcntl( $conf->get( 'pcntl_max', 4 ), $conf->get( 'pcntl_priority', 19 ) );
		$process = new \Aimeos\MW\Process\Decorator\Check( $process );
		$ctx->setProcess( $process );

		return $ctx;
	}


	/**
	 * Returns the database configuration
	 *
	 * @param \Aimeos\MW\Config\Iface $conf Configuration object
	 * @return array Database configuration
	 */
	protected function getDBConfig( \Aimeos\MW\Config\Iface $conf ) : array
	{
		$dbconfig = $conf->get( 'resource', [] );

		foreach( $dbconfig as $rname => $dbconf )
		{
			if( strncmp( $rname, 'db', 2 ) !== 0 ) {
				unset( $dbconfig[$rname] );
			} else {
				$conf->set( 'resource/' . $rname . '/limit', 5 );
			}
		}

		return $dbconfig;
	}


	/**
	 * Adds the required marcos to the Upscheme objects
	 */
	protected function macros()
	{
		\Aimeos\Upscheme\Up::macro( 'connect', function( array $cfg ) {

			switch( $cfg['adapter'] )
			{
				case 'mysql': $cfg['driver'] = 'pdo_mysql'; break;
				case 'pgsql': $cfg['driver'] = 'pdo_pgsql'; break;
				case 'sqlsrv': $cfg['driver'] = 'pdo_sqlsrv'; break;
				default: $cfg['driver'] = $cfg['adapter'];
			}

			if( isset( $cfg['database'] ) ) {
				$cfg['dbname'] = $cfg['database'];
			}

			if( isset( $cfg['username'] ) ) {
				$cfg['user'] = $cfg['username'];
			}

			unset( $cfg['adapter'], $cfg['database'], $cfg['username'] );

			return \Doctrine\DBAL\DriverManager::getConnection( $cfg );
		} );


		\Aimeos\Upscheme\Schema\Table::macro( 'startend', function() {
			$this->datetime( 'start' )->null( true );
			return $this->datetime( 'end' )->null( true );
		} );

		\Aimeos\Upscheme\Schema\Table::macro( 'code', function( string $name = 'code' ) {
			return $this->string( $name, 64 )
				->opt( 'charset', 'utf8', 'mysql' )
				->opt( 'collation', 'utf8_bin', 'mysql' )
				->default( '' );
		} );

		\Aimeos\Upscheme\Schema\Table::macro( 'type', function( string $name = 'type' ) {
			return $this->string( $name, 64 )
				->opt( 'charset', 'utf8', 'mysql' )
				->opt( 'collation', 'utf8_bin', 'mysql' )
				->default( '' );
		} );

		\Aimeos\Upscheme\Schema\Table::macro( 'refid', function( string $name = 'refid' ) {
			return $this->string( $name, 36 )
				->opt( 'charset', 'utf8', 'mysql' )
				->opt( 'collation', 'utf8_bin', 'mysql' )
				->default( '' );
		} );

		\Aimeos\Upscheme\Schema\Table::macro( 'meta', function() {
			$this->datetime( 'mtime' );
			$this->datetime( 'ctime' );
			return $this->string( 'editor' );
		} );
	}
}
