<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
*/

class Jobs
{
	private $_context;


	public function __construct( array $configPaths )
	{
		$this->_context = $this->_createContext( $configPaths );
	}


	/**
	 * Function executed by the scheduler.
	 *
	 * @return boolean True if success, false if not
	 */
	public function execute()
	{
		try
		{
			$count = $total = 0;

			$localeManager = MShop_Locale_Manager_Factory::createManager( $this->_context );
			$siteManager = $localeManager->getSubManager( 'site' );

			$criteria = $siteManager->createSearch();
			$criteria->setSlice( 0, 0 );
			$siteManager->searchItems( $criteria, array(), $total );

			while( $count < $total )
			{
				$criteria->setSlice( $count, 100 );
				$items = $siteManager->searchItems( $criteria );

				foreach( $items as $item )
				{
					$sites = MShop_Locale_Manager_Abstract::SITE_ONE;

					try {
						$localeItem = $localeManager->bootstrap( $item->getCode(), '', '', false, $sites );
						$this->_executeJobs( $localeItem );
					} catch ( Exception $e ) {
						$this->_context->getLogger()->log( 'Job scheduler: ' . $e->getMessage() );
					}
				}

				$count += count( $items );
			}
		}
		catch( Exception $e )
		{
			$this->_context->getLogger()->log( 'Job scheduler: ' . $e->getMessage() );
			return false;
		}

		return true;
	}


	/**
	 * Executes the jobs of one site.
	 *
	 * @param MShop_Locale_Item_Site_Interface $site Site item
	 */
	protected function _executeJobs( MShop_Locale_Item_Interface $locale )
	{
		$this->_context->setLocale( $locale );

		$count = $total = 0;
		$manager = MAdmin_Job_Manager_Factory::createManager( $this->_context );
		$criteria = $manager->createSearch( true );
		$criteria->setSlice( 0, 0 );
		$manager->searchItems( $criteria, array(), $total );

		while( $count < $total )
		{
			$criteria->setSlice( $count, 100 );
			$items = $manager->searchItems( $criteria );

			foreach( $items as $item )
			{
				$this->_executeJob( $item );
				$manager->saveItem( $item );
			}

			$count += count( $items );
		}
	}


	/**
	 * Executes a single job.
	 *
	 * @param MAdmin_Job_Item_Interface $job Job item
	 */
	protected function _executeJob( MAdmin_Job_Item_Interface $job )
	{
		try
		{
			$parts = explode( '.', $job->getMethod() );
			if( count( $parts ) !== 2 ) {
				throw new Exception( sprintf( 'Invalid job method "%1$s"', $job->getMethod() ) );
			}

			if( preg_match( '/^[a-zA-Z0-9\_]+$/', $parts[0] ) !== 1 ) {
				throw new Exception( sprintf( 'Invalid controller name "%1$s"', $parts[0] ) );
			}

			$name = "Controller_ExtJS_{$parts[0]}_Default";
			$params = (object) $job->getParameter();
			$controller = new $name( $this->_context );

			if( ( $result = call_user_func_array( array( $controller, $parts[1] ), array( $params ) ) ) === false ) {
				throw new Exception( sprintf( 'Unable to execute "$1%s"', $job->getMethod() ) );
			}

			$job->setResult( $result );
			$job->setStatus( 0 );
		}
		catch( Exception $e )
		{
			$msg = 'Unable to execute job "%1$s": %2$s';
			$this->_context->getLogger()->log( sprintf( $msg, $job->getMethod(), $e->getMessage() ) );
			$job->setStatus( -1 );
		}
	}


	protected function _createContext( array $conf )
	{
		$context = new MShop_Context_Item_Default();

		$conf[] = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'local';

		$config = new MW_Config_Array( array(), $conf );
		$config = new MW_Config_Decorator_MemoryCache( $config );
		$context->setConfig( $config );

		$dbm = new MW_DB_Manager_PDO( $config );
		$context->setDatabaseManager( $dbm );

		$locale = MShop_Locale_Manager_Factory::createManager($context)->createItem();
		$context->setLocale( $locale );

		$logger = new MAdmin_Log_Manager_Default( $context );
		$context->setLogger( $logger );

		$context->setEditor( 'tests' );

		return $context;
	}
}