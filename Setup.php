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
	private $config;
	private $verbose;


	/**
	 * Initializes the Aimeos setup object
	 *
	 * @param Bootstrap $bootstrap Aimeos bootstrap object
	 * @param array $config Associative list of config keys and values
	 */
	public function __construct( Bootstrap $bootstrap, array $config = [] )
	{
		$this->bootstrap = $bootstrap;
		$this->config = $config;

		$this->macros();
	}


	/**
	 * Creates a new initialized setup object
	 *
	 * @param Bootstrap $bootstrap Aimeos bootstrap object
	 * @param array $config Associative list of config keys and values
	 * @return self Aimeos setup object
	 */
	public static function use( Bootstrap $bootstrap, array $config = [] ) : self
	{
		return new static( $bootstrap, $config );
	}


	/**
	 * Sets a custom context object
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 * @return self Same object for fluid method calls
	 */
	public function context( \Aimeos\MShop\ContextIface $context ) : self
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
		$ctx = ( $this->context ?? $this->createContext() )->setEditor( 'setup' );

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
	 * @param mixed $level Verbosity level (empty: none, v: notice, vv: info, vvv: debug)
	 * @return self Same object for fluid method calls
	 */
	public function verbose( $level = 'v' ) : self
	{
		$this->verbose = $level;
		return $this;
	}


	/**
	 * Returns a new context object
	 *
	 * @return \Aimeos\MShop\ContextIface New context object
	 */
	protected function createContext() : \Aimeos\MShop\ContextIface
	{
		$ctx = new \Aimeos\MShop\Context();

		$conf = new \Aimeos\Base\Config\PHPArray( [], $this->bootstrap->getConfigPaths() );

		foreach( $this->config as $key => $value ) {
			$conf->set( $key, $value );
		}

		$conf = new \Aimeos\Base\Config\Decorator\Memory( $conf );
		$ctx->setConfig( $conf );

		$dbm = new \Aimeos\Base\DB\Manager\Standard( $conf->get( 'resource', [] ), 'DBAL' );
		$ctx->setDatabaseManager( $dbm );

		$fsm = new \Aimeos\Base\Filesystem\Manager\Standard( $conf->get( 'resource', [] ) );
		$ctx->setFilesystemManager( $fsm );

		$logger = new \Aimeos\Base\Logger\Errorlog( \Aimeos\Base\Logger\Iface::INFO );
		$ctx->setLogger( $logger );

		$password = new \Aimeos\Base\Password\Standard();
		$ctx->setPassword( $password );

		$session = new \Aimeos\Base\Session\None();
		$ctx->setSession( $session );

		$cache = new \Aimeos\Base\Cache\None();
		$ctx->setCache( $cache );

		$process = new \Aimeos\Base\Process\Pcntl( $conf->get( 'pcntl_max', 4 ), $conf->get( 'pcntl_priority', 19 ) );
		$process = new \Aimeos\Base\Process\Decorator\Check( $process );
		$ctx->setProcess( $process );

		return $ctx;
	}


	/**
	 * Returns the database configuration
	 *
	 * @param \Aimeos\Base\Config\Iface $conf Configuration object
	 * @return array Database configuration
	 */
	protected function getDBConfig( \Aimeos\Base\Config\Iface $conf ) : array
	{
		$dbconfig = $conf->get( 'resource', [] );

		foreach( $dbconfig as $rname => $entry )
		{
			if( strncmp( $rname, 'db', 2 ) !== 0 ) {
				unset( $dbconfig[$rname] );
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
				case 'oracle': $cfg['driver'] = 'pdo_oci'; break;
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


		$codelen = $this->config['codelength'] ?? 64;

		\Aimeos\Upscheme\Schema\Table::macro( 'startend', function() {
			$this->datetime( 'start' )->null( true );
			return $this->datetime( 'end' )->null( true );
		} );

		\Aimeos\Upscheme\Schema\Table::macro( 'code', function( string $name = 'code' ) use ( $codelen ) {
			return $this->string( $name, $codelen )
				->opt( 'charset', 'utf8mb4', 'mysql' )
				->opt( 'collation', 'utf8mb4_bin', 'mysql' )
				->default( '' );
		} );

		\Aimeos\Upscheme\Schema\Table::macro( 'config', function( string $name = 'config' ) {
			return $this->text( $name )
				->opt( 'charset', 'utf8mb4', 'mysql' )
				->opt( 'collation', 'utf8mb4_general_ci', 'mysql' )
				->default( '{}' );
		} );

		\Aimeos\Upscheme\Schema\Table::macro( 'type', function( string $name = 'type' ) use ( $codelen ) {
			return $this->string( $name, $codelen )
				->opt( 'charset', 'utf8mb4', 'mysql' )
				->opt( 'collation', 'utf8mb4_bin', 'mysql' )
				->default( '' );
		} );

		\Aimeos\Upscheme\Schema\Table::macro( 'refid', function( string $name = 'refid' ) {
			return $this->string( $name, 36 )
				->opt( 'charset', 'utf8mb4', 'mysql' )
				->opt( 'collation', 'utf8mb4_bin', 'mysql' )
				->default( '' );
		} );

		\Aimeos\Upscheme\Schema\Table::macro( 'meta', function() {
			$this->datetime( 'mtime' );
			$this->datetime( 'ctime' );
			return $this->string( 'editor' );
		} );
	}
}
