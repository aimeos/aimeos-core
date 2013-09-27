<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */



class Jobs
{
	private $_arcavias;
	private $_context;


	public function __construct( Arcavias $arcavias, array $configPaths )
	{
		$this->_arcavias = $arcavias;
		$this->_context = $this->_createContext( $configPaths );
	}


	/**
	 * Executes the given jobs.
	 *
	 * @param array $jobs List of jobs to execute, e.g. 'catalog/index/rebuild'
	 * @param array $sites List of site codes the jobs should use
	 * @throws Exception If something goes wrong
	 */
	public function execute( array $jobs, array $sites = array( 'default' ) )
	{
		$localeManager = MShop_Locale_Manager_Factory::createManager( $this->_context );

		foreach( $sites as $siteCode )
		{
			$localeItem = $localeManager->bootstrap( $siteCode, '', '', false );
			$localeItem->setLanguageId( null );
			$localeItem->setCurrencyId( null );

			$context = clone $this->_context;
			$context->setLocale( $localeItem );

			foreach( $jobs as $jobname )
			{
				$cntl = Controller_Jobs_Factory::createController( $context, $this->_arcavias, $jobname );
				$cntl->run();
			}
		}
	}


	/**
	 * Creates the context object.
	 *
	 * @param array $conf List of configuration paths
	 * @return MShop_Context_Item_Interface Context object
	 */
	protected function _createContext( array $conf )
	{
		$context = new MShop_Context_Item_Default();

		$config = new MW_Config_Array( array(), $conf );
		$config = new MW_Config_Decorator_Memory( $config );
		$context->setConfig( $config );

		$dbm = new MW_DB_Manager_PDO( $config );
		$context->setDatabaseManager( $dbm );

		$logger = new MAdmin_Log_Manager_Default( $context );
		$context->setLogger( $logger );

		$mail = new MW_Mail_Zend( new Zend_Mail( 'UTF-8' ) );
		$context->setMail( $mail );

		$i18nPaths = $this->_arcavias->getI18nPaths();
		$i18n = new MW_Translation_Zend( $i18nPaths, 'gettext', 'en', array( 'disableNotices' => true ) );
		$i18n = new MW_Translation_Decorator_Memory( $i18n );
		$context->setI18n( array( 'en' => $i18n ) );

		$context->setView( $this->_createView( $config ) );

		$context->setEditor( 'tests' );

		return $context;
	}


	/**
	 * Creates the view object for the HTML client.
	 *
	 * @param MW_Config_Interface $config Config object
	 * @return MW_View_Interface View object
	 */
	protected function _createView( MW_Config_Interface $config )
	{
		$view = new MW_View_Default();

		$sepDec = $config->get( 'client/html/common/format/seperatorDecimal', '.' );
		$sep1000 = $config->get( 'client/html/common/format/seperator1000', ' ' );

		$helper = new MW_View_Helper_Number_Default( $view, $sepDec, $sep1000 );
		$view->addHelper( 'number', $helper );

		$helper = new MW_View_Helper_Config_Default( $view, $config );
		$view->addHelper( 'config', $helper );

		$helper = new MW_View_Helper_Url_None( $view );
		$view->addHelper( 'url', $helper );

		$helper = new MW_View_Helper_Encoder_Default( $view );
		$view->addHelper( 'encoder', $helper );

		return $view;
	}
}